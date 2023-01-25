<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Transfer" icon="bx bx-plus-circle" :onclick="$dom. '.event.create()'" />
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
                        <x-button-tools tooltip="View" icon="bx bx-show-alt" :onclick="$dom. '.event.view()'" />
                    </x-row-tools>
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Upload to SAP" icon="bx bx-upload" :onclick="$dom. '.event.upload()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Delivery Orders" icon="bx bx-printer" :onclick="$dom. '.event.preview()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.create()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Transfer') }}</a>
                                @endcan
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                @endcan
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.view()" ><i class="bx bx-show-alt mr-50"></i>{{ __('View') }}</a>
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.upload()" ><i class="bx bx-upload mr-50"></i>{{ __('Upload to SAP') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-printer mr-50"></i>{{ __('Delivery Orders') }}</a>
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
                                'label' => 'doc. posto',
                                'data' => 'document_posto',
                                'orderable' => 'true',
                                'searchable' => 'true',
                            ],
                            [
                                'label' => 'date',
                                'data' => 'date',
                                'orderable' => 'true',
                                'searchable' => 'false',
                                'format' => 'date',
                            ],
                            [
                                'label' => 'issuing plant',
                                'data' => 'issuing_plant',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'issuer',
                                'data' => 'issuer',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'receiving plant',
                                'data' => 'receiving_plant',
                                'orderable' => 'false',
                                'searchable' => 'false',
                            ],
                            [
                                'label' => 'requester',
                                'data' => 'requester',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/giplant/dtble?plant-id=0&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" :order="[3, 'desc']"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Transfer GI Plant" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Issuer">
                    <input type="text" class="form-control form-control-sm" id="issuer{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Requester">
                    <input type="text" class="form-control form-control-sm" id="requester{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Issuing Plant">
                    <x-select :dom="$dom" compid="issuing_plant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Receiving Plant">
                    <x-select :dom="$dom" compid="receiving_plant" type="serverside" url="master/plant/select" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <label>{{ __('Material Transfer') }}</label>
                    </div>
                    <div class="col-12">
                        <div id="toolsmaterialgi{{$dom}}">
                            <x-tools class="border">
                                <x-slot name="left">
                                    <x-row-tools>
                                        <div style="width: 20rem">
                                            <x-select :dom="$dom" type="serverside" compid="material" url="master/material/select?limit=10" size="sm" autocomplete="true"/>
                                        </div>
                                    </x-row-tools>
                                    <x-row-tools>
                                        <x-button-tools tooltip="Delete Material" icon="bx bx-trash" :onclick="$dom. '.func.removeMaterial()'" />
                                    </x-row-tools>
                                </x-slot>
                            </x-tools>
                        </div>
                        @php
                            $columns =
                                [[
                                    'label' => 'material code',
                                    'data' => 'code',
                                ],[
                                    'label' => 'material name',
                                    'data' => 'description',
                                ],[
                                    'label' => 'Qty',
                                    'data' => 'qty_input',
                                    'class' => 'input'
                                ],[
                                    'label' => 'uom',
                                    'data' => 'uom',
                                ],[
                                    'label' => 'Note',
                                    'data' => 'note_input',
                                    'class' => 'input'
                                ]];

                            $className = [
                                [
                                    'class' => 'input',
                                    'target' => 3
                                ],
                                [
                                    'class' => 'input',
                                    'target' => 4
                                ],
                                [
                                    'class' => 'input',
                                    'target' => 5
                                ],
                            ]
                        @endphp
                        <x-datatable-source :dom="$dom" compid="tablematerialgi" :columns="$columns" url="" compidmodal="modalmanage" footer="false" height="285" :select="[true, 'single']" :className="$className" />
                    </div>
                </div>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal" id="btnCancel{{$dom}}">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()" id="btnSave{{$dom}}">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
$('#select2material{{$dom}}').on('select2:select', function (e) {
    var material = fslctmaterial{{$dom}}.get();

    // check key exist
    var dataExist = fdtbletablematerialgi{{$dom}}.getArrayData();
    funique = true;
    for (let i = 0; i < dataExist.length; i++) {
        if(dataExist[i][6] == material){
            funique = false;
            break;
        }
    }

    if (funique) {
        loadingModal("start");
        $.get( 'master/material/data/' + material, function (res) {

            var datas = fdtbletablematerialgi{{$dom}}.getAllData();
            var qty = $("input[name='qtygi[]']").map(function(){return $(this).val();}).get();
            var note = $("input[name='notegi[]']").map(function(){return $(this).val();}).get();
            var uom = $("select[name='uomgi[]']").map(function(){return $(this).val();}).get();

            fdtbletablematerialgi{{$dom}}.clear();

            for (let i = 0; i < qty.length; i++) {

                slct  = '<select name="uomgi[]" class="form-control form-control-sm">';
                for (let j = 0; j < datas[i][7].length; j++) {
                    if( uom[i] != datas[i][7][j].id){
                        slct += '<option value="' + datas[i][7][j].id + '">' + datas[i][7][j].text + '</option>';
                    } else {
                        slct += '<option value="' + datas[i][7][j].id + '" selected>' + datas[i][7][j].text + '</option>';
                    }

                }
                slct += '</select>';

                fdtbletablematerialgi{{$dom}}.add([
                    '',
                    datas[i][1],
                    datas[i][2],
                    '<input type="number" class="form-control form-control-sm" name="qtygi[]" value="' + qty[i] + '" style="min-width: 6rem;">',
                    slct,
                    '<input type="text" class="form-control form-control-sm" name="notegi[]" value="' + note[i] + '" style="min-width: 6rem;">',
                    datas[i][6],
                    datas[i][7],
                ]);

            }

            slct  = '<select name="uomgi[]" class="form-control form-control-sm">';
            for (let i = 0; i < res.alternative_uom.length; i++) {
                slct += '<option value="' + res.alternative_uom[i].id + '">' + res.alternative_uom[i].text + '</option>';
            }
            slct += '</select>';

            fdtbletablematerialgi{{$dom}}.add([
                '',
                res.material.code,
                res.material.description,
                '<input type="number" class="form-control form-control-sm" name="qtygi[]" value="0" style="min-width: 6rem;">',
                slct,
                '<input type="text" class="form-control form-control-sm" name="notegi[]" style="min-width: 6rem;">',
                res.material.id,
                res.alternative_uom
            ]);

            fdtbletablematerialgi{{$dom}}.refresh();

            loadingModal("stop");

        });
    }

    fslctmaterial{{$dom}}.clear();
});

{{$dom}} = {
   data: {
        id: 0,
        idMaterialTransfer: -1,
        materialTransfer: []
    },
    url: {
        save: "inventory/giplant",
        upload: "inventory/giplant/upload/sap/",
        preview: "inventory/giplant/preview/",
        datatable: "inventory/giplant/dtble"
    },
    event: {
        create: function () {
            if ({{$dom}}.func.checkLock()) {
                message.info(" {{ __('GI / GR transactions are being locked by accounting') }} ");
                return false;
            }

            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        },
        edit: function () {
            if ({{$dom}}.func.checkLock()) {
                message.info(" {{ __('GI / GR transactions are being locked by accounting') }} ");
                return false;
            }

            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check send sap or not
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if((data.document_number != null && data.document_number != '') || (data.document_posto != null && data.document_posto != '')){
                message.info(" {{ __('Data already have transaction number SAP, cannot be changed.') }} ");
                return false;
            }

            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        delete: function () {
            if ({{$dom}}.func.checkLock()) {
                message.info(" {{ __('GI / GR transactions are being locked by accounting') }} ");
                return false;
            }

            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check send sap or not
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if((data.document_number != null && data.document_number != '') || (data.document_posto != null && data.document_posto != '')){
                message.info(" {{ __('Data already have transaction number SAP, cannot be deleted.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.delete");

        },
        upload: function () {
            if ({{$dom}}.func.checkLock()) {
                message.info(" {{ __('GI / GR transactions are being locked by accounting') }} ");
                return false;
            }

            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check send sap or not
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if((data.document_number != null && data.document_number != '')){
                message.info(" {{ __('Data already have transaction number SAP, cannot be uploaded.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been uploaded cannot be changed.",
                            "{{$dom}}.func.upload");

        },
        view: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.reset();
            {{$dom}}.func.set();
            {{$dom}}.func.view();
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

            if(!data.document_number || !data.document_posto){
                message.info(" {{ __('This transaction is incomplete, cannot be previewed.') }} ");
                return false;
            }

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
        reset: function () {
            {{$dom}}.data.id = 0;
            pickerdatedate{{$dom}}.set('select', '{{ date("Y-m-d") }}', { format: 'yyyy-mm-dd' });
            $("#issuer{{$dom}}").val('');
            $("#requester{{$dom}}").val('');

            fslctissuing_plant{{$dom}}.clear();
            fslctreceiving_plant{{$dom}}.clear();

            $("#pickerdatedate{{$dom}}").prop("disabled", false);
            $("#issuer{{$dom}}").prop("disabled", false);
            $("#requester{{$dom}}").prop("disabled", false);
            $("#select2issuing_plant{{$dom}}").prop("disabled", false);
            $("#select2receiving_plant{{$dom}}").prop("disabled", false);
            $("#toolsmaterialgi{{$dom}}").css("display", "inherit");
            $("#btnCancel{{$dom}}").css("display", "inherit");
            $("#btnSave{{$dom}}").css("display", "inherit");

            fdtbletablematerialgi{{$dom}}.clear();
            fdtbletablematerialgi{{$dom}}.refresh();
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            {{$dom}}.data.id = data.id;
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            $("#issuer{{$dom}}").val(data.issuer);
            $("#requester{{$dom}}").val(data.requester);

            fslctissuing_plant{{$dom}}.set(data.issuing_plant_id, data.issuing_plant);
            fslctreceiving_plant{{$dom}}.set(data.receiving_plant_id, data.receiving_plant);

            $.get( 'inventory/giplant/item/' + data.id, function (res) {

                for (let i = 0; i < res.length; i++) {

                    slct  = '<select name="uomgi[]" class="form-control form-control-sm">';
                    for (let j = 0; j < res[i].alternative_uom.length; j++) {
                        if( res[i].uom != res[i].alternative_uom[j].id){
                            slct += '<option value="' + res[i].alternative_uom[j].id + '">' + res[i].alternative_uom[j].text + '</option>';
                        } else {
                            slct += '<option value="' + res[i].alternative_uom[j].id + '" selected>' + res[i].alternative_uom[j].text + '</option>';
                        }

                    }
                    slct += '</select>';

                    fdtbletablematerialgi{{$dom}}.add([
                        '',
                        res[i].code,
                        res[i].description,
                        '<input type="number" class="form-control form-control-sm" name="qtygi[]" value="' + res[i].qty + '" style="min-width: 6rem;">',
                        slct,
                        '<input type="text" class="form-control form-control-sm" name="notegi[]" value="' + res[i].note + '" style="min-width: 6rem;">',
                        res[i].material_id,
                        res[i].alternative_uom,
                    ]);
                }

                showModal('modalmanage{{$dom}}');
            });
        },
        view: function () {
            $("#pickerdatedate{{$dom}}").prop("disabled", true);
            $("#issuer{{$dom}}").prop("disabled", true);
            $("#requester{{$dom}}").prop("disabled", true);
            $("#select2issuing_plant{{$dom}}").prop("disabled", true);
            $("#select2receiving_plant{{$dom}}").prop("disabled", true);
            $("#toolsmaterialgi{{$dom}}").css("display", "none");
            $("#btnCancel{{$dom}}").css("display", "none");
            $("#btnSave{{$dom}}").css("display", "none");
        },
        getDataForm: function () {
            var datas = fdtbletablematerialgi{{$dom}}.getAllData();

            var material_id = [];
            datas.map(function (data) {
                material_id.push(data[6]);
            });

            return {
                'id' : {{$dom}}.data.id,
                'issuer': $("#issuer{{$dom}}").val(),
                'requester': $("#requester{{$dom}}").val(),
                'issuing_plant': $("#select2issuing_plant{{$dom}}").val(),
                'receiving_plant': $("#select2receiving_plant{{$dom}}").val(),
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'uom': JSON.stringify($("select[name='uomgi[]']").map(function(){return $(this).val();}).get()),
                'qty': JSON.stringify($("input[name='qtygi[]']").map(function(){return $(this).val();}).get()),
                'note': JSON.stringify($("input[name='notegi[]']").map(function(){return $(this).val();}).get()),
                'material_id': JSON.stringify(material_id),
            };
        },
        save: function () {
            hideErrors();

            var data = {{$dom}}.func.getDataForm();

            var url = {{$dom}}.url.save;

            if( {{$dom}}.data.id != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

            // check have material or not
            aQty = JSON.parse(data.qty);
            if(aQty.length <= 0){
                message.info('{{ __("Please choose material first") }}');
                return false
            }

            var valid = true;
            for (let i = 0; i < aQty.length; i++) {
                if( aQty[i] == '' || aQty[i] <= 0){
                    valid = false;
                    break;
                }
            }

            if( !valid ){
                message.info('{{ __("Please input qty more than zero") }}');
                return false
            }

            loadingModal('start');

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
        removeMaterial: function () {
            var rows = fdtbletablematerialgi{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            var index = fdtbletablematerialgi{{$dom}}.getRowIndex();
            var datas = fdtbletablematerialgi{{$dom}}.getAllData();
            var qty = $("input[name='qtygi[]']").map(function(){return $(this).val();}).get();
            var note = $("input[name='notegi[]']").map(function(){return $(this).val();}).get();
            var uom = $("select[name='uomgi[]']").map(function(){return $(this).val();}).get();
            fdtbletablematerialgi{{$dom}}.clear();

            for (let i = 0; i < qty.length; i++) {
                if( index != i ){
                    slct  = '<select name="uomgi[]" class="form-control form-control-sm">';
                    for (let j = 0; j < datas[i][7].length; j++) {
                        if( uom[i] != datas[i][7][j].id){
                            slct += '<option value="' + datas[i][7][j].id + '">' + datas[i][7][j].text + '</option>';
                        } else {
                            slct += '<option value="' + datas[i][7][j].id + '" selected>' + datas[i][7][j].text + '</option>';
                        }

                    }
                    slct += '</select>';

                    fdtbletablematerialgi{{$dom}}.add([
                        '',
                        datas[i][1],
                        datas[i][2],
                        '<input type="number" class="form-control form-control-sm" name="qtygi[]" value="' + qty[i] + '" style="min-width: 6rem;">',
                        slct,
                        '<input type="text" class="form-control form-control-sm" name="notegi[]" value="' + note[i] + '" style="min-width: 6rem;">',
                        datas[i][6],
                        datas[i][7],
                    ]);
                }
            }

            fdtbletablematerialgi{{$dom}}.refresh();
        },
        upload: function () {
            loading('start', '{{ __("Upload to SAP") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.upload + row[0].id;

            $.ajax({
                url: url,
                type: 'GET',
                success: function (res) {
                    loading("stop");
                    fdtbletabledata{{$dom}}.refresh();
                    if (res.status == 'success') {
                        message.success(res.message);
                    } else {
                        message.info(res.message);
                    }
                },
                statusCode: {
                    0: function (data) {
                        loading('stop');
                        message.warning("{{ __('Connection to SAP Server is to long, try again in a few minutes') }}");
                    },
                    504: function (data) {
                        loading('stop');
                        message.warning("{{ __('Connection to SAP Server is to long, try again in a few minutes') }}");
                    }
                }
            });

        }
    }
}

</script>
