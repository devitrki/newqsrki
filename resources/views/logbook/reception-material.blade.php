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
                                    <x-row-vertical label="From Date">
                                        <x-pickerdate :dom="$dom" compid="ffromdate" data-value="{{ date('Y/m/d') }}" clear="false" />
                                    </x-row-vertical>
                                    <x-row-vertical label="Until Date">
                                        <x-pickerdate :dom="$dom" compid="funtildate" data-value="{{ date('Y/m/d') }}" clear="false" />
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
                        'label' => 'product',
                        'data' => 'product',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'transport temperature',
                        'data' => 'transport_temperature',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'transport cleanliness',
                        'data' => 'transport_cleanliness',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'product temperature',
                        'data' => 'product_temperature',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'producer',
                        'data' => 'producer',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'country',
                        'data' => 'country',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'supplier',
                        'data' => 'supplier',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'logo halal',
                        'data' => 'logo_halal',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'product condition',
                        'data' => 'product_condition',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'production code',
                        'data' => 'production_code',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'qty',
                        'data' => 'product_qty',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'uom',
                        'data' => 'product_uom',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'expired date',
                        'data' => 'expired_date_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'status',
                        'data' => 'status',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic',
                        'data' => 'pic',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/reception-material/dtble?plant-id={{$first_plant_id}}&from-date={{ date('Y/m/d') }}&until-date={{ date('Y/m/d') }}" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Reception Material / Product" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" :default="[$first_plant_id, $first_plant_name]"/>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Product">
                    <input type="text" class="form-control form-control-sm" id="product{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Transport Temperature">
                    <input type="text" class="form-control form-control-sm" id="transport_temperature{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Transport Cleanliness">
                    <input type="text" class="form-control form-control-sm" id="transport_cleanliness{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Product Temperature">
                    <input type="text" class="form-control form-control-sm" id="product_temperature{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Producer">
                    <input type="text" class="form-control form-control-sm" id="producer{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Country">
                    <input type="text" class="form-control form-control-sm" id="country{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Supplier">
                    <input type="text" class="form-control form-control-sm" id="supplier{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Logo Halal">
                    <input type="text" class="form-control form-control-sm" id="logo_halal{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Product Condition">
                    <input type="text" class="form-control form-control-sm" id="product_condition{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Production Code">
                    <input type="text" class="form-control form-control-sm" id="production_code{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Product Qty">
                    <input type="number" class="form-control form-control-sm" id="product_qty{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Product UOM">
                    <input type="text" class="form-control form-control-sm" id="product_uom{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Expired Date">
                    <x-pickerdate :dom="$dom" compid="expired_date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Status">
                    <x-select :dom="$dom" compid="status" type="array" :options="$status" size="sm" :default="['0', 'Pass']"/>
                </x-row-horizontal>
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="pic{{$dom}}">
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
        save: "logbook/reception-material",
        datatable: "logbook/reception-material/dtble"
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
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&from-date=' + pickerdateffromdate{{$dom}}.get('select', 'yyyy/mm/dd') + '&until-date=' + pickerdatefuntildate{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;

            $("#product{{$dom}}").val('');
            $("#transport_temperature{{$dom}}").val('');
            $("#transport_cleanliness{{$dom}}").val('');
            $("#product_temperature{{$dom}}").val('');
            $("#producer{{$dom}}").val('');
            $("#country{{$dom}}").val('');
            $("#supplier{{$dom}}").val('');
            $("#logo_halal{{$dom}}").val('');
            $("#product_condition{{$dom}}").val('');
            $("#production_code{{$dom}}").val('');
            $("#product_qty{{$dom}}").val('');
            $("#product_uom{{$dom}}").val('');
            pickerdateexpired_date{{$dom}}.clear();
            fslctstatus{{$dom}}.set('Pass', 'Pass');
            $("#pic{{$dom}}").val('');

            $("#select2plant{{$dom}}").prop("disabled", false);
            $("#pickerdatedate{{$dom}}").prop("disabled", false);
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctplant{{$dom}}.set(data.plant_id, data.plant);
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            $("#product{{$dom}}").val(data.product);
            $("#transport_temperature{{$dom}}").val(data.transport_temperature);
            $("#transport_cleanliness{{$dom}}").val(data.transport_cleanliness);
            $("#product_temperature{{$dom}}").val(data.product_temperature);
            $("#producer{{$dom}}").val(data.producer);
            $("#country{{$dom}}").val(data.country);
            $("#supplier{{$dom}}").val(data.supplier);
            $("#logo_halal{{$dom}}").val(data.logo_halal);
            $("#product_condition{{$dom}}").val(data.product_condition);
            $("#production_code{{$dom}}").val(data.production_code);
            $("#product_qty{{$dom}}").val(data.product_qty);
            $("#product_uom{{$dom}}").val(data.product_uom);
            pickerdateexpired_date{{$dom}}.set('select', data.expired_date, { format: 'yyyy-mm-dd' });
            fslctstatus{{$dom}}.set(data.status, data.status);
            $("#pic{{$dom}}").val(data.pic);

            $("#select2plant{{$dom}}").prop("disabled", true);
            $("#select2material{{$dom}}").prop("disabled", true);
            $("#pickerdatedate{{$dom}}").prop("disabled", true);

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'plant': fslctplant{{$dom}}.get(),

                'product': $("#product{{$dom}}").val(),
                'transport_temperature': $("#transport_temperature{{$dom}}").val(),
                'transport_cleanliness': $("#transport_cleanliness{{$dom}}").val(),
                'product_temperature': $("#product_temperature{{$dom}}").val(),
                'producer': $("#producer{{$dom}}").val(),
                'country': $("#country{{$dom}}").val(),
                'supplier': $("#supplier{{$dom}}").val(),
                'logo_halal': $("#logo_halal{{$dom}}").val(),
                'product_condition': $("#product_condition{{$dom}}").val(),
                'production_code': $("#production_code{{$dom}}").val(),
                'product_qty': $("#product_qty{{$dom}}").val(),
                'product_uom': $("#product_uom{{$dom}}").val(),
                'expired_date': pickerdateexpired_date{{$dom}}.get('select', 'yyyy/mm/dd'),
                'status': fslctstatus{{$dom}}.get(),
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
