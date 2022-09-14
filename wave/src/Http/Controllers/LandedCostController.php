<?php

namespace Wave\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;
use Wave\User;
use Wave\KeyValue;
use Wave\ApiKey;
use TCG\Voyager\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection; 
use Yajra\Datatables\Datatables as dt;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Log;

class LandedCostController extends Controller
{
    public function index($section = ''){        
       
         $pageName = getPageName();
    	 return view('theme::landedcost.index',['pageName' => $pageName]);
    }
    
    public function getTransactions(Request $request)
    {         
        
         if ($request->ajax()) {  
            $securityKey    = auth()->user()->landedCostAPIKey->value('key');   
            $pageLength     = $request->input('length');
            
            $lcAPIpageStart = 0;
            $dtPageStart    = 0;
            
            if ($request->input('start') && intVal($request->input('start') >= 10)) {
                $dtPageStart        = intVal($request->input('start'));
                $lcAPIpageStart     = $dtPageStart/$pageLength; //the LC API uses pages not row numbers like the Datatables
            }
             
            
            $url = 'https://api.landedcost.io/calculator/findAllWithPagination/'.$securityKey.'/'.$lcAPIpageStart.'/'.$pageLength.'/desc'; 
            Log::debug($url);
            $lcData = Http::get($url);
            $lcData = json_decode($lcData); 
          //      Log::debug($lcData);
            $data = new Collection; 
            $i = $dtPageStart+1; 
            foreach($lcData as $_data){
                    $i++; 
                    $data->push([
                    'id'         => $i,
                    'utcDateTimeStamp'          => $_data->utcDateTimeStamp, 
                    'code'                      => $_data->code,
                    'dutiesTotal'               => $_data->dutiesTotal,
                    'taxesTotal'                => $_data->taxesTotal,
                    'feesTotal'                 => $_data->feesTotal,
                    'grandTotal'                => $_data->grandTotal,
                    '_id'                       => $_data->id
                ]);
                  //  Log::debug('COUNTINGXXXXX --' . $i);
                    
                }
            // Log::debug($data);
            return dt::of($data)
                ->setOffset($dtPageStart)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<button onclick="showModal(\''.$row['_id'].'\');" class="edit btn btn-primary btn-md">View</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->setTotalRecords($this->getTotalNumberofTransactionsforLCCAPI())
                ->make(true);
        }
    }
    
    
    public function getTotalNumberOfTransactions(Request $request) {
        
            if ($request->ajax()) { 
                return $defaultValue = $this->getTotalNumberofTransactionsforLCCAPI(); 
            }
    }
    
    private function getTotalNumberofTransactionsforLCCAPI() {
            
                $company_name       = auth()->User()->company_name;   
                $securityKey        = auth()->user()->landedCostAPIKey->value('key');    
                $uniqueIdentifer    = $company_name . '-' . $securityKey;
                $lcData = Http::get('https://api.landedcost.io/data/namevalue/find/'.$uniqueIdentifer."-LC-TotalCount");
                $lcData = json_decode($lcData);
                
                $defaultValue = 0;
                if ($lcData->value != "") {
                    $defaultValue = $lcData->value;
                }
                return $defaultValue;
        
    }
    
    
     public function getYearlyNumberOfTransactions(Request $request) {
        
            if ($request->ajax()) {
                $company_name       = auth()->User()->company_name;   
                $securityKey        = auth()->user()->landedCostAPIKey->value('key');    
                $uniqueIdentifer    = $company_name . '-' . $securityKey;
                $year               = gmdate("Y");
                $lcData = Http::get('https://api.landedcost.io/data/namevalue/find/'.$uniqueIdentifer."-LC-Year-".$year);
                $lcData = json_decode($lcData);
                
                
                
                $defaultValue = 0;
                if ($lcData->value != "") {
                    $defaultValue = $lcData->value;
                }
                return $defaultValue;
            }
    }

    public function getMonthlyNumberOfTransactions(Request $request) {
        
            if ($request->ajax()) {
                $company_name       = auth()->User()->company_name;   
                $securityKey        = auth()->user()->landedCostAPIKey->value('key');    
                $uniqueIdentifer    = $company_name . '-' . $securityKey;
                $year               = gmdate("Y");
                $month              = gmdate("m");
                $month              = ltrim($month, '0'); //remove leading zero  
                $lcData = Http::get('https://api.landedcost.io/data/namevalue/find/'.$uniqueIdentifer."-LC-YearMonth-".$year."-".$month);
                $lcData = json_decode($lcData);
                
                $defaultValue = 0;
                if ($lcData->value != "") {
                    $defaultValue = $lcData->value;
                }
                return $defaultValue;
            }
    }
    
