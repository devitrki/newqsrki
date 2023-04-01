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
                                @endcan
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
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
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Plant">
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Year">
                                        <x-select :dom="$dom" compid="fyear" type="array" :options="$years" size="sm" dropdowncompid="tabledata" :default="[date('Y'), date('Y')]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Month">
                                        <x-select :dom="$dom" compid="fmonth" type="array" :options="$months" size="sm" dropdowncompid="tabledata" :default="[$months[ date('n') - 1 ]['id'], $months[ date('n') - 1  ]['text']]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Material">
                                        <x-select :dom="$dom" compid="fmaterial" type="serverside" url="master/material-logbook/select?limit=10" size="sm" dropdowncompid="tabledata" autocomplete="true"/>
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
                        'label' => 'date',
                        'data' => 'date',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'date',
                    ],[
                        'label' => 'item',
                        'data' => 'item',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'no po',
                        'data' => 'no_po',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock initial',
                        'data' => 'stock_initial',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock in gr',
                        'data' => 'stock_in_gr',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock in tf',
                        'data' => 'stock_in_tf',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock out used',
                        'data' => 'stock_out_used',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock out waste',
                        'data' => 'stock_out_waste',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock out tf',
                        'data' => 'stock_out_tf',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock last',
                        'data' => 'stock_last',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'description',
                        'data' => 'description',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic',
                        'data' => 'pic',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/stock-card/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Stock Card" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" :default="[$first_plant_id, $first_plant_name]"/>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Material">
                    <x-select :dom="$dom" type="serverside" compid="material" url="master/material-logbook/select?limit=10" size="sm" autocomplete="true"/>
                </x-row-horizontal>
                <x-row-horizontal label="No PO">
                    <input type="text" class="form-control form-control-sm" id="no_po{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Stock Initial">
                    <input type="number" class="form-control form-control-sm" id="stock_initial{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Stock GR (IN)">
                    <input type="number" class="form-control form-control-sm" id="stock_gr_in{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Stock TF (IN)">
                    <input type="number" class="form-control form-control-sm" id="stock_tf_in{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Stock Used (OUT)">
                    <input type="number" class="form-control form-control-sm" id="stock_used_out{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Stock Waste (OUT)">
                    <input type="number" class="form-control form-control-sm" id="stock_waste_out{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Stock TF GI (OUT)">
                    <input type="number" class="form-control form-control-sm" id="stock_tf_gi_out{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Stock Last">
                    <input type="number" class="form-control form-control-sm" id="stock_last{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="pic{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Description">
                    <textarea id="description{{$dom}}" class="form-control form-control-sm" rows="5"></textarea>
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
{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "logbook/stock-card",
        datatable: "logbook/stock-card/dtble"
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
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&month=' + fslctfmonth{{$dom}}.get() + '&year=' + fslctfyear{{$dom}}.get() + '&material=' + fslctfmaterial{{$dom}}.get();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctmaterial{{$dom}}.clear();
            $("#no_po{{$dom}}").val('');
            $("#stock_initial{{$dom}}").val('0');
            $("#stock_gr_in{{$dom}}").val('0');
            $("#stock_tf_in{{$dom}}").val('0');
            $("#stock_used_out{{$dom}}").val('0');
            $("#stock_waste_out{{$dom}}").val('0');
            $("#stock_tf_gi_out{{$dom}}").val('0');
            $("#stock_last{{$dom}}").val('0');
            $("#description{{$dom}}").val('');
            $("#pic{{$dom}}").val('');

            $("#select2plant{{$dom}}").prop("disabled", false);
            $("#select2material{{$dom}}").prop("disabled", false);
            $("#pickerdatedate{{$dom}}").prop("disabled", false);
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctplant{{$dom}}.set(data.plant_id, data.plant);
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            fslctmaterial{{$dom}}.set(data.material_logbook_id, data.item);
            $("#no_po{{$dom}}").val(data.no_po);
            $("#stock_initial{{$dom}}").val(data.stock_initial);
            $("#stock_gr_in{{$dom}}").val(data.stock_in_gr);
            $("#stock_tf_in{{$dom}}").val(data.stock_in_tf);
            $("#stock_used_out{{$dom}}").val(data.stock_out_used);
            $("#stock_waste_out{{$dom}}").val(data.stock_out_waste);
            $("#stock_tf_gi_out{{$dom}}").val(data.stock_out_tf);
            $("#stock_last{{$dom}}").val(data.stock_last);
            $("#description{{$dom}}").val(data.description);
            $("#pic{{$dom}}").val(data.pic);

            $("#select2plant{{$dom}}").prop("disabled", true);
            $("#select2material{{$dom}}").prop("disabled", true);
            $("#pickerdatedate{{$dom}}").prop("disabled", true);

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'month': pickerdatedate{{$dom}}.get('select', 'm'),
                'year': pickerdatedate{{$dom}}.get('select', 'yyyy'),
                'plant': fslctplant{{$dom}}.get(),
                'material': fslctmaterial{{$dom}}.get(),
                'no_po': $("#no_po{{$dom}}").val(),
                'stock_initial': $("#stock_initial{{$dom}}").val(),
                'stock_gr_in': $("#stock_gr_in{{$dom}}").val(),
                'stock_tf_in': $("#stock_tf_in{{$dom}}").val(),
                'stock_used_out': $("#stock_used_out{{$dom}}").val(),
                'stock_waste_out': $("#stock_waste_out{{$dom}}").val(),
                'stock_tf_gi_out': $("#stock_tf_gi_out{{$dom}}").val(),
                'stock_last': $("#stock_last{{$dom}}").val(),
                'description': $("#description{{$dom}}").val(),
                'pic': $("#pic{{$dom}}").val(),
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

        }
    }
}

</script>
