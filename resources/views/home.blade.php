@include('includes.home.head', ['configurations' => $configurationwebs])

<!-- BEGIN: Body-->
@include('includes.home.body-head', ['configurations' => $configurationwebs, 'user' => $user, 'user_companies' => $user_companies ])


    <!-- BEGIN: Main Menu-->
    @include('includes.home.menu', ['label' => $configurationwebs['label'], 'user_mapping_menus' => $user_mapping_menus])
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    @include('includes.home.content')
    <!-- END: Content-->


@include('includes.home.footer', ['configurations' => $configurationwebs])

