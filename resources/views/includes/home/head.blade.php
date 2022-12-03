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

    <link rel="preconnect" as="font" href="{{ asset( 'fonts/boxicons/fonts/boxicons.woff' ) }}" type="font/woff" crossorigin />
    <link rel="preload" as="font" href="{{ asset( 'fonts/boxicons/fonts/boxicons.woff' ) }}" type="font/woff" crossorigin="anonymous">
    <link rel="preconnect" as="font" href="{{ asset( 'fonts/boxicons/fonts/boxicons.woff2' ) }}" type="font/woff2" crossorigin />
    <link rel="preload" as="font" href="{{ asset( 'fonts/boxicons/fonts/boxicons.woff2' ) }}" type="font/woff2" crossorigin="anonymous">
    <link rel="preconnect" as="font" href="{{ asset( 'fonts/boxicons/fonts/boxicons.ttf' ) }}" type="font/ttf" crossorigin />
    <link rel="preload" as="font" href="{{ asset( 'fonts/boxicons/fonts/boxicons.ttf' ) }}" type="font/ttf" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700&display=swap&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700&display=swap&display=swap" media="print" onload="this.media='all'" />

    <link rel="stylesheet" type="text/css" href="{{ asset( 'vendor/css/critical/critical.min.css' ) }}">

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/boxicons.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/boxicons.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/perfect-scrollbar.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/perfect-scrollbar.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/flag-icon.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/flag-icon.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/pace.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/pace.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/datatables.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/datatables.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/sweetalert2.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/sweetalert2.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/toastr.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/toastr.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/pickadate.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/pickadate.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/select2.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/select2.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendors/css/editors/quill/katex.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendors/css/editors/quill/katex.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendors/css/editors/quill/quill.snow.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendors/css/editors/quill/quill.snow.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/colors.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/colors.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/vertical-menu.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/vertical-menu.min.css' ) }}"></noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset( 'vendor/css/noncritical/style.'.$configurations['version_css'].'.min.css' ) }}">
    <noscript><link rel="stylesheet" href="{{ asset( 'vendor/css/noncritical/style.'.$configurations['version_css'].'.min.css' ) }}"></noscript>

</head>
<!-- END: Head-->
