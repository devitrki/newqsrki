<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="javascript:void(0)">
                    <div class="brand-logo secondary" style="background: url('{{ asset($configurationwebs['logo']) }}') no-repeat 0px 0px;"></div>
                    <h2 class="brand-text mb-0 secondary">{{ $label }}</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 secondary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block secondary" data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
            @php
                \App\Models\Auth\Menu::getStructureMenuHTML($user_mapping_menus['root'], $user_mapping_menus['parent']);
            @endphp
        </ul>
    </div>
</div>
