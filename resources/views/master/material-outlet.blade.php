<style>
    .custom-switch .custom-control-label:before {
        background-color: red;
    }
</style>
<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.create()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Import Excel" icon="bx bx-export" :onclick="$dom. '.event.import()'" />
                    </x-row-tools>
                    @endcan
                    @can('d'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.delete()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.import()" ><i class="bx bx-export mr-50"></i>{{ __('Import Excel') }}</a>
                                @endcan
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick=fdtbletabledata{{$dom}}.refresh()><i class="bx bx-revision mr-50"></i>{{ __('Refresh') }}</a>
                            </div>
                        </div>
                    </x-row-tools>
                </x-slot>
                <x-slot name="right">
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
                        'label' => 'material code',
                        'data' => 'code',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'description',
                        'data' => 'description',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'opname',
                        'data' => 'opname_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'opname uom',
                        'data' => 'opname_uom',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'waste',
                        'data' => 'waste_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'waste uom',
                        'data' => 'waste_uom',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'waste flag',
                        'data' => 'waste_flag_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="master/material-outlet/dtble?company-id={{ $first_company_id }}" :select="[true, 'single']" :dblclick="true"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Material Outlet">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Material')" class="mt-0" />
            </div>
            <div class="col-12 mb-1">
                <x-select :dom="$dom" type="serverside" compid="material" url="master/material/select?limit=10" size="sm" autocomplete="true"/>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Opname')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Status">
                    <div class="custom-control custom-switch custom-switch-success">
                        <input type="checkbox" class="custom-control-input" id="status_opname{{$dom}}">
                        <label class="custom-control-label" for="status_opname{{$dom}}"></label>
                    </div>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="UOM">
                    <x-select :dom="$dom" type="serverside" compid="uom_opname" url="" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Waste')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-12 col-md-7">
                        <x-row-horizontal label="Status">
                            <div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input" id="status_waste{{$dom}}">
                                <label class="custom-control-label" for="status_waste{{$dom}}"></label>
                            </div>
                        </x-row-horizontal>
                    </div>
                    <div class="col-12 col-md-5">
                        <x-row-horizontal label="Flag">
                            <fieldset>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" checked="" id="flag_waste{{$dom}}">
                                    <label class="custom-control-label" for="flag_waste{{$dom}}">x</label>
                                </div>
                            </fieldset>
                        </x-row-horizontal>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="UOM">
                    <x-select :dom="$dom" type="serverside" compid="uom_waste" url="" size="sm"/>
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

<x-modal :dom="$dom" compid="modalimport" title="Material Outlet">
    <x-form-horizontal>
        <x-row-horizontal label="File Excel">
            <input type="file" class="form-control form-control-sm" id="fileexcel{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.import()">
            <span>{{ __('Import') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>

$('#select2material{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;

    $.get( 'master/material/uom/' + data.id, function (res) {

        // opname
        $('#select2uom_opname{{$dom}}').select2('destroy');
        fslctuom_opname{{$dom}}.initWithData(res);

        // waste
        $('#select2uom_waste{{$dom}}').select2('destroy');
        fslctuom_waste{{$dom}}.initWithData(res);

    });
});

// event double click comp datatable
function callbacktabledata{{$dom}}(data) {
    {{$dom}}.func.set(data);
}

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "master/material-outlet",
        datatable: "master/material-outlet/dtble",
    },
    event: {
        create: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        },
        delete: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.delete");

        },
        import: function () {
            showModal('modalimport{{$dom}}');
        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctmaterial{{$dom}}.clear();
            // opname
            $('#status_opname{{$dom}}').prop('checked', false);
            fslctuom_opname{{$dom}}.clear();
            // waste
            $('#status_waste{{$dom}}').prop('checked', false);
            $('#flag_waste{{$dom}}').prop('checked', false);
            fslctuom_waste{{$dom}}.clear();

            $("#select2material{{$dom}}").prop("disabled", false);
        },
        set: function (data) {
            {{$dom}}.data.id = data.id;

            $.get( 'master/material/uom/' + data.material_id, function (res) {
                $('#select2uom_opname{{$dom}}').select2('destroy');
                fslctuom_opname{{$dom}}.initWithData(res);

                $('#select2uom_waste{{$dom}}').select2('destroy');
                fslctuom_waste{{$dom}}.initWithData(res);

                // opname
                $('#status_opname{{$dom}}').prop('checked', data.opname);
                $("#select2uom_opname{{$dom}}").val(data.opname_uom).trigger('change');
                // waste
                $('#status_waste{{$dom}}').prop('checked', data.waste);
                $('#flag_waste{{$dom}}').prop('checked', data.waste_flag);
                $("#select2uom_waste{{$dom}}").val(data.waste_uom).trigger('change');

                fslctmaterial{{$dom}}.set(data.material_id, data.material_name);
                $("#select2material{{$dom}}").prop("disabled", true);

                showModal('modalmanage{{$dom}}');
            });

        },
        getDataForm: function () {
            return {
                'material': fslctmaterial{{$dom}}.get(),
                'status_opname': $('#status_opname{{$dom}}').is(':checked'),
                'uom_opname': fslctuom_opname{{$dom}}.get(),
                'status_waste': $('#status_waste{{$dom}}').is(':checked'),
                'uom_waste': fslctuom_waste{{$dom}}.get(),
                'flag_waste': $('#flag_waste{{$dom}}').is(':checked'),
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
                    message.info(res.message);
                }
            }, 'json');
        },
        delete: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.save + '/' + row[0].id;

            $.post(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');
        },
        import: function () {
            hideErrors();
            loadingModal('start');

            var url = {{$dom}}.url.save + '/import';

            var data = new FormData();
            var files = $('#fileexcel{{$dom}}')[0].files[0];
            var file = '';

            if( typeof files != 'undefined' ){
                file = files;
            }

            data.append('file_excel',file);

            $.ajax({
                url: url,
                type: 'post',
                data: data,
                contentType: false,
                processData: false,
                success: function(res){
                    loadingModal("stop");
                    if( res.status == 'success' ){
                        fdtbletabledata{{$dom}}.refresh();
                        hideModal('modalimport{{$dom}}');
                        message.success(res.message);
                    } else {
                        message.warning(res.message);
                    }
                },
            });
        },
    }
}

</script>