      public function getDailyNumberOfTransactions(Request $request) {
        
            if ($request->ajax()) {
                $company_name       = auth()->User()->company_name;   
                $securityKey        = auth()->user()->landedCostAPIKey->value('key');    
                $uniqueIdentifer    = $company_name . '-' . $securityKey;
                $year               = gmdate("Y");
                $month              = gmdate("m");
                $month              = ltrim($month, '0'); //remove leading zero 
                $day                = gmdate("d");
                $day              = ltrim($day, '0'); //remove leading zero  
                $lcData = Http::get('https://api.landedcost.io/data/namevalue/find/'.$uniqueIdentifer."-LC-YearMonthDay-".$year."-".$month."-".$day);
                $lcData = json_decode($lcData);
                
                $defaultValue = 0;
                if ($lcData->value != "") {
                    $defaultValue = $lcData->value;
                }
                return $defaultValue;
            }
    }
    
    public function getCurrentMonthLCChart(Request $request) { 
           
          if ($request->ajax()) {
                $company_name       = auth()->User()->company_name;   
                $securityKey        = auth()->user()->landedCostAPIKey->value('key');    
                $uniqueIdentifer    = $company_name . '-' . $securityKey;
                
                $currentYear               = gmdate("Y");
                $currentMonth              = gmdate("m");
                $today                     = gmdate("d");
                $fullMonthName             = gmdate("F"); 
                $chartTtile                = $fullMonthName. ' ' .  $currentYear;
              
                $oStart = new \DateTime($currentYear .'-'. $currentMonth. '-1'); //always starts on the first
                $oEnd = clone $oStart;
                $oEnd->add(new \DateInterval("P1M"));
                
                $dates = array();
                $calls = array();
                
                /* we will need to remove the leading currentMonth and today */
                $currentMonthWithRemovedLeadingZero = ltrim($currentMonth, '0');
                
                
                while ($oStart->getTimestamp() < $oEnd->getTimestamp()) { 
                     $day           = $oStart->format('d');    
                     $oStart->add(new \DateInterval("P1D")); 
                     
                     $currentDayWithRemovedLeadingZero   = ltrim($day, '0');
                     
                     $currentYearMonthDate  = $currentYear.'-'.$currentMonthWithRemovedLeadingZero.'-'.$currentDayWithRemovedLeadingZero;
                     $dates[]               = $currentYearMonthDate;
                     
                     $lcData = Http::get('https://api.landedcost.io/data/namevalue/find/'.$uniqueIdentifer."-LC-YearMonthDay-".$currentYearMonthDate);
                     $lcData = json_decode($lcData);
                    
                    $defaultValue = 0;
                    if ($lcData->value != "") {
                        $defaultValue = $lcData->value;
                    }
                    $calls[]  = $defaultValue;  
                     
                     if ($today == $day ) {
                        break;
                     }
                }   
              
              return json_encode(array("dates"=>$dates,"calls"=>$calls,"title"=>$chartTtile));
          } 
    } 
    
