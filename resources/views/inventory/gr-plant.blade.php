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
                            <x-button-tools tooltip="BSTB" icon="bx bx-printer" :onclick="$dom. '.event.preview()'" />
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
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-printer mr-50"></i>{{ __('BSTB') }}</a>
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
                                'label' => 'doc. number',
                                'data' => 'document_number',
                                'orderable' => 'true',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'GI Number',
                                'data' => 'delivery_number',
                                'orderable' => 'true',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'PO Number',
                                'data' => 'posto_number',
                                'orderable' => 'true',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'Receive Date',
                                'data' => 'date_desc',
                                'orderable' => 'true',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'receiving plant',
                                'data' => 'receiving_plant',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'issuing plant',
                                'data' => 'issuing_plant',
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
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/grplant/dtble?plant-id=0&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" :order="[4, 'desc']"/>
        </div>
	</div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modaloutstanding" title="GR Plant">
	<x-form-vertical>
        <div class="row">
            <div class="col-12">
                <x-row-vertical label="Plant">
                    <x-select :dom="$dom" compid="plant_outstanding" type="serverside" url="master/plant/select?auth=true" size="sm"/>
                </x-row-vertical>
            </div>
            <div class="col-12">
                <label>{{ __('Outstanding Document GR') }}</label>
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
                                    'label' => 'Issuing Plant',
                                ],
                                [
                                    'label' => 'GI Number',
                                ],
                                [
                                    'label' => 'Document Date',
                                ]];
                @endphp
                <x-datatable-source :dom="$dom" compid="tableoutstanding" :columns="$columns" compidmodal="modaloutstanding" footer="false" height="300" />
            </div>
        </div>
	</x-form-vertical>
</x-modal>

