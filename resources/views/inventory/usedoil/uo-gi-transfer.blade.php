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
                    @can('d'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Cancel" icon="bx bx-trash" :onclick="$dom. '.event.cancel()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="View" icon="bx bx-show-alt" :onclick="$dom. '.event.view()'" />
                    </x-row-tools>
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
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.cancel()" ><i class="bx bx-trash mr-50"></i>{{ __('Cancel') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.view()" ><i class="bx bx-show-alt mr-50"></i>{{ __('View') }}</a>
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
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
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
                        'label' => 'plant',
                        'data' => 'plant_sender',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'dc',
                        'data' => 'plant_receiver',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic',
                        'data' => 'pic_sender',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'note',
                        'data' => 'note',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'status gr',
                        'data' => 'status_gr',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'status reverse',
                        'data' => 'status_reverse',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/usedoil/uo-gitransfer/dtble?plant-id={{ $first_plant_id }}&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="GI Transfer" size="lg">
    <x-form-horizontal>
        <x-row-horizontal label="Plant">
            <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="DC">
            <x-select :dom="$dom" compid="dc" type="serverside" url="master/plant/select?auth=true&type=dc" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="PIC">
            <input type="text" class="form-control form-control-sm" id="pic{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Note">
            <input type="text" class="form-control form-control-sm" id="note{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>
    <div class="row mb-1">
        <div class="col-12 mb-1">
            <label>{{ __('Material Used Oil') }}</label>
        </div>
        <div class="col-12">
            @php
                $columns = [[
                            'label' => 'code',
                            'data' => 'code',
                        ],[
                            'label' => 'name',
                            'data' => 'name',
                        ],[
                            'label' => 'stock',
                            'data' => 'stock_desc',
                        ],[
                            'label' => 'Qty GI',
                            'data' => 'qty_input',
                            'class' => 'input'
                        ],[
                            'label' => 'uom',
                            'data' => 'uom',
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

<x-modal :dom="$dom" compid="modalcancel" title="Cancel GI Transfer">
    <x-form-horizontal>
        <x-row-horizontal label="PIC">
            <input type="text" class="form-control form-control-sm" id="pic_cancel{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Note">
            <input type="text" class="form-control form-control-sm" id="note_cancel{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.cancel()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalview" title="View GI Transfer" size="lg">
    <x-form-horizontal>
        <x-row-horizontal label="Plant">
            <x-select :dom="$dom" compid="vplant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="PIC">
            <input type="text" class="form-control form-control-sm" id="vpic{{$dom}}" readonly>
        </x-row-horizontal>
        <x-row-horizontal label="Note">
            <input type="text" class="form-control form-control-sm" id="vnote{{$dom}}" readonly>
        </x-row-horizontal>
    </x-form-horizontal>
    <div class="row mb-1">
        <div class="col-12 mb-1">
            <label>{{ __('Material Used Oil') }}</label>
        </div>
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
                'label' => 'Qty GI',
                'data' => 'qty'
            ],[
                'label' => 'uom',
                'data' => 'material_uom',
            ]];
    @endphp
    <x-datatable-serverside :dom="$dom" compid="tableview" :columns="$columns" url="" compidmodal="modalview" footer="false" height="150" />
        </div>
    </div>
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
                'label' => 'uom',
                'data' => 'uom',
            ],[
                'label' => 'Stock Current',
                'data' => 'stock',
            ]];
    @endphp
    <x-datatable-serverside :dom="$dom" compid="tablestockcurrent" :columns="$columns" url="" compidmodal="modalstockcurrent" footer="false" height="170" />
        </div>
    </div>
</x-modal>
<!-- end modal -->

<script>
$('#select2plant{{$dom}}').on('select2:select', function (e) {
    var plant = fslctplant{{$dom}}.get();

    fdtbletabledetail{{$dom}}.changeUrl({{$dom}}.url.urlDtble + plant);
});

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "inventory/usedoil/uo-gitransfer",
        datatable: "inventory/usedoil/uo-gitransfer/dtble",
        datatableView: "inventory/usedoil/uo-gitransfer/dtble/view/",
        urlDtble: "inventory/usedoil/uo-material/dtble/qty/uogitransfer/"
    },
    event: {
        create: function () {
            {{$dom}}.func.reset();

            fdtbletabledetail{{$dom}}.changeUrl({{$dom}}.url.urlDtble + '0');

            showModal('modalmanage{{$dom}}');
        },
        cancel: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.is_reverse != '0'){
                message.info(" {{ __('This transaction has been canceled.') }} ");
                return false;
            }

            if( data.gr_status != '0'){
                message.info(" {{ __('This transaction has been gr.') }} ");
                return false;
            }

            {{$dom}}.func.resetCancel();

            showModal('modalcancel{{$dom}}');
        },
        view: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.showView();
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

            window.open('inventory/usedoil/uo-gitransfer/print/' + data.id);
        }
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&from=' + pickerdateffrom{{$dom}}.get('select', 'yyyy/mm/dd') + '&until=' + pickerdatefuntil{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctplant{{$dom}}.clear();
            fslctdc{{$dom}}.clear();
            $("#pic{{$dom}}").val('');
            $("#note{{$dom}}").val('');
        },
        resetCancel: function () {
            {{$dom}}.data.id = 0;
            $("#pic_cancel{{$dom}}").val('');
            $("#note_cancel{{$dom}}").val('');
        },
        showView: function () {
            var row = fdtbletabledata{{$dom}}.getSelectedData();
            var data = row[0];
            fslctvplant{{$dom}}.set(data.plant_id_sender, data.plant_sender);
            $("#vpic{{$dom}}").val(data.pic_sender);
            $("#vnote{{$dom}}").val(data.note);

            $("#select2vplant{{$dom}}").prop("disabled", true);
            fdtbletableview{{$dom}}.changeUrl({{$dom}}.url.datatableView + data.id);

            showModal('modalview{{$dom}}');
        },
        getDataForm: function () {
            var datas = fdtbletabledetail{{$dom}}.getAllData();
            var material_id = [];
            datas.map(function (data) {
                material_id.push(data.id);
            });

            return {
                'plant': fslctplant{{$dom}}.get(),
                'dc': fslctdc{{$dom}}.get(),
                'pic': $("#pic{{$dom}}").val(),
                'note': $("#note{{$dom}}").val(),
                'material_id': material_id,
                'qty': $("input[name='uogitransfer[]']").map(function(){return $(this).val();}).get(),
                'id': {{$dom}}.data.id
            }
        },
        save: function () {
            hideErrors();

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save;
            if( {{$dom}}.data.id != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

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
                    fdtbletablestockcurrent{{$dom}}.changeUrl("inventory/usedoil/uo-stock/dtble/current/" + data.plant);
                    showModal('modalstockcurrent{{$dom}}');
                    // stock current show

                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        cancel: function () {
            hideErrors();

            var data = {
                'pic': $("#pic_cancel{{$dom}}").val(),
                'note': $("#note_cancel{{$dom}}").val(),
            };

            var row = fdtbletabledata{{$dom}}.getSelectedData();
            var url = {{$dom}}.url.save + '/cancel/' + row[0].id;

            loadingModal('start');

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalcancel{{$dom}}');

                    // stock current show
                    fdtbletablestockcurrent{{$dom}}.changeUrl("inventory/usedoil/uo-stock/dtble/current/" + row[0].plant_id_sender);
                    showModal('modalstockcurrent{{$dom}}');
                    // stock current show

                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');

        }
    }
}

</script>
