<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Receive" icon="bx bx-plus-circle" :onclick="$dom. '.event.create()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Print Receive Document" icon="bx bx-printer" :onclick="$dom. '.event.preview()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.create()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Receive') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-printer mr-50"></i>{{ __('Print Receive Document') }}</a>
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
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&ext=all" size="sm" dropdowncompid="tabledata" :default="[0, __('All') ]"/>
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
                $columns = [[
                                'label' => 'gr number',
                                'data' => 'gr_number',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'PO Number',
                                'data' => 'po_number',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Ref Number',
                                'data' => 'ref_number',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Posting Date',
                                'data' => 'posting_date_desc',
                                'orderable' => 'true',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'Plant',
                                'data' => 'plant_desc',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'vendor id',
                                'data' => 'vendor_id',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'vendor name',
                                'data' => 'vendor_name',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'material code',
                                'data' => 'material_code',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'material desc',
                                'data' => 'material_desc',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'qty po',
                                'data' => 'qty_po',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'qty remaining po',
                                'data' => 'qty_remaining_po',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'qty gr',
                                'data' => 'qty_gr',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'qty remaining',
                                'data' => 'qty_remaining',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'batch',
                                'data' => 'batch',
                                'orderable' => 'false',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'uom',
                                'data' => 'uom',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'recepient',
                                'data' => 'recepient',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/grvendor/dtble?plant-id=0&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" :order="[4, 'desc']"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modaloutstanding" title="GR PO Vendor" size="full">
    <x-form-vertical>
        <div class="row">
            <div class="col-12">
                <x-row-vertical label="Plant">
                    <x-select :dom="$dom" compid="plant_outstanding" type="serverside" url="master/plant/select?auth=true" size="sm"/>
                </x-row-vertical>
            </div>
            <div class="col-12">
                <label>{{ __('Outstanding Document GR PO Vendor') }}</label>
            </div>
            <div class="col-12">
                <x-tools class="border">
                    <x-slot name="left">
                        <x-row-tools>
                            <x-button-tools tooltip="GR Document" icon="bx bx-package" :onclick="$dom. '.event.grDocument()'" />
                        </x-row-tools>
                    </x-slot>
                    <x-slot name="right">
                        <x-row-tools>
                            <x-input-search :dom="$dom" dtblecompid="tableoutstanding" />
                        </x-row-tools>
                    </x-slot>
                </x-tools>
                @php
                    $columns = [[
                                    'label' => 'PO Number',
                                ],
                                [
                                    'label' => 'PO Date',
                                ],
                                [
                                    'label' => 'Vendor ID',
                                ],
                                [
                                    'label' => 'Vendor Name',
                                ],
                                [
                                    'label' => 'Item',
                                ],
                                [
                                    'label' => 'Material Code',
                                ],
                                [
                                    'label' => 'Material Name',
                                ],
                                [
                                    'label' => 'QTY PO',
                                ],
                                [
                                    'label' => 'QTY Remaining PO',
                                ],
                                [
                                    'label' => 'UOM',
                                ]];
                @endphp
                <x-datatable-source :dom="$dom" compid="tableoutstanding" :columns="$columns" compidmodal="modaloutstanding" footer="false" height="400" />
            </div>
        </div>
    </x-form-vertical>
</x-modal>

<x-modal :dom="$dom" compid="modalmanage" title="GR PO Vendor" size="lg" close="false">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('PO Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="PO Number">
                    <input type="text" class="form-control form-control-sm" id="po_number{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="PO Date">
                    <input type="text" class="form-control form-control-sm" id="po_date{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Vendor ID">
                    <input type="text" class="form-control form-control-sm" id="vendor_id{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Vendor Name">
                    <input type="text" class="form-control form-control-sm" id="vendor_name{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Item">
                    <input type="text" class="form-control form-control-sm" id="item_number{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Material Code">
                    <input type="text" class="form-control form-control-sm" id="material_code{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Material Name">
                    <input type="text" class="form-control form-control-sm" id="material_desc{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="QTY PO">
                    <input type="text" class="form-control form-control-sm" id="qty_po{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="QTY Remaining PO">
                    <input type="text" class="form-control form-control-sm" id="qty_remaining_po{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="uom{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('GR Data')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Posting Date">
                    <x-pickerdate :dom="$dom" compid="posting_date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Ref. Number">
                    <input type="text" class="form-control form-control-sm" id="ref_number{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Recepient">
                    <input type="text" class="form-control form-control-sm" id="recepient{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="QTY GR">
                    <input type="number" step=".001" class="form-control form-control-sm" id="qty_gr{{$dom}}" min="0.001" max="1000000">
                </x-row-horizontal>
                <x-row-horizontal label="Batch" desc="qty kg received">
                    <input type="number" step=".001" class="form-control form-control-sm" id="batch{{$dom}}" min="0.001" max="1000000">
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" onclick="{{$dom}}.func.closeGRVendorDetail()" id="btnCancel{{$dom}}">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()" id="btnSave{{$dom}}">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
$('#select2plant_outstanding{{$dom}}').on('select2:select', function (e) {
    {{$dom}}.func.setOutstanding();
});

{{$dom}} = {
   data: {
        id: 0,
        plant_id: '',
        mandt: '',
        elikz: '',
        gi_number: '',
        mat_code_batch: @json($mat_code_batch),
    },
    url: {
        save: "inventory/grvendor",
        datatable: "inventory/grvendor/dtble"
    },
    event: {
        create: function () {
            if ({{$dom}}.func.checkLock()) {
                message.info(" {{ __('GI / GR transactions are being locked by accounting') }} ");
                return false;
            }

            showModal('modaloutstanding{{$dom}}');
            setTimeout(function() {
                {{$dom}}.func.setOutstanding();
            }, 300);
        },
        grDocument: function () {
            var rows = fdtbletableoutstanding{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        editMaterial: function () {
            var rows = fdtbletablematerialgr{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.resetMaterialGr();
            {{$dom}}.func.setMaterialGr();
        },
        preview: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            window.open('inventory/grvendor/preview/' + data.id);
        },
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&from=' + pickerdateffrom{{$dom}}.get('select', 'yyyy/mm/dd') + '&until=' + pickerdatefuntil{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        checkLock: function () {
            var lock = true;
            if ('{{ $lock }}' == 'unlock') {
                lock = false;
            }
            return lock;
        },
        setOutstanding: function () {
            fdtbletableoutstanding{{$dom}}.clear();
            plant_outstanding_id = fslctplant_outstanding{{$dom}}.get();
            if(plant_outstanding_id != '' && plant_outstanding_id != null){
                loadingModal('start');
                $.get( 'inventory/grvendor/outstanding/' + plant_outstanding_id, function (res) {
                    for (let i = 0; i < res.length; i++) {
                        fdtbletableoutstanding{{$dom}}.add([
                            '',
                            res[i].doc_number,
                            res[i].po_date,
                            res[i].vendor_id,
                            res[i].vendor_name,
                            res[i].item_number,
                            res[i].material_code,
                            res[i].material_desc,
                            res[i].qty_po,
                            res[i].qty_remaining_po,
                            res[i].uom,
                            res[i].plant_id,
                            res[i].mandt,
                            res[i].elikz,
                            res[i].gi_number,
                        ]);
                    }
                    fdtbletableoutstanding{{$dom}}.refresh();
                    loadingModal('stop');
                });
            }
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#po_number{{$dom}}").val('');
            $("#po_date{{$dom}}").val('');
            $("#vendor_id{{$dom}}").val('');
            $("#vendor_name{{$dom}}").val('');
            $("#item_number{{$dom}}").val('');
            $("#material_code{{$dom}}").val('');
            $("#material_desc{{$dom}}").val('');
            $("#qty_po{{$dom}}").val('');
            $("#qty_remaining_po{{$dom}}").val('');
            $("#uom{{$dom}}").val('');
            $("#ref_number{{$dom}}").val('');
            $("#qty_gr{{$dom}}").val('');
            $("#recepient{{$dom}}").val('');
            $("#batch{{$dom}}").val('');
            $("#batch{{$dom}}").prop("disabled", true);
        },
        set: function () {
            var row_data = fdtbletableoutstanding{{$dom}}.getSelectedData();
            data = row_data[0];

            pickerdateposting_date{{$dom}}.set('select', "{{ date('Y/m/d') }}", { format: 'yyyy-mm-dd' });
            $("#po_number{{$dom}}").val(data[1]);
            $("#po_date{{$dom}}").val(data[2]);
            $("#vendor_id{{$dom}}").val(data[3]);
            $("#vendor_name{{$dom}}").val(data[4]);
            $("#item_number{{$dom}}").val(data[5]);
            $("#material_code{{$dom}}").val(data[6]);
            $("#material_desc{{$dom}}").val(data[7]);
            $("#qty_po{{$dom}}").val(data[8]);
            $("#qty_remaining_po{{$dom}}").val(data[9]);
            $("#uom{{$dom}}").val(data[10]);

            {{$dom}}.data.plant_id = data[11];
            {{$dom}}.data.mandt = data[12];
            {{$dom}}.data.elikz = data[13];
            {{$dom}}.data.gi_number = data[14];

            // check material code must input batch or not
            var indexChecked = {{$dom}}.data.mat_code_batch.indexOf(data[6]);

            if(indexChecked >= 0){
                // must input
                $("#batch{{$dom}}").val('');
                $("#batch{{$dom}}").prop("disabled", false);
            }

            loadingModal('stop');
            hideModal('modaloutstanding{{$dom}}');
            showModal('modalmanage{{$dom}}');
        },
        closeGRVendorDetail: function () {
            hideModal('modalmanage{{$dom}}');
            showModal('modaloutstanding{{$dom}}');
        },
        getDataForm: function () {
            return {
                'id' : {{$dom}}.data.id,
                'po_number': $("#po_number{{$dom}}").val(),
                'po_date': $("#po_date{{$dom}}").val(),
                'vendor_id': $("#vendor_id{{$dom}}").val(),
                'vendor_name': $("#vendor_name{{$dom}}").val(),
                'item_number': $("#item_number{{$dom}}").val(),
                'material_code': $("#material_code{{$dom}}").val(),
                'material_desc': $("#material_desc{{$dom}}").val(),
                'qty_po': $("#qty_po{{$dom}}").val(),
                'qty_remaining_po': $("#qty_remaining_po{{$dom}}").val(),
                'uom': $("#uom{{$dom}}").val(),
                'ref_number': $("#ref_number{{$dom}}").val(),
                'qty_gr': $("#qty_gr{{$dom}}").val(),
                'batch': $("#batch{{$dom}}").val(),
                'recepient': $("#recepient{{$dom}}").val(),

                'posting_date': pickerdateposting_date{{$dom}}.get('select', 'yyyy/mm/dd'),
                'plant_id': {{$dom}}.data.plant_id,
                'mandt': {{$dom}}.data.mandt,
                'elikz': {{$dom}}.data.elikz,
                'gi_number': {{$dom}}.data.gi_number,
            };
        },
        save: function () {
            hideErrors();

            // validate
            if( $("#qty_gr{{$dom}}").val() == '' || $("#qty_gr{{$dom}}").val() == null ){
                showError("{{ __('validation.required', ['attribute' => 'qty gr']) }}");
                return false;
            }
            var qtyGr = parseFloat($("#qty_gr{{$dom}}").val());
            var qtyRemainingPo = parseFloat($("#qty_remaining_po{{$dom}}").val());

            if (qtyGr < 0) {
                showError("{{ __('Qty GR cannot be less than 0') }}");
                return false;
            }

            if (qtyGr > qtyRemainingPo) {
                showError("{{ __('Qty GR cannot be more than qty remaining po') }}");
                return false;
            }

            // check material code must input batch or not
            var indexChecked = {{$dom}}.data.mat_code_batch.indexOf($("#material_code{{$dom}}").val());

            if(indexChecked >= 0){
                // must input
                if( $("#batch{{$dom}}").val() == '' || $("#batch{{$dom}}").val() == null || $("#batch{{$dom}}").val() == '-' ){
                    showError("{{ __('validation.required', ['attribute' => 'batch']) }}");
                    return false;
                }
            }
            // validate

            loadingModal('start');

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save;

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function (res) {
                    loadingModal("stop");
                    if( res.status == 'success' ){
                        fdtbletabledata{{$dom}}.refresh();
                        hideModal('modalmanage{{$dom}}');
                        message.success(res.message);
                    } else {
                        message.info(res.message);
                    }
                }
            });
        },
    }
}

</script>
