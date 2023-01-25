<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @hasanyrole('store manager|superadmin')
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
                    @endhasanyrole
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Preview" icon="bx bx-show-alt" :onclick="$dom. '.event.preview()'" />
                    </x-row-tools>
                    @hasanyrole('co staff|co supervisor|superadmin')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Submit SAP" icon="bx bx-upload" :onclick="$dom. '.event.submit()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Dump Txt" icon="bx bx-download" :onclick="$dom. '.event.download()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Update Document Number" icon="bx bx-send" :onclick="$dom. '.event.updateDocNumber()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Open Lock" icon="bx bxs-lock-open-alt" :onclick="$dom. '.event.openLock()'" />
                    </x-row-tools>
                    @endhasanyrole
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @hasanyrole('store manager|superadmin')
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.create()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Create') }}</a>
                                @endcan
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                @endcan
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete') }}</a>
                                @endcan
                                @endhasanyrole
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-show-alt mr-50"></i>{{ __('Preview') }}</a>
                                @hasanyrole('co staff|co supervisor|superadmin')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.submit()" ><i class="bx bx-upload mr-50"></i>{{ __('Submit SAP') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.download()" ><i class="bx bx-download mr-50"></i>{{ __('Dump Txt') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.updateDocNumber()" ><i class="bx bx-send mr-50"></i>{{ __('Update Doc.Number') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.openLock()" ><i class="bx bxs-lock-open-alt mr-50"></i>{{ __('Open Lock') }}</a>
                                @endhasanyrole
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
                        'label' => 'submit sap',
                        'data' => 'submit_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'document number',
                        'data' => 'document_number_desc',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant',
                        'data' => 'plant',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'opname date',
                        'data' => 'date',
                        'searchable' => 'true',
                        'orderable' => 'false',
                        'format' => 'date',
                    ],[
                        'label' => 'pic',
                        'data' => 'pic',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'create date',
                        'data' => 'created_at',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'datetime',
                    ],[
                        'label' => 'pic update',
                        'data' => 'pic_update',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'update date',
                        'data' => 'update_date',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'datetime',
                    ],[
                        'label' => 'posting date',
                        'data' => 'posting_date',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'postingdate',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/opname/dtble?plant-id={{ $first_plant_id }}&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Opname" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="pic{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note / Additional Material">
                    <textarea class="form-control form-control-sm" id="note{{$dom}}" rows="9"></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Material')" class="mt-0" />
            </div>
            <div class="col-12">
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
                        ]];
                @endphp
                <x-datatable-serverside :dom="$dom" compid="tabledetail" :columns="$columns" url="" compidmodal="modalmanage" footer="false" height="285" />
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

<x-modal :dom="$dom" compid="modalupdate" title="Opname Update Document Number" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <x-select :dom="$dom" compid="uplant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="udate" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="upic{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note / Additional Material">
                    <textarea class="form-control form-control-sm" id="unote{{$dom}}" rows="9" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Update')" class="mt-0" />
            </div>
            <div class="col-12 col-md-8">
                <x-row-horizontal label="Document Number">
                    <input type="text" class="form-control form-control-sm" id="document_number{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-4">
                <x-row-horizontal label="Submit">
                    @php
                        $options = [
                                    ['id' => 1, 'text' => 'Yes'],
                                    ['id' => 0, 'text' => 'No'],
                                ];
                    @endphp
                    <x-select :dom="$dom" compid="submit" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>
    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.update()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>

$('#select2plant{{$dom}}').on('select2:select', function (e) {
    var plant = fslctplant{{$dom}}.get();
    fdtbletabledetail{{$dom}}.changeUrl({{$dom}}.url.urlDtbleDetail + '0/' + plant);
});

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "inventory/opname",
        datatable: "inventory/opname/dtble",
        urlDtbleDetail: "inventory/opname/dtble/qty/opname/"
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

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit != '0' ){
                message.info(" {{ __('This transaction has been submitted, cannot edited.') }} ");
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

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit != '0' ){
                message.info(" {{ __('This transaction has been submitted, cannot deleted.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.delete");

        },
        openLock: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been open lock.",
                            "{{$dom}}.func.openLock");

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

            window.open('inventory/opname/preview/' + data.id);
        },
        download: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            window.open('inventory/opname/download/' + data.id);
        },
        submit: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit != '0' ){
                message.info(" {{ __('This transaction has been submitted.') }} ");
                return false;
            }

            {{$dom}}.func.submit();
        },
        updateDocNumber: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.setUpdateDocNumber();

        },
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
            pickerdatedate{{$dom}}.set('select', '{{ date("Y-m-d") }}', { format: 'yyyy-mm-dd' });
            $("#pic{{$dom}}").val('');
            $("#note{{$dom}}").val('');
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fdtbletabledetail{{$dom}}.changeUrl({{$dom}}.url.urlDtbleDetail + data.id + '/' + data.plant_id);
            fslctplant{{$dom}}.set(data.plant_id, data.plant);
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            $("#note{{$dom}}").val(data.note);
            showModal('modalmanage{{$dom}}');
        },
        setUpdateDocNumber: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctuplant{{$dom}}.set(data.plant_id, data.plant);
            pickerdateudate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            $("#unote{{$dom}}").val(data.note);
            $("#upic{{$dom}}").val(data.pic);

            $("#document_number{{$dom}}").val(data.document_number);
            fslctsubmit{{$dom}}.set(data.submit);

            $("#select2uplant{{$dom}}").prop("disabled", true);
            $("#pickerdateudate{{$dom}}").prop("disabled", true);

            showModal('modalupdate{{$dom}}');
        },
        getDataForm: function () {
            var datas = fdtbletabledetail{{$dom}}.getAllData();
            var material_id = [];
            datas.map(function (data) {
                material_id.push(data.id);
            });

            return {
                'plant': fslctplant{{$dom}}.get(),
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'pic': $("#pic{{$dom}}").val(),
                'note': $("#note{{$dom}}").val(),
                'material_id': material_id,
                'qty': $("input[name='opname[]']").map(function(){return $(this).val();}).get(),
                'id': {{$dom}}.data.id
            }
        },
        getDataFormUpdate: function () {
            return {
                'submit': fslctsubmit{{$dom}}.get(),
                'document_number': $("#document_number{{$dom}}").val(),
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
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        update: function () {
            hideErrors();

            var data = {{$dom}}.func.getDataFormUpdate();
            var url = {{$dom}}.url.save + '/update';

            loadingModal('start');

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalupdate{{$dom}}');
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
        openLock: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/open-lock/' + row[0].id;

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
        submit: function () {
            loading('start', '{{ __("Submit") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/submit/' + row[0].id;

            $.get(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');

        }
    }
}

</script>
