<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="maximum-scale=5, width=device-width">
    <meta name="description" content="Back Office & Outlet Richeese Factory">
    <meta name="keywords" content="back office, apps rf, apps.richeesefactory.com, richeese factory, apps, web apps, web apps richeese factory">
    <meta name="author" content="richeese factory">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="apple-touch-icon" href="{{ asset( 'images/ico/favicon.png' ) }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset( 'images/ico/favicon.ico' ) }}">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600&display=swap&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600&display=swap&display=swap" media="print" onload="this.media='all'" />

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'css/bootstrap.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'css/bootstrap.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'css/components.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'css/components.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'css/pages/authentication.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'css/pages/authentication.css' ) }}"></noscript>
    @isset($configurations['version_css'])
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/style.'.$configurations['version_css'].'.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/style.'.$configurations['version_css'].'.min.css' ) }}"></noscript>
    @else
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/style.4.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/style.4.min.css' ) }}"></noscript>
    @endisset


</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 1-column  navbar-sticky footer-static bg-full-screen-image blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content" style="min-height: 100%;">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- END: Content-->


    <script src="{{ asset('vendors/js/jquery/jquery-3.5.1.min.js') }}"></script>

    @stack('scripts')

</body>
<!-- END: Body-->

</html>
