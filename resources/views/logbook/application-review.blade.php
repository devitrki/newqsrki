<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Approve" icon="bx bx-check" :onclick="$dom. '.event.approve()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="UnApprove" icon="bx bx-x" :onclick="$dom. '.event.unApprove()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Preview" icon="bx bx-show-alt" :onclick="$dom. '.event.preview()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.approve()" ><i class="bx bx-check mr-50"></i>{{ __('Approve') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.unApprove()" ><i class="bx bx-x mr-50"></i>{{ __('UnApprove') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-show-alt mr-50"></i>{{ __('Preview') }}</a>
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
                                    <x-row-vertical label="Plant">
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="From">
                                        <x-pickerdate :dom="$dom" compid="ffrom" data-value="{{ date('Y/m/d', strtotime('-30 days')) }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Until">
                                        <x-pickerdate :dom="$dom" compid="funtil" data-value="{{ date('Y/m/d') }}" clear="false" />
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
                $columns =
                    [[
                        'label' => 'outlet',
                        'data' => 'outlet',
                    ],
                    [
                        'label' => 'date',
                        'data' => 'date_desc',
                    ],
                    [
                        'label' => 'PIC MOD',
                        'data' => 'mod_pic',
                    ],
                    [
                        'label' => 'approval MOD',
                        'data' => 'mod_approval_desc',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/application-review/dtble?plant-id={{$first_plant_id}}&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Application Review Logbook">
    <x-form-horizontal>
        <x-row-horizontal label="Outlet">
            <x-select :dom="$dom" compid="outlet" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Date">
            <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}"/>
        </x-row-horizontal>
        <x-row-horizontal label="PIC MOD">
            <input type="text" class="form-control form-control-sm" id="pic_mod{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()">
            <span>{{ __('Approve') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalpreview" title="Application Logbook Review" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-row-horizontal label="Plant">
                    <input type="text" class="form-control form-control-sm" id="pplant{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="pdate" data-value="{{ date('Y/m/d') }}" clear="false" disabled/>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <label>{{ __('Document Logbook') }}</label>
                    </div>
                    <div class="col-12">
                        <x-tools class="border">
                            <x-slot name="left">
                                <x-row-tools>
                                    <x-button-tools tooltip="View" icon="bx bxs-show" :onclick="$dom. '.event.view()'" />
                                </x-row-tools>
                            </x-slot>
                        </x-tools>
                        @php
                            $columns = [[
                                            'label' => 'document',
                                            'data' => 'document',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        // [
                                        //     'label' => 'last update by',
                                        //     'data' => 'last_update_by',
                                        //     'orderable' => 'false',
                                        //     'searchable' => 'false',
                                        // ],
                                        [
                                            'label' => 'last update',
                                            'data' => 'last_update',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ]];
                        @endphp
                        <x-datatable-serverside :dom="$dom" compid="tablepreview" :columns="$columns" compidmodal="modalpreview" url="logbook/application-review/preview/dtble?id=0" :select="[true, 'single']"  footer="false" height="300" />
                    </div>
                </div>
            </div>
        </div>
    </x-form-horizontal>
</x-modal>
<!-- end modal -->

<script>
{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "logbook/application-review",
        datatable: "logbook/application-review/dtble",
        preview: {
            datatable: "logbook/application-review/preview/dtble"
        }
    },
    event: {
        approve: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.mod_approval != '0' ){
                message.info(" {{ __('This data has been approved') }} ");
                return false;
            }

            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        unApprove: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.mod_approval != '1' ){
                message.info(" {{ __('This data not yet approved') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been unApproved.",
                            "{{$dom}}.func.unApprove");

        },
        preview: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            $("#pplant{{$dom}}").val(data.outlet);
            pickerdatepdate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });

            var url = {{$dom}}.url.preview.datatable + '?id=' + data.id;
            fdtbletablepreview{{$dom}}.changeUrl(url);

            showModal('modalpreview{{$dom}}');

        },
        view: function () {
            var rows = fdtbletablepreview{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletablepreview{{$dom}}.getSelectedData();
            data = row_data[0];

            window.open('/' + data.url_preview);
        }
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + $("#select2fplant{{$dom}}").val() + '&from=' + pickerdateffrom{{$dom}}.get('select', 'yyyy/mm/dd') + '&until=' + pickerdatefuntil{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctoutlet{{$dom}}.clear();
            pickerdatedate{{$dom}}.set('clear');
            $("#pic_mod{{$dom}}").val("");

            $("#select2outlet{{$dom}}").prop("disabled", false);
            $("#pickerdatedate{{$dom}}").prop("disabled", false);
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctoutlet{{$dom}}.set(data.plant_id, data.outlet);
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            $("#pic_mod{{$dom}}").val(data.mod_pic);

            $("#select2outlet{{$dom}}").prop("disabled", true);
            $("#pickerdatedate{{$dom}}").prop("disabled", true);

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'outlet': fslctoutlet{{$dom}}.get(),
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'pic_mod': $("#pic_mod{{$dom}}").val(),
                'id': {{$dom}}.data.id
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
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        approve: function () {
            loading('start', '{{ __("Approve") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/approve/' + row[0].id;

            $.get(url, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');
        },
        unApprove: function () {
            loading('start', '{{ __("UnApprove") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/unapprove/' + row[0].id;

            $.get(url, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');
        },
    }
}

</script>
