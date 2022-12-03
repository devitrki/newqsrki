@if( $configurations['sidebar_collapse'] != "close" )
<body class="vertical-layout vertical-menu-modern 2-columns navbar-sticky footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
@else
<body class="vertical-layout vertical-menu-modern 2-columns navbar-sticky footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
@endif

<!-- BEGIN: Header-->
<div class="header-navbar-shadow"></div>
<nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top ">
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="javascript:void(0)"><i class="ficon bx bx-menu"></i></a></li>
                    </ul>
                    <ul class="nav navbar-nav">
                        <li class="nav-item d-none d-md-block">
                            <div class="con-date-time" id="current-time-head"></div>
                        </li>
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-language nav-item">
                        <a class="dropdown-toggle nav-link" id="dropdown-flag" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="selected-language">{{ $user_companies['selected']->name }}</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                            @foreach($user_companies['options'] as $user_company)
                                <a class="dropdown-item" onclick="company.change({{ Auth::id() }}, {{ $user_company->id }})" href="javascript:void(0)">{{ $user_company->name }}</a>
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon bx bx-search"></i></a>
                        <div class="search-input">
                            <div class="search-input-icon"><i class="bx bx-search secondary"></i></div>
                            <input class="input" type="text" placeholder="{{ __('Search Menu') }}..." tabindex="-1" id="inputsearchmenu">
                            <div class="search-input-close"><i class="bx bx-x"></i></div>
                            <ul class="search-list"></ul>
                        </div>
                    </li>
                    @if( $configurations['fullscreen_status'] != "hide" )
                    <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon bx bx-fullscreen"></i></a></li>
                    @endif
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="javascript:void(0)" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none"><span class="user-name" id="pfullnamename">{{ $user->name }}</span><span class="user-status text-muted">{{__("Online")}}</span></div><span><img class="round" src="{{ asset( $configurations['logo_user'] ) }}" alt="avatar" height="40" width="40"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right pb-0">
                            <a class="dropdown-item" href="javascript:void(0)" onclick="login.showChangeProfile()"><i class="bx bx-user mr-50"></i> {{ __('Change Profile') }}</a>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="login.showChangePassword()"><i class="bx bx-lock-alt mr-50"></i> {{ __('Change Password') }}</a>
                            <div class="dropdown-divider mb-0"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off mr-50"></i> {{ __('Logout') }}</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- END: Header-->
