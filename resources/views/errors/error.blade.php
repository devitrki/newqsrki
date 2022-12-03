@php
    $configweb = [];
    if( isset($configurationwebs) ){
        $configweb = $configurationwebs;
    }
@endphp

@extends('layouts.front', ['configurations' => $configweb])
@section('content')
<section id="auth-login" class="row flexbox-container ryellow">
    <div class="col-xl-8 col-11">
        <div class="card bg-authentication mb-0">
            <div class="row m-0">
                <!-- left section-login -->
                <div class="col-md-6 d-md-block d-none text-center align-self-center p-3 rorange">
                    <div class="card-content">
                        <img class="img-fluid" src="{{ asset('images/pages/logo.jpg') }}" alt="richeese factory logo">
                    </div>
                </div>
                <!-- right section image -->
                
                <div class="col-md-6 col-12 px-0">
                    <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                        <div class="card-header pb-1">
                        </div>
                        <div class="card-content">
                            <div class="card-body text-center">
                                <h1 class="error-title mt-1">@yield('code', __('Oh no'))</h1>
                                <p class="p-2">
                                    @yield('message')
                                </p>
                                <a href="{{ app('router')->has('home') ? route('home') : url('/') }}" class="btn btn-primary round glow">{{ __('Go Home') }}</a>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection