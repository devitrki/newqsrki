<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.edit()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Sync" icon="bx bx-sync" :onclick="$dom. '.event.sync()'" />
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
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.sync()" ><i class="bx bx-sync mr-50"></i>{{ __('Sync') }}</a>
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
                $columns = [[
                                'label' => 'Code',
                                'data' => 'code',
                            ],
                            [
                                'label' => 'Initial',
                                'data' => 'initital',
                            ],
                            [
                                'label' => 'Short Name',
                                'data' => 'short_name',
                            ],
                            [
                                'label' => 'Email',
                                'data' => 'email',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Phone',
                                'data' => 'phone',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Description',
                                'data' => 'description',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Address',
                                'data' => 'address',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'DC',
                                'data' => 'pdc_name',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Area Order',
                                'data' => 'area',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Regional Manager',
                                'data' => 'plant_rm',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Area Manager',
                                'data' => 'plant_am',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Store Manager',
                                'data' => 'plant_mod',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Type',
                                'data' => 'type',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Company',
                                'data' => 'company_name',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Cost Center',
                                'data' => 'cost_center',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Cost Center Desc',
                                'data' => 'cost_center_desc',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Customer Code',
                                'data' => 'customer_code',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Sloc ID gr',
                                'data' => 'sloc_id_gr',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Sloc ID GR Vendor',
                                'data' => 'sloc_id_gr_vendor',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Sloc ID Waste',
                                'data' => 'sloc_id_waste',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Sloc ID Asset Mutation',
                                'data' => 'sloc_id_asset_mutation',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Sloc ID Current Stock',
                                'data' => 'sloc_id_current_stock',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Pos',
                                'data' => 'pos_name',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Hours',
                                'data' => 'hours',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Drivethru',
                                'data' => 'drivethru_desc',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Price Category',
                                'data' => 'price_category',
                                'orderable' => 'false',
                            ]
                        ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="master/plant/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Plant" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Profile')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Short Name">
                    <input type="text" class="form-control form-control-sm" id="short_name{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Description">
                    <input type="text" class="form-control form-control-sm" id="description{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Code">
                    <input type="text" class="form-control form-control-sm" id="code{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Initial">
                    <input type="text" class="form-control form-control-sm" id="initial{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Type">
                    <input type="text" class="form-control form-control-sm" id="type{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Address">
                    <textarea class="form-control form-control-sm" id="address{{$dom}}" rows="2"></textarea>
                </x-row-horizontal>
                <x-row-horizontal label="Email">
                    <input type="email" class="form-control form-control-sm" id="email{{$dom}}"`>
                </x-row-horizontal>
                <x-row-horizontal label="Phone">
                    <input type="text" class="form-control form-control-sm" id="phone{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Configurations')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Cost Center">
                    <input type="text" class="form-control form-control-sm" id="cost_center{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Desc">
                    <input type="text" class="form-control form-control-sm" id="cost_center_desc{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="DC">
                    <x-select :dom="$dom" compid="dc" type="serverside" url="master/plant/select?type=dc" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Customer Code">
                    <input type="text" class="form-control form-control-sm" id="customer_code{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Area Order">
                    <x-select :dom="$dom" compid="area" type="serverside" url="master/area/select" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Sloc ID GR">
                    <input type="text" class="form-control form-control-sm" id="sloc_id_gr{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Sloc ID GR Vendor">
                    <input type="text" class="form-control form-control-sm" id="sloc_id_gr_vendor{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Sloc ID Waste">
                    <input type="text" class="form-control form-control-sm" id="sloc_id_waste{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Sloc ID Asset Mutation">
                    <input type="text" class="form-control form-control-sm" id="sloc_id_asset_mutation{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Sloc ID Current Stock">
                    <input type="text" class="form-control form-control-sm" id="sloc_id_current_stock{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('POS')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Hours">
                    <select class="form-control form-control-sm" id="hours{{$dom}}">
                        <option value="12">12 Hours</option>
                        <option value="24">24 Hours</option>
                    </select>
                </x-row-horizontal>
                <x-row-horizontal label="Drivethru">
                    <select class="form-control form-control-sm" id="drivethru{{$dom}}">
                        <option value="0">False</option>
                        <option value="1">True</option>
                    </select>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="POS">
                    <x-select :dom="$dom" compid="pos" type="serverside" url="master/pos/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Price Category">
                    <select class="form-control form-control-sm" id="price_category{{$dom}}">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
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
        save: "master/plant",
        sync: "master/plant/sync",
        datatable: "master/plant/dtble",
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
        sync: function () {
            message.confirm("Are you sure ?",
                            "Data will updated.",
                            "{{$dom}}.func.sync");
        }
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $('#short_name{{$dom}}').val(data.short_name);
            $('#description{{$dom}}').val(data.description);
            $('#code{{$dom}}').val(data.code);
            $('#initial{{$dom}}').val(data.initital);
            $('#type{{$dom}}').val(data.type);
            $('#address{{$dom}}').val(data.address);
            $('#email{{$dom}}').val(data.email);
            $('#phone{{$dom}}').val(data.phone);
            $('#cost_center{{$dom}}').val(data.cost_center);
            $('#cost_center_desc{{$dom}}').val(data.cost_center_desc);
            $('#customer_code{{$dom}}').val(data.customer_code);
            $('#hours{{$dom}}').val(data.hours);
            $('#drivethru{{$dom}}').val(data.drivethru);
            $('#price_category{{$dom}}').val(data.price_category);

            $('#sloc_id_gr{{$dom}}').val(data.sloc_id_gr);
            $('#sloc_id_gr_vendor{{$dom}}').val(data.sloc_id_gr_vendor);
            $('#sloc_id_waste{{$dom}}').val(data.sloc_id_waste);
            $('#sloc_id_asset_mutation{{$dom}}').val(data.sloc_id_asset_mutation);
            $('#sloc_id_current_stock{{$dom}}').val(data.sloc_id_current_stock);

            fslctdc{{$dom}}.set(data.dc_id, data.pdc_name);
            fslctarea{{$dom}}.set(data.area_id, data.area);
            fslctpos{{$dom}}.set(data.pos_id, data.pos_name);

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'id': {{$dom}}.data.id,
                'short_name': $('#short_name{{$dom}}').val(),
                'description': $('#description{{$dom}}').val(),
                'code': $('#code{{$dom}}').val(),
                'initial': $('#initial{{$dom}}').val(),
                'type': $('#type{{$dom}}').val(),
                'address': $('#address{{$dom}}').val(),
                'email': $('#email{{$dom}}').val(),
                'phone': $('#phone{{$dom}}').val(),
                'cost_center': $('#cost_center{{$dom}}').val(),
                'cost_center_desc': $('#cost_center_desc{{$dom}}').val(),
                'customer_code': $('#customer_code{{$dom}}').val(),
                'hours': $('#hours{{$dom}}').val(),
                'drivethru': $('#drivethru{{$dom}}').val(),
                'pos': fslctpos{{$dom}}.get(),
                'price_category': $('#price_category{{$dom}}').val(),
                'sloc_id_gr': $('#sloc_id_gr{{$dom}}').val(),
                'sloc_id_gr_vendor': $('#sloc_id_gr_vendor{{$dom}}').val(),
                'sloc_id_waste': $('#sloc_id_waste{{$dom}}').val(),
                'sloc_id_asset_mutation': $('#sloc_id_asset_mutation{{$dom}}').val(),
                'sloc_id_current_stock': $('#sloc_id_current_stock{{$dom}}').val(),
                'dc': fslctdc{{$dom}}.get(),
                'area': fslctarea{{$dom}}.get(),
            }
        },
        save: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataForm();

            var url = {{$dom}}.url.save;
            url += '/' + {{$dom}}.data.id;
            data._method = 'PUT';

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
        sync: function () {
            loading('start', '{{ __("Sync") }}', 'process');

            var url = {{$dom}}.url.sync;
            var data = {}

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        }
    }
}

</script>
