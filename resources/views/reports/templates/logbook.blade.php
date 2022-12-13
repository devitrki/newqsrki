<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="Back Office & Outlet Richeese Factory">
        <meta name="keywords" content="back office, apps rf, apps.richeesefactory.com, richeese factory, apps, web apps, web apps richeese factory">
        <meta name="author" content="richeese factory">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>QSR RKI</title>
        <link rel="apple-touch-icon" href="{{ asset( 'images/ico/favicon.png' ) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset( 'images/ico/favicon.ico' ) }}">
        <link rel="stylesheet" type="text/css" href="{{ asset( 'css/bootstrap.css' ) }}">
        <link rel="stylesheet" type="text/css" href="{{ asset( 'css/report.css' ) }}">
    </head>
    <body>
        <div class="row m-0 report-logbook">
            <div class="col-4 col-md-4 col-lg-3 border border-right-0 p-0">
                <img class="logo" src="{{ asset( 'images/logo/logo-header-rki.jpeg' ) }}" alt="Richeese Kuliner Indonesia">
            </div>
            <div class="col-8 col-md-8 col-lg-9 border p-0 title">
                <p>@yield('title')</p>
            </div>
            <div class="col-12">
                <div class="row head-item-row">
                    @yield('head_desc')
                </div>
            </div>

            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        @yield('table_header')
                    </thead>
                    <tbody>
                        @yield('table_body')
                    </tbody>
                </table>
            </div>
        </div>

    </body>
</html>
