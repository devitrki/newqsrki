    <div class="sidenav-overlay"></div>
    {{-- <div class="drag-target"></div> --}}

    <!-- BEGIN: Footer-->
    <footer class="footer d-none d-sm-block">
        <p class="clearfix mb-0"><span class="float-left d-inline-block">{{ $configurations['year'] }} &copy; {{ $configurations['name'] }}</span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="bx bx-up-arrow-alt"></i></button>
        </p>
    </footer data-backdrop="static" data-keyboard="false">
    <!-- END: Footer-->

    <!-- BEGIN: Modal-->
    <x-modal :dom="$dom" compid="modalRelogin" title="Session Login Expired" close="false">
        <x-form-vertical>
            <x-row-vertical label="Password" desc="Login session expired. Please relogin to continue using this application.">
                <input type="password" id="lpassword" class="form-control form-control-sm">
            </x-row-vertical>
        </x-form-vertical>
        <x-slot name="footer">
            <button class="btn btn-light btn-sm" id="btnReloginExit">
                <span>{{ __('Exit') }}</span>
            </button>
            <button class="btn btn-secondary ml-1 btn-sm" id="btnRelogin">
                <span>{{ __('Relogin') }}</span>
            </button>
        </x-slot>
    </x-modal>

    <x-modal :dom="$dom" compid="modalChangePassword" title="Change Password" >
        <x-form-horizontal>
            <x-row-horizontal label="Old Password">
                <input type="password" class="form-control form-control-sm" id="loldpassword">
            </x-row-horizontal>
            <x-row-horizontal label="New Password">
                <input type="password" class="form-control form-control-sm" id="lnewpassword">
            </x-row-horizontal>
            <x-row-horizontal label="New Password Confirmation">
                <input type="password" class="form-control form-control-sm" id="lnewpasswordconfirm">
            </x-row-horizontal>
        </x-form-horizontal>
        <x-slot name="footer">
            <button class="btn btn-light btn-sm" data-dismiss="modal">
                <span>{{ __('Cancel') }}</span>
            </button>
            <button class="btn btn-secondary ml-1 btn-sm" id="btnChangePassword">
                <span>{{ __('Change Password') }}</span>
            </button>
        </x-slot>
    </x-modal>

    <x-modal :dom="$dom" compid="modalChangeProfile" title="Change Profile" >
        <x-form-horizontal>
            <x-row-horizontal label="Name">
                <input type="text" class="form-control form-control-sm" id="pName" value="{{ $user->name }}">
            </x-row-horizontal>
            <x-row-horizontal label="Phone">
                <input type="text" class="form-control form-control-sm" id="pPhone" value="{{ $user->phone }}">
            </x-row-horizontal>
        </x-form-horizontal>
        <x-slot name="footer">
            <button class="btn btn-light btn-sm" data-dismiss="modal">
                <span>{{ __('Cancel') }}</span>
            </button>
            <button class="btn btn-secondary ml-1 btn-sm" id="btnChangeProfile">
                <span>{{ __('Save') }}</span>
            </button>
        </x-slot>
    </x-modal>

    <x-modal :dom="$dom" compid="modalChangePasswordMonthly" title="Change Password Monthly" close="false">
        <p class="font-weight-bold mb-2">For reason security in using the qsr web, please change the password at least once a month</p>
        <x-form-horizontal>
            <x-row-horizontal label="Old Password">
                <input type="password" class="form-control form-control-sm" id="moldpassword">
            </x-row-horizontal>
            <x-row-horizontal label="New Password">
                <input type="password" class="form-control form-control-sm" id="mnewpassword">
            </x-row-horizontal>
            <x-row-horizontal label="New Password Confirmation">
                <input type="password" class="form-control form-control-sm" id="mnewpasswordconfirm">
            </x-row-horizontal>
        </x-form-horizontal>
        <x-slot name="footer">
            <button class="btn btn-secondary ml-1 btn-sm" id="btnChangePasswordMonthly">
                <span>{{ __('Change Password') }}</span>
            </button>
        </x-slot>
    </x-modal>

    <x-modal :dom="$dom" compid="modalBannerNotification" title="Notification System" close="false" >
        <div class="text-center mb-2">
            <h4 class="font-weight-bold text" id="notif-title"></h4>
            <hr>
            <p class="mb-2 text-justify" id="notif-content"></p>
            <hr>
            <button class="btn btn-secondary btn-sm" id="btnReadNotification">
                <span>{{ __('Yes, I already know') }}</span>
            </button>
        </div>
    </x-modal>
    <!-- END: Modal-->

    <!-- BEGIN: Script-->
    <script src="{{ asset('vendor/js/critical/critical.min.js') }}"></script>
    <script defer src="{{ asset('vendor/js/noncritical/noncritical.min.js') }}"></script>
    <script defer src="{{ asset('vendor/js/noncritical/jquery.inputmask.min.js') }}"></script>

    <script defer type="text/javascript">

        // inisialisasi variable global from server side
        var global = {
            url : "{{ url('') }}",
            dom : "{{ $dom }}",
            flagChangePassword: "{{ $user->flag_change_pass }}",
            token : $('meta[name="csrf-token"]').attr('content'),
            display : {
                heightMainContent : $('#main-content').height(),
                navbar: $('.header-navbar.main-header-navbar').height(),
                content_wrapper_height : 0,
                content_tabs_height : 0,
            },
            user : {
                email : '{{ $user->email }}',
            },
            message : {
                error : {
                    not_found : "{{ __('No results found.') }}",
                    failed : '{{ __("Failed") }}',
                    abort : '{{ __("Process Aborted : Its unnormal that process take so long to finish without any response. Please try again.") }}',
                    internal_server_error : '{{ __("Internal Server Error.") }}',
                },
                validation : {
                    password : '{{ __("validation.required", ["attribute" => "password"]) }}',
                    credential : '{{ __("validation.credential") }}',
                },
                info : '{{ __("Information") }}',
                warning : '{{ __("Warning") }}',
            },
            label : {
                loading : '{{ __("Loading") }}',
                process : '{{ __("process") }}',
                success : '{{ __("Success") }}',
                delete : '{{ __("delete") }}',
            },
            datatable : {
                lengthMenu: '{{ __("datatable.lengthmenu") }}',
                zeroRecords: '{{ __("datatable.zeroRecords") }}',
                info: '{{ __("datatable.info") }}',
                infoEmpty: '{{ __("datatable.infoEmpty") }}',
                infoFiltered: '{{ __("datatable.infoFiltered") }}',
                search: '{{ __("datatable.search") }}',
                loadingRecords: '{{ __("datatable.loading") }}...',
                selected: '{{ __("datatable.selected") }}...',
            }
        }
    </script>

    <script defer src="{{ asset('js/apps.js') }}"></script>
    <script defer src="{{ asset('vendor/js/critical/main.min.js') }}"></script>
    {{-- <script defer src="{{ asset('vendor/js/critical/apps.'.$configurations['version_js'].'.min.js') }}"></script> --}}
    <script defer src="{{ asset('vendor/js/critical/helper.'.$configurations['version_js'].'.min.js') }}"></script>
    <!-- BEGIN: Script-->
</body>
<!-- END: Body-->

</html>
