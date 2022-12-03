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
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Print" icon="bx bx-printer" :onclick="$dom. '.event.print()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.create()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Create') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.print()" ><i class="bx bx-printer mr-50"></i>{{ __('Print') }}</a>
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
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&type=dc" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
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
                        'label' => 'document number',
                        'data' => 'document_number',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'document date',
                        'data' => 'date_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'dc',
                        'data' => 'plant_receiver',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic dc',
                        'data' => 'pic_receiver',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant sender',
                        'data' => 'plant_sender',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic sender',
                        'data' => 'pic_sender',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'note',
                        'data' => 'note',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/usedoil/uo-grtransfer/dtble?plant-id={{ $first_plant_id }}&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modaloutstanding" title="GR Transfer">
    <x-form-vertical>
        <div class="row">
            <div class="col-12">
                <x-row-vertical label="DC">
                    <x-select :dom="$dom" compid="dc_outstanding" type="serverside" url="master/plant/select?auth=true&type=dc" size="sm"/>
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

<x-modal :dom="$dom" compid="modalmanage" title="GR Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data GI')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="GI Number">
                    <input type="text" class="form-control form-control-sm" id="gi_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="GI Date">
                    <input type="text" class="form-control form-control-sm" id="gi_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="plant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="PIC Sender">
                    <input type="text" class="form-control form-control-sm" id="pic_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Data GR')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="GR Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="PIC Receiver">
                    <input type="text" class="form-control form-control-sm" id="pic_receiver{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note">
                    <textarea class="form-control form-control-sm" id="note{{$dom}}" rows="4"></textarea>
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>
    <div class="col-12">
        <x-divider-text :text="__('Material Used Oil')" class="mt-0" />
    </div>
    <div class="row mb-1">
        <div class="col-12">
            @php
                $columns =
                    [[
                        'label' => 'code',
                        'data' => 'material_code',
                    ],[
                        'label' => 'name',
                        'data' => 'material_name',
                    ],[
                        'label' => 'qty gi',
                        'data' => 'qty',
                    ],[
                        'label' => 'Qty GR',
                        'data' => 'qty_input',
                        'class' => 'input'
                    ],[
                        'label' => 'uom',
                        'data' => 'material_uom',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledetail" :columns="$columns" url="" compidmodal="modalmanage" footer="false" height="150" />
        </div>
    </div>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalstockcurrent" title="Stock Current" size="lg">
    <div class="row mb-2">
        <div class="col-12">
            @php
        $columns =
            [[
                'label' => 'plant',
                'data' => 'plant',
            ],[
                'label' => 'code',
                'data' => 'code',
            ],[
                'label' => 'name',
                'data' => 'name',
            ],[
                'label' => 'Stock Current',
                'data' => 'stock',
            ],[
                'label' => 'uom',
                'data' => 'uom',
            ]];
    @endphp
    <x-datatable-serverside :dom="$dom" compid="tablestockcurrent" :columns="$columns" url="" compidmodal="modalstockcurrent" footer="false" height="170" />
        </div>
    </div>
</x-modal>
<!-- end modal -->

<script>
$('#select2dc_outstanding{{$dom}}').on('select2:select', function (e) {
    {{$dom}}.func.setOutstanding();
});

{{$dom}} = {
   data: {
        id: 0,
        plant_id_receiver: 0
    },
    url: {
        save: "inventory/usedoil/uo-grtransfer",
        datatable: "inventory/usedoil/uo-grtransfer/dtble",
        datatableOutstandingItem: "inventory/usedoil/uo-grtransfer/outstanding/item/",
        urlDtble: "inventory/usedoil/uo-material/dtble/qty/uogrtransfer"
    },
    event: {
        create: function () {
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
        print: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.is_reverse != '0'){
                message.info(" {{ __('This transaction has been canceled.') }} ");
                return false;
            }

            window.open('inventory/usedoil/uo-grtransfer/print/' + data.id);
        }
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&from=' + pickerdateffrom{{$dom}}.get('select', 'yyyy/mm/dd') + '&until=' + pickerdatefuntil{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        setOutstanding: function () {
            fdtbletableoutstanding{{$dom}}.clear();
            dc_outstanding_id = $("#select2dc_outstanding{{$dom}}").val();
            if(dc_outstanding_id != '' && dc_outstanding_id != null){
                loadingModal('start');
                $.get( 'inventory/usedoil/uo-grtransfer/outstanding/' + dc_outstanding_id, function (res) {
                    console.log(res);
                    for (let i = 0; i < res.length; i++) {
                        fdtbletableoutstanding{{$dom}}.add([
                            '',
                            res[i].plant_sender,
                            res[i].document_number,
                            res[i].date_desc,
                            res[i].pic_sender,
                            res[i].id,
                            res[i].plant_id_receiver,
                        ]);
                    }
                    fdtbletableoutstanding{{$dom}}.refresh();
                    loadingModal('stop');
                });
            }
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            {{$dom}}.data.plant_id_receiver = 0;
            $("#document_number{{$dom}}").val('');
            $("#document_date{{$dom}}").val('');
            $("#pic_receiver{{$dom}}").val('');
            pickerdatedate{{$dom}}.set('select', '{{ date("Y-m-d") }}', { format: 'yyyy-mm-dd' });
        },
        set: function () {
            var row_data = fdtbletableoutstanding{{$dom}}.getSelectedData();
            data = row_data[0];

            pickerdatedate{{$dom}}.set('select', "{{ date('Y/m/d') }}", { format: 'yyyy-mm-dd' });

            $("#plant_sender{{$dom}}").val(data[1]);
            $("#gi_number{{$dom}}").val(data[2]);
            $("#gi_date{{$dom}}").val(data[3]);
            $("#pic_sender{{$dom}}").val(data[4]);
            fdtbletabledetail{{$dom}}.changeUrl({{$dom}}.url.datatableOutstandingItem + data[5]);

            {{$dom}}.data.plant_id_receiver = data[6];

            console.log(data);
            console.log({{$dom}}.data.plant_id_receiver);

            hideModal('modaloutstanding{{$dom}}');
            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            var datas = fdtbletabledetail{{$dom}}.getAllData();
            var material_code = [];
            var id = '';
            datas.map(function (data) {
                material_code.push(data.material_code);
                id = data.uo_movement_id;
            });

            return {
                'gr_date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'pic_receiver': $("#pic_receiver{{$dom}}").val(),
                'note': $("#note{{$dom}}").val(),
                'material_code': material_code,
                'qty': $("input[name='uogrtransfer[]']").map(function(){return $(this).val();}).get(),
                'id': id
            }
        },
        save: function () {
            hideErrors();

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save;

            var have = false;
            for (let i = 0; i < data.qty.length; i++) {
                if( data.qty[i] > 0 ){
                    have = true;
                    break;
                }
            }

            if( !have ){
                message.info('{{ __("Please input qty first") }}');
                return false
            }

            loadingModal('start');

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalmanage{{$dom}}');

                    // stock current show
                    fdtbletablestockcurrent{{$dom}}.changeUrl("inventory/usedoil/uo-stock/dtble/current/" + {{$dom}}.data.plant_id_receiver);
                    showModal('modalstockcurrent{{$dom}}');
                    // stock current show

                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        }
    }
}

</script>
