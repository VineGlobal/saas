<!DOCTYPE html> 
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="default">
<!-- BEGIN: Head -->
<head>
    <meta charset="utf-8">
    <link href="{{ asset('themes/tailwind/images/logo.svg') }}" rel="shortcut icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="url" content="{{ url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="LandedCost.io Admin Dashboard">
    <meta name="keywords" content="LandedCost.io Admin Dashboard">  
   

    @yield('head')
    
    <!-- BEGIN: CSS Assets  -->
        <link href="{{ asset('themes/' . $theme->folder . '/css/admin-app.css') }}" rel="stylesheet"> 
        <link href="{{ asset('themes/' . $theme->folder . '/css/wave-app.css') }}" rel="stylesheet">
        <link href="{{ asset('themes/' . $theme->folder . '/css/wave-app-custom.css') }}" rel="stylesheet">
        
    <!-- END: CSS Assets--> 

 <!-- BEGIN: JS Assets   <!-- END: JS Assets       --> 
    <script type="module" src="{{ asset('themes/' . $theme->folder . '/js/build-app.js') }}"></script>         
    
    <script src="{{ asset('themes/' . $theme->folder . '/js/app.js') }}"></script>  
 
     
</head>
 
 
<body class="main">
        <!-- BEGIN: Mobile Menu -->
        <div class="mobile-menu md:hidden"> 
          
        </div>
        <!-- END: Mobile Menu -->
        <div class="flex">
            <!-- BEGIN: Side Menu -->
            <nav class="side-nav">
            
               <a href="" class="intro-x flex items-center pl-5 pt-4">
                <img alt="Midone - HTML Admin Template" class="w-6" src="{{ asset('themes/tailwind/images/logo.svg') }}">
                <span class="hidden xl:block text-white text-lg ml-3">
                    LandedCost.io
                </span>
            </a>
            <div class="side-nav__devider my-6"></div> 
              <ul>
                            <li>
                            <a href="/dashboard" class="side-menu {{ $pageName == 'dashboard' ? 'side-menu--active' : '' }}">
                                <div class="side-menu__icon">
                                    <i data-lucide="home"></i>
                                </div>
                                <div class="side-menu__title">
                                    Dashboard 
                                </div>
                            </a>
                                                             
                            </li>
                            <li>
                            <a href="/lc-api" class="side-menu {{ $pageName == 'lc-api' ? 'side-menu--active' : '' }}">
                                <div class="side-menu__icon">
                                    <i data-lucide="box"></i>
                                </div>
                                <div class="side-menu__title">
                                    Landed Cost Calculations 
                                    </div>
                            </a>                         
                            </li>
                            
                            <li>
                            <a href="/hs-api" class="side-menu {{ $pageName == 'hs-api' ? 'side-menu--active' : '' }}">
                                <div class="side-menu__icon">
                                    <i data-lucide="box"></i>
                                </div>
                                <div class="side-menu__title">
                                    HS Classifications 
                                </div>
                            </a>                         
                            </li> 
                            
                            <li>
                            <a href="/settings" class="side-menu {{ $pageName == 'settings' ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon">
                                    <i data-lucide="box"></i>
                                </div>
                                <div class="side-menu__title">
                                   Settings 
                                </div>
                            </a>                         
                            </li>          
                </ul>  
              
            </nav>
            <!-- END: Side Menu -->
            


            <!-- BEGIN: Content -->
            <div class="content">

@include('theme::partials.header')
                
               @yield('content')
            </div>
            <!-- END: Content -->
        </div>
  
  <!-- Full Screen Loader -->
    <div id="fullscreenLoader" class="fixed inset-0 top-0 left-0 z-50 flex flex-col items-center justify-center hidden w-full h-full bg-gray-900 opacity-50">
        <svg class="w-5 h-5 mr-3 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p id="fullscreenLoaderMessage" class="mt-4 text-sm font-medium text-white uppercase"></p>
    </div>
    <!-- End Full Loader -->      
        
  @include('theme::partials.toast')
    @if(session('message'))
        <script>setTimeout(function(){ popToast("{{ session('message_type') }}", "{{ session('message') }}"); }, 10);</script>
    @endif
    @waveCheckout
            
   <!-- <script defer src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.6.0/dist/alpine.min.js"></script>  
    <script defer  src="{{ asset('themes/' . $theme->folder . '/js/alpine-app.js') }}"></script>   
         -->
    </body>   
</html>