<x-modal :dom="$dom" compid="modalmanage" title="GR Plant" size="lg">
	<x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Issuing Plant">
                    <input type="text" class="form-control form-control-sm" id="issuing_plant{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Document Number">
                    <input type="text" class="form-control form-control-sm" id="document_number{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Document Date">
                    <input type="text" class="form-control form-control-sm" id="document_date{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Receive Date">
                    <x-pickerdate :dom="$dom" compid="receive_date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Recepient">
                    <input type="text" class="form-control form-control-sm" id="recepient{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                            <label>{{ __('Material GR') }}</label>
                    </div>
                    <div class="col-12">
                        @php
                                $columns = [[
                                                'label' => 'Item',
                                            ],
                                            [
                                                'label' => 'Material',
                                            ],
                                            [
                                                'label' => 'Description',
                                            ],
                                            [
                                                'label' => 'QTY PO',
                                            ],
                                            [
                                                'label' => 'QTY Remaining PO',
                                            ],
                                            [
                                                'label' => 'QTY GR',
                                            ],
                                            [
                                                'label' => 'UOM',
                                            ]];

                                $className = [
                                                [
                                                    'class' => 'input',
                                                    'target' => 6
                                                ],
                                            ];
                        @endphp
                        <x-datatable-source :dom="$dom" compid="tablematerialgr" :columns="$columns" compidmodal="modalmanage" footer="false" height="300" :className="$className"/>
                    </div>
                </div>
            </div>
        </div>
	</x-form-horizontal>

	<x-slot name="footer">
        <button class="btn btn-light btn-sm" onclick="{{$dom}}.func.closeGRPlantDetail()" id="btnCancel{{$dom}}">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()" id="btnSave{{$dom}}">
            <span>{{ __('Save') }}</span>
        </button>
	</x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalmaterialgr" title="Material GR" close="false">
	<x-form-horizontal>
        <x-row-horizontal label="Material Code">
            <input type="text" class="form-control form-control-sm" id="material_code{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Material Description">
            <input type="text" class="form-control form-control-sm" id="material_desc{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Qty PO">
            <input type="number" class="form-control form-control-sm" id="qty_po{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Qty Remaining PO">
            <input type="number" class="form-control form-control-sm" id="qty_remaining{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="UOM">
            <input type="text" class="form-control form-control-sm" id="uom{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Qty GR">
            <input type="number" step=".001" class="form-control form-control-sm" id="qty_gr{{$dom}}" min="0.001" max="1000000">
        </x-row-horizontal>
	</x-form-horizontal>

	<x-slot name="footer">
        <button class="btn btn-light btn-sm" onclick="{{$dom}}.func.closeMaterialGr()">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveMaterialGr()">
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
        idMaterialGr: -1,
        materialID: 0,
        header: []
	},
	url: {
        save: "inventory/grplant",
        outstanding: "inventory/grplant/outstanding/",
        preview: "inventory/grplant/preview/",
        datatable: "inventory/grplant/dtble"
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

            // check send sap or not
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            window.open({{$dom}}.url.preview + data.id);
        }
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
            plant_outstanding_id = $("#select2plant_outstanding{{$dom}}").val();
            if(plant_outstanding_id != '' && plant_outstanding_id != null){
                loadingModal('start');
                $.get( 'inventory/grplant/outstanding/' + plant_outstanding_id, function (res) {
                    for (let i = 0; i < res.length; i++) {
                        fdtbletableoutstanding{{$dom}}.add([
                            '',
                            res[i].plant_from,
                            res[i].document_number,
                            res[i].mutation_date,
                            res[i].code_from,
                            res[i].code_to,
                        ]);
                    }
                    fdtbletableoutstanding{{$dom}}.refresh();
                    loadingModal('stop');
                });
            }
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#document_number{{$dom}}").val('');
            $("#document_date{{$dom}}").val('');
            $("#recepient{{$dom}}").val('');
            fdtbletablematerialgr{{$dom}}.clear();
            {{$dom}}.data.header = [];
        },
        set: function () {
            var row_data = fdtbletableoutstanding{{$dom}}.getSelectedData();
            data = row_data[0];

            pickerdatereceive_date{{$dom}}.set('select', "{{ date('Y/m/d') }}", { format: 'yyyy-mm-dd' });
            $("#issuing_plant{{$dom}}").val(data[1]);
            $("#document_number{{$dom}}").val(data[2]);
            $("#document_date{{$dom}}").val(data[3]);

            hideModal('modaloutstanding{{$dom}}');
            showModal('modalmanage{{$dom}}');

            setTimeout(function() {
                if(data[2] != '' && data[2] != null){
                    loadingModal('start');
                    $.get( 'inventory/grplant/outstanding/detail/' + data[4] + '/' + data[2], function (res) {
                        {{$dom}}.data.header = res['header'];
                        for (let i = 0; i < res.detail.length; i++) {
                            fdtbletablematerialgr{{$dom}}.add([
                                '',
                                res.detail[i].item_number,
                                res.detail[i].material_code,
                                res.detail[i].material_desc,
                                res.detail[i].qty_po,
                                res.detail[i].qty_remaining,
                                '<input type="number" class="form-control form-control-sm mul" name="qtygr[]" value="' + res.detail[i].qty_gr + '" style="min-width: 6rem;">',
                                res.detail[i].uom,
                                res.detail[i].material_id
                            ]);
                        }
                        fdtbletablematerialgr{{$dom}}.refresh();
                        loadingModal('stop');
                    });
                }
            }, 300);
        },
        getDataForm: function () {
            material_gr = fdtbletablematerialgr{{$dom}}.getAllData();
            return {
                'id' : {{$dom}}.data.id,
                'gi_number': $("#document_number{{$dom}}").val(),
                'gi_date': $("#document_date{{$dom}}").val(),
                'recepient': $("#recepient{{$dom}}").val(),
                'receive_date': pickerdatereceive_date{{$dom}}.get('select', 'yyyy/mm/dd'),
                'po_number': {{$dom}}.data.header.po_number,
                'plant_to': {{$dom}}.data.header.plant,
                'plant_from': {{$dom}}.data.header.plant_from,
                'text': {{$dom}}.data.header.text,
                'material_gr': (material_gr.length < 1) ? "" : JSON.stringify(material_gr),
                'qty': JSON.stringify($("input[name='qtygr[]']").map(function(){return $(this).val();}).get()),
            };
        },
        closeGRPlantDetail: function () {
            hideModal('modalmanage{{$dom}}');
            showModal('modaloutstanding{{$dom}}');
        },
        save: function () {
            hideErrors();

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save;

            // check have material or not
            aQty = JSON.parse(data.qty);
            if(aQty.length <= 0){
                message.info('{{ __("Please choose material first") }}');
                return false
            }

            $emptyAll = true;
            for (let i = 0; i < aQty.length; i++) {
                if( aQty[i] > 0){
                    $emptyAll = false;
                    break;
                }
            }

            if($emptyAll){
                showError('{{ __("Qty GR cannot be all empty") }}');
                return false;
            }

            // check qty gr cannot more than qty remaining po
            aMaterialGr = JSON.parse(data.material_gr);
            check = true;
            for (let i = 0; i < aMaterialGr.length; i++) {
                qtyRemainingPo = aMaterialGr[i][4];
                if( aQty[i] > qtyRemainingPo){
                    check = false;
                    break;
                }
            }

            if( !check ){
                showError('{{ __("Qty GR cannot more than qty remaining po") }}');
                return false;
            }

            loadingModal('start');

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
                },
                statusCode: {
                    422: function (data) {
                        loading('stop');
                        loadingModal('stop');
                        showErrors($.parseJSON(data.responseText));
                        fdtbletablematerialgr{{$dom}}.refresh();
                    },
                }
            });
        },
	}
}

</script>
