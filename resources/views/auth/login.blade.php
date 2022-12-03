@php
    $configweb = [];
    if( isset($configurationwebs) ){
        $configweb = $configurationwebs;
    }
@endphp

@extends('layouts.front', ['configurations' => $configweb])

@section('content')
<!-- login page start -->
<section id="auth-login" class="row flexbox-container ryellow back-m">
    <div class="col-xl-8 col-11">
        <div class="card bg-authentication mb-0">
            <div class="row m-0">
                <!-- left section-login -->
                <div class="col-md-6 d-md-block d-none text-center align-self-center p-3 rorange">
                    <div class="card-content">
                        <img class="img-fluid" src="{{ asset('images/pages/logo.jpg') }}" alt="{{ config('app.name') }} logo" >
                    </div>
                </div>
                <!-- right section image -->

                <div class="col-md-6 col-12 px-0">
                    <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                        <div class="card-header pb-1">
                            <div class="card-title">
                                <h3 class="text-center mb-2">{{ config('app.name') }}</h3>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form method="POST" action="{{ route('login') }}" novalidate>
                                    @csrf
                                    <div class="form-group mb-50">
                                        <label class="text-bold-600" for="email">{{__("Email address")}}</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" data-validation-required-message="This email cannot be empty" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="text-bold-600" for="password">{{__("Password")}}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required data-validation-required-message="This password cannot be empty" autocomplete="current-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                                    </div>
                                    <button type="submit" class="btn btn-secondary glow w-100 position-relative">{{__("Login")}}<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- login page ends -->

@endsection