     public function getCurrentYearLCChart(Request $request) { 
           
          if ($request->ajax()) {
                $company_name       = auth()->User()->company_name;   
                $securityKey        = auth()->user()->landedCostAPIKey->value('key');    
                $uniqueIdentifer    = $company_name . '-' . $securityKey;
                
                $currentYear               = gmdate("Y");
                $currentMonth              = gmdate("m");
                $today                     = gmdate("d");
                $chartTtile                = $currentYear;
                
                
                $months = array("01", "02", "04", "05", "06", "07", "08", "09", "10", "11", "12");

                foreach ($months as $month) {  
                      
                     $currentMonthWithRemovedLeadingZero = ltrim($month, '0');  
                     $currentYearMonth      = $currentYear.'-'.$currentMonthWithRemovedLeadingZero;
                     $dates[]               = $currentYearMonth;
                     
                     $lcData = Http::get('https://api.landedcost.io/data/namevalue/find/'.$uniqueIdentifer."-LC-YearMonth-".$currentYearMonth);
                     $lcData = json_decode($lcData);
                    
                    $defaultValue = 0;
                    if ($lcData->value != "") {
                        $defaultValue = $lcData->value;
                    }
                    $calls[]  = $defaultValue;  
                     
                     if ($currentMonth == $month ) {
                        break;
                    } 
                     
                } 
                
              
              return json_encode(array("dates"=>$dates,"calls"=>$calls,"title"=>$chartTtile));
          } 
    }
    
    
     public function getTransactionLookup(Request $request) { 
           
          if ($request->ajax()) {
                $company_name       = auth()->User()->company_name;   
                $securityKey        = auth()->user()->landedCostAPIKey->value('key');    
                $uniqueIdentifer    = $company_name . '-' . $securityKey;
                
                $_id = $request->input('_id');
                 Log::debug("value: ".$_id);
                
                //  https://api.landedcost.io/calculator/1bBvQLHHgsnkQUy0cq4KnUffzTc6PQJRyhdieH4Bw93xSxMQYOqBUgEyetQQFmJP/631ee24af979df7d76b2f7fd 
                $lcData = Http::get('https://api.landedcost.io/calculator/'.$securityKey.'/'. $_id);
                $lcData = json_decode($lcData);
                   
              return json_encode(array("transaction"=>$lcData));
          } 
    }
    
    
    
    
    public function profilePut(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . Auth::user()->id,
            'username' => 'sometimes|required|unique:users,username,' . Auth::user()->id
        ]);

    	$authed_user = auth()->user();

    	$authed_user->name = $request->name;
    	$authed_user->email = $request->email;
        if($request->avatar){
    	   $authed_user->avatar = $this->saveAvatar($request->avatar, $authed_user->username);
        }
    	$authed_user->save();

    	foreach(config('wave.profile_fields') as $key){
    		if(isset($request->{$key})){
	    		$type = $key . '_type__wave_keyvalue';
	    		if($request->{$type} == 'checkbox'){
	                if(!isset($request->{$key})){
	                    $request->request->add([$key => null]);
	                }
	            }

	            $row = (object)['field' => $key, 'type' => $request->{$type}, 'details' => ''];
	            $value = $this->getContentBasedOnType($request, 'themes', $row);

	    		if(!is_null($authed_user->keyValue($key))){
	    			$keyValue = KeyValue::where('keyvalue_id', '=', $authed_user->id)->where('keyvalue_type', '=', 'users')->where('key', '=', $key)->first();
	    			$keyValue->value = $value;
	    			$keyValue->type = $request->{$type};
	    			$keyValue->save();
	    		} else {
	    			KeyValue::create(['type' => $request->{$type}, 'keyvalue_id' => $authed_user->id, 'keyvalue_type' => 'users', 'key' => $key, 'value' => $value]);
	    		}
	    	}
    	}

    	return back()->with(['message' => 'Successfully updated user profile', 'message_type' => 'success']);
    }

    public function securityPut(Request $request){

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:'.config('wave.auth.min_password_length'),
        ]);

        if ($validator->fails()) {
            return back()->with(['message' => $validator->errors()->first(), 'message_type' => 'danger']);
        }

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return back()->with(['message' => 'Incorrect current password entered.', 'message_type' => 'danger']);
        }

        auth()->user()->forceFill([
            'password' => bcrypt($request->password)
        ])->save();

        return back()->with(['message' => 'Successfully updated your password.', 'message_type' => 'success']);
    }

    public function paymentPost(Request $request){
        $subscribed = auth()->user()->updateCard($request->paymentMethod);
    }

    public function apiPost(Request $request){
        $request->validate([
            'key_name' => 'required'
        ]);
    
        $apiKey = auth()->user()->createApiKey(str_slug($request->key_name));
        if(isset($apiKey->id)){
            return back()->with(['message' => 'Successfully created new API Key', 'message_type' => 'success']);
        } else {
            return back()->with(['message' => 'Error Creating API Key, please make sure you entered a valid name.', 'message_type' => 'danger']);
        }
    }

    public function apiPut(Request $request, $id = null){
        if(is_null($id)){
            $id = $request->id;
        }
        $apiKey = ApiKey::findOrFail($id);
        if($apiKey->user_id != auth()->user()->id){
            return back()->with(['message' => 'Canot update key name. Invalid User', 'message_type' => 'danger']);
        }
        $apiKey->name = str_slug($request->key_name);
        $apiKey->save();
        return back()->with(['message' => 'Successfully update API Key name.', 'message_type' => 'success']);
    }

    public function apiDelete(Request $request, $id = null){
        if(is_null($id)){
            $id = $request->id;
        }
        $apiKey = ApiKey::findOrFail($id);
        if($apiKey->user_id != auth()->user()->id){
            return back()->with(['message' => 'Canot delete Key. Invalid User', 'message_type' => 'danger']);
        }
        $apiKey->delete();
        return back()->with(['message' => 'Successfully Deleted API Key', 'message_type' => 'success']);
    }

    private function saveAvatar($avatar, $filename){
    	$path = 'avatars/' . $filename . '.png';
    	Storage::disk(config('voyager.storage.disk'))->put($path, file_get_contents($avatar));
    	return $path;
    }

    public function invoice(Request $request, $invoiceId) {
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor'  => setting('site.title', 'Wave'),
            'product' => ucfirst(auth()->user()->role->name) . ' Subscription Plan',
        ]);
    }
}
