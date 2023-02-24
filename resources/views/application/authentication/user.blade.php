<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.create()'" />
                    </x-row-tools>
                    @endcan
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.edit()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.create()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Create') }}</a>
                                @endcan
                                @can('e'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick=fdtbletabledata{{$dom}}.refresh()><i class="bx bx-revision mr-50"></i>{{ __('Refresh') }}</a>
                            </div>
                        </div>
                    </x-row-tools>
                </x-slot>
                <x-slot name="right">
                    <x-row-tools>
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Company">
                                        <x-select :dom="$dom" compid="fcompany" type="serverside" url="master/company/select?ext=all" size="sm" dropdowncompid="tabledata" :default="[0, __('All') ]" />
                                    </x-row-vertical>
                                </x-form-vertical>
                            </div>
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.filter()">
                                    <span>{{ __('Filter') }}</span>
                                </button>
                            </div>
                        </x-dropdown-filter>
                    </x-row-tools>
                    <x-row-tools>
                        <x-dropdown-export :dom="$dom" dtblecompid="tabledata" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-input-search :dom="$dom" dtblecompid="tabledata" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns = [[
                                'label' => 'Name',
                                'data' => 'profile_name',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Role',
                                'data' => 'role',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Authorize Role',
                                'data' => 'authorize_role',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Email',
                                'data' => 'email',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Company',
                                'data' => 'company_name',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Country',
                                'data' => 'country_name',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Department',
                                'data' => 'department_name',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Position',
                                'data' => 'position_name',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Work At',
                                'data' => 'work_at_desc',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Languange',
                                'data' => 'lang',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Status',
                                'data' => 'status',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Last Login',
                                'data' => 'last_login',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Created By',
                                'data' => 'created_by',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Created Date',
                                'data' => 'created_at',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="application/authentication/user/dtble?company=0" :select="[true, 'single']"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="User" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Profile')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Name">
                    <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Phone (addtional)">
                    <input type="text" class="form-control form-control-sm" id="phone{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Country">
                    <x-select :dom="$dom" compid="country" type="serverside" url="master/country/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Company">
                    <x-select :dom="$dom" compid="company" type="serverside" url="master/company/select?ext=all" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Work At">
                    @php
                        $options = [
                                    ['id' => 0, 'text' => 'Back Office'],
                                    ['id' => 1, 'text' => 'Outlet'],
                                    ['id' => 2, 'text' => 'DC']
                                ];
                    @endphp
                    <x-select :dom="$dom" compid="work_at" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Department">
                    <x-select :dom="$dom" compid="department" type="serverside" url="master/department/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Position">
                    <x-select :dom="$dom" compid="position" type="serverside" url="master/position/select" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Account')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Email">
                    <input type="text" class="form-control form-control-sm" id="email{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Passsword">
                    <input type="password" class="form-control form-control-sm" id="password{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Plant">
                    <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Area">
                    <x-select :dom="$dom" compid="area" type="serverside" url="master/area-plant/select" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Role">
                    <x-select :dom="$dom" compid="role" type="serverside" url="application/authentication/role/select?superadmin=true" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Language">
                    <x-select :dom="$dom" compid="language" type="serverside" url="master/language/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Regional">
                    <x-select :dom="$dom" compid="regional" type="serverside" url="master/regional-plant/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Status">
                    @php
                        $options = [
                                    ['id' => 2, 'text' => 'Active'],
                                    ['id' => 0, 'text' => 'Blocked'],
                                    ['id' => 1, 'text' => 'Unactive']
                                ];
                    @endphp
                    <x-select :dom="$dom" compid="status" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>

$('#select2role{{$dom}}').on('select2:select', function (e) {
    fslctplant{{$dom}}.clear();
    fslctarea{{$dom}}.clear();
    fslctregional{{$dom}}.clear();

    if ( fslctrole{{$dom}}.get() == '{{ $role_am }}') {
        $("#select2area{{$dom}}").prop("disabled", false);
        $("#select2regional{{$dom}}").prop("disabled", true);
        $("#select2plant{{$dom}}").prop("disabled", true);
    } else if ( fslctrole{{$dom}}.get() == '{{ $role_rm }}') {
        $("#select2area{{$dom}}").prop("disabled", true);
        $("#select2regional{{$dom}}").prop("disabled", false);
        $("#select2plant{{$dom}}").prop("disabled", true);
    } else if ( fslctrole{{$dom}}.get() == '{{ $role_sm }}' || fslctrole{{$dom}}.get() == '{{ $role_sc }}' ){
        $("#select2area{{$dom}}").prop("disabled", true);
        $("#select2regional{{$dom}}").prop("disabled", true);
        $("#select2plant{{$dom}}").prop("disabled", false);
    } else {
        $("#select2area{{$dom}}").prop("disabled", true);
        $("#select2regional{{$dom}}").prop("disabled", true);
        $("#select2plant{{$dom}}").prop("disabled", true);
    }
});

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "application/authentication/user",
        datatable: "application/authentication/user/dtble?company="
    },
    event: {
        create: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        },
        edit: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        refresh: function () {
            fdtbletabledata{{$dom}}.refresh();
        },
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable +  fslctfcompany{{$dom}}.get();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#name{{$dom}}").val('');
            $("#phone{{$dom}}").val('');
            fslctwork_at{{$dom}}.clear();
            fslctcountry{{$dom}}.clear();
            fslctcompany{{$dom}}.clear();
            fslctdepartment{{$dom}}.clear();
            fslctposition{{$dom}}.clear();
            fslctplant{{$dom}}.clear();
            fslctrole{{$dom}}.clear();
            fslctlanguage{{$dom}}.clear();
            fslctarea{{$dom}}.clear();
            fslctregional{{$dom}}.clear();
            $("#email{{$dom}}").val('');
            $("#password{{$dom}}").val('');
            fslctstatus{{$dom}}.clear();

            $("#select2area{{$dom}}").prop("disabled", true);
            $("#select2regional{{$dom}}").prop("disabled", true);
            $("#select2plant{{$dom}}").prop("disabled", true);
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#name{{$dom}}").val(data.profile_name);
            $("#phone{{$dom}}").val(data.phone);
            fslctwork_at{{$dom}}.set(data.work_at, data.work_at_desc);
            fslctcountry{{$dom}}.set(data.country_id, data.country_name);
            fslctcompany{{$dom}}.set(data.company_id, data.company_name);
            fslctdepartment{{$dom}}.set(data.department_id, data.department_name);
            fslctposition{{$dom}}.set(data.position_id, data.position_name);
            $("#email{{$dom}}").val(data.email);
            $("#password{{$dom}}").val(data.password);
            fslctrole{{$dom}}.set(data.role_id, data.role);
            fslctlanguage{{$dom}}.set(data.languange_id, data.lang);
            fslctstatus{{$dom}}.set(data.status_id, data.status);

            if ( data.role_id == '{{ $role_am }}') {

                $("#select2area{{$dom}}").prop("disabled", false);
                $("#select2regional{{$dom}}").prop("disabled", true);
                $("#select2plant{{$dom}}").prop("disabled", true);

                fslctarea{{$dom}}.set(data.area_plant_id, data.area_plant_name);

            } else if ( data.role_id == '{{ $role_rm }}') {

                $("#select2area{{$dom}}").prop("disabled", true);
                $("#select2regional{{$dom}}").prop("disabled", false);
                $("#select2plant{{$dom}}").prop("disabled", true);

                fslctregional{{$dom}}.set(data.regional_plant_id, data.regional_plant_name);

            } else if ( data.role_id == '{{ $role_sm }}' || data.role_id == '{{ $role_sc }}' ){

                $("#select2area{{$dom}}").prop("disabled", true);
                $("#select2regional{{$dom}}").prop("disabled", true);
                $("#select2plant{{$dom}}").prop("disabled", false);

                fslctplant{{$dom}}.set(data.plant_id, data.plant_name);

            } else {

                $("#select2area{{$dom}}").prop("disabled", true);
                $("#select2regional{{$dom}}").prop("disabled", true);
                $("#select2plant{{$dom}}").prop("disabled", true);
            }

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'id': {{$dom}}.data.id,
                'name': $("#name{{$dom}}").val(),
                'phone': $("#phone{{$dom}}").val(),
                'work_at': fslctwork_at{{$dom}}.get(),
                'country': fslctcountry{{$dom}}.get(),
                'company': fslctcompany{{$dom}}.get(),
                'department': fslctdepartment{{$dom}}.get(),
                'position': fslctposition{{$dom}}.get(),
                'email': $("#email{{$dom}}").val(),
                'password': $("#password{{$dom}}").val(),
                'plant': fslctplant{{$dom}}.get(),
                'area': fslctarea{{$dom}}.get(),
                'regional': fslctregional{{$dom}}.get(),
                'role': fslctrole{{$dom}}.get(),
                'language': fslctlanguage{{$dom}}.get(),
                'status': fslctstatus{{$dom}}.get(),
            }
        },
        save: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataForm();

            var url = {{$dom}}.url.save;
            if( {{$dom}}.data.id != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalmanage{{$dom}}');
                    message.success(res.message);
                } else if ( res.status == 'warning' ) {
                    showError(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        }
    }
}

</script>
