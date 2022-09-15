<?php

namespace Wave\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Wave\Plan;
use Wave\User;
use Wave\PaddleSubscription;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{

    private $paddle_checkout_url;
    private $paddle_vendors_url;
    private $endpoint = 'https://vendors.paddle.com/api';

    private $vendor_id;
    private $vendor_auth_code;

    public function __construct(){
        $this->vendor_auth_code = config('wave.paddle.auth_code');
        $this->vendor_id = config('wave.paddle.vendor');

        $this->paddle_checkout_url = (config('wave.paddle.env') == 'sandbox') ? 'https://sandbox-checkout.paddle.com/api' : 'https://checkout.paddle.com/api';
        $this->paddle_vendors_url = (config('wave.paddle.env') == 'sandbox') ? 'https://sandbox-vendors.paddle.com/api' : 'https://vendors.paddle.com/api';
    }


    public function webhook(Request $request){

        // Which alert/event is this request for?
        $alert_name = $request->alert_name;
        $subscription_id = $request->subscription_id;
        $status = $request->status;


        // Respond appropriately to this request.
        switch($alert_name) {

            case 'subscription_created':
                Log::debug('***subscription_created');    
                break;
            case 'subscription_updated':
                Log::debug('***subscription_updated');  
                break;
            case 'subscription_cancelled':
                Log::debug('***subscription_cancelled');  
                $this->cancelSubscription($subscription_id);
                return response()->json(['status' => 1]);
                break;
            case 'subscription_payment_succeeded':
                 Log::debug('***subscription_payment_succeeded');      
                break;
            case 'subscription_payment_failed':
                 Log::debug('***subscription_payment_failed');     
                $this->cancelSubscription($subscription_id);
                return response()->json(['status' => 1]);
                break;
        }

    }

    public function cancel(Request $request){
        $this->cancelSubscription($request->id);
        return response()->json(['status' => 1]);
    }

    private function cancelSubscription($subscription_id){
        $subscription = PaddleSubscription::where('subscription_id', $subscription_id)->first();
        $subscription->status = 'cancelled';
        $subscription->save();
        $user = User::find( $subscription->user_id );
        $cancelledRole = Role::where('name', '=', 'cancelled')->first();
        $user->role_id = $cancelledRole->id;
        $user->save();
    }

    public function checkout(Request $request){

        //PaddleSubscriptions
        $response = Http::get($this->paddle_checkout_url . '/1.0/order?checkout_id=' . $request->checkout_id);
        $status = 0;
        $message = '';
        $guest = (auth()->guest()) ? 1 : 0;

          \Illuminate\Support\Facades\Log::info($response);

        if( $response->successful() ){
            
            $resBody = json_decode($response->body());
            
            Log::debug("--resBody Data--". print_r($resBody,true));

            if(isset($resBody->order)){
                $order = $resBody->order;

                $plans = Plan::all();

                  \Illuminate\Support\Facades\Log::info($plans);

                if($order->is_subscription && $plans->contains('plan_id', $order->product_id) ){

                    $subscriptionUser = Http::post($this->paddle_vendors_url . '/2.0/subscription/users', [
                        'vendor_id' => $this->vendor_id,
                        'vendor_auth_code' => $this->vendor_auth_code,
                        'subscription_id' => $order->subscription_id
                    ]);

                     \Illuminate\Support\Facades\Log::debug($subscriptionUser);
                     \Illuminate\Support\Facades\Log::info($subscriptionUser);
                     \Illuminate\Support\Facades\Log::error($subscriptionUser);


                    $subscriptionData = json_decode($subscriptionUser->body()); 
                     Log::debug("--subscriptionData--". print_r($subscriptionData,true)); 
                  
                    $subscription = $subscriptionData->response[0];

                     Log::debug("IS Guest? --". auth()->guest());   

                    if(auth()->guest()){

                        if(User::where('email', $subscription->user_email)->exists()){
                            Log::debug("Yes Exists");   
                            $user = User::where('email', $subscription->user_email)->first();
                        } else {
                            Log::debug("Creating New User...");   
                            // create a new user
                            $registration = new \Wave\Http\Controllers\Auth\RegisterController;

                            $user_data = [
                                'name' => '',
                                'email' => $subscription->user_email,
                                'password' => Hash::make(uniqid())
                            ];

                            $user = $registration->create($user_data);
                            Log::debug("After Reg Create...");  
                            Auth::login($user);
                            Log::debug("Aftering  Login...". $user);  
                        }

                    } else {
                        $user = auth()->user();

                         Log::debug("USER...". $user); 
                    }

                    $plan = Plan::where('plan_id', $subscription->plan_id)->first();

                    Log::debug("Plan -- ". $plan ); 

                    // add associated role to user
                    $user->role_id = $plan->role_id;
                    $user->save();

                    $subscription = PaddleSubscription::create([
                        'subscription_id' => $order->subscription_id,
                        'plan_id' => $order->product_id,
                        'user_id' => $user->id,
                        'status' => 'active', // https://paddle.com/docs/subscription-status-reference/
                        'next_bill_data' => \Carbon\Carbon::now()->addMonths(1)->toDateTimeString(),
                        'cancel_url' => $subscription->cancel_url,
                        'update_url' => $subscription->update_url
                    ]);

                    $status = 1;
                } else {

                    $message = 'Error locating that subscription product id. Please contact us if you think this is incorrect.';

                }
            } else {

                $message = 'Error locating that order. Please contact us if you think this is incorrect.';
            }

        } else {
            $message = $response->serverError();
        }

        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'guest' => $guest
                ]);
    }

    public function invoices(User $user){

        $invoices = [];

        if(isset($user->subscription->subscription_id)){
            $response = Http::post($this->paddle_vendors_url . '/2.0/subscription/payments', [
                'vendor_id' => $this->vendor_id,
                'vendor_auth_code' => $this->vendor_auth_code,
                'subscription_id' => $user->subscription->subscription_id,
                'is_paid' => 1
            ]);

            $invoices = json_decode($response->body());
        }

        return $invoices;

    }

    public function switchPlans(Request $request){
        $plan = Plan::where('plan_id', $request->plan_id)->first();

        if(isset($plan->id)){


            // Update the user plan with Paddle
            $response = Http::post($this->paddle_vendors_url . '/2.0/subscription/users/update', [
                'vendor_id' => $this->vendor_id,
                'vendor_auth_code' => $this->vendor_auth_code,
                'subscription_id' => auth()->user()->subscription->subscription_id,
                'plan_id' => $request->plan_id
            ]);

            // Next, update the user role associated with the updated plan
            auth()->user()->role_id = $plan->role_id;
            auth()->user()->save();

            if($response->successful()){
                return back()->with(['message' => 'Successfully switched to the ' . $plan->name . ' plan.', 'message_type' => 'success']);
            }

        }

        return back()->with(['message' => 'Sorry, there was an issue updating your plan.', 'message_type' => 'danger']);


    }

}
