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
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Material Formula" icon="bx bx-file" :onclick="$dom. '.event.showItem()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.showItem()" ><i class="bx bx-file mr-50"></i>{{ __('Material Formula') }}</a>
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
                    [
                        [
                            'label' => 'material name',
                            'data' => 'material_name',
                            'searchable' => 'true',
                            'orderable' => 'false',
                        ],
                        [
                            'label' => 'material code',
                            'data' => 'material_code',
                            'searchable' => 'true',
                            'orderable' => 'false',
                        ],
                    ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="master/opname-material-formula/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Opname Material Formula">
    <x-form-horizontal>
        <x-row-horizontal label="Material">
            <x-select :dom="$dom" type="serverside" compid="material" url="master/material-outlet/select?type=opname" size="sm"/>
        </x-row-horizontal>
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

<x-modal :dom="$dom" compid="modalitem" title="Opname Material Formula" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <label>{{ __('Items') }}</label>
            </div>
            <div class="col-12">
                <x-tools class="border">
                    <x-slot name="left">
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.createItem()'" />
                        </x-row-tools>
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.editItem()'" />
                        </x-row-tools>
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.deleteItem()'" />
                        </x-row-tools>
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletableitem'.$dom.'.refresh()'" />
                        </x-row-tools>
                    </x-slot>
                    <x-slot name="right">
                        <x-row-tools>
                            <x-input-search :dom="$dom" dtblecompid="tableitem" />
                        </x-row-tools>
                    </x-slot>
                </x-tools>
                @php
                    $columns =
                        [
                            [
                                'label' => 'material code',
                                'data' => 'material_code',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'material name',
                                'data' => 'material_name',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'multiplication',
                                'data' => 'multiplication',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                        ];
                @endphp
                <x-datatable-serverside :dom="$dom" compid="tableitem" :columns="$columns" url="" compidmodal="modalitem" footer="false" height="400" :select="[true, 'single']"/>
            </div>
        </div>
    </x-form-horizontal>
</x-modal>

<x-modal :dom="$dom" compid="modalmanageitem" title="Opname Material Formula Item" close="false">
    <x-form-horizontal>
        <x-row-horizontal label="Material">
            <x-select :dom="$dom" type="serverside" compid="materialitem" url="master/material-outlet/select?type=opname" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Multiplication">
            <input type="number" class="form-control form-control-sm" id="multiplication{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" onclick="{{$dom}}.func.cancelItem()">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveItem()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
{{$dom}} = {
   data: {
        id: 0,
        idItem: 0,
    },
    url: {
        save: "master/opname-material-formula",
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
        showItem: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.initItem();
            showModal('modalitem{{$dom}}');
        },
        createItem: function () {
            {{$dom}}.func.resetItem();

            hideModal('modalitem{{$dom}}');
            showModal('modalmanageitem{{$dom}}');
        },
        editItem: function () {
            var rows = fdtbletableitem{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.resetItem();
            {{$dom}}.func.setItem();
        },
        deleteItem: function () {
            var rows = fdtbletableitem{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.deleteItem");

        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctmaterial{{$dom}}.clear();
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctmaterial{{$dom}}.set(data.material_code, data.material_name);
            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'material': fslctmaterial{{$dom}}.get(),
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
                    message.failed(res.message);
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
        initItem: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#modalitem{{$dom}}-title").html("Opname Material Formula " + data.material_name);

            fdtbletableitem{{$dom}}.changeUrl({{$dom}}.url.save + '/' + data.id + '/item/dtble');
        },
        resetItem: function () {
            {{$dom}}.data.idItem = 0;

            fslctmaterialitem{{$dom}}.clear();
            $("#multiplication{{$dom}}").val('');
        },
        setItem: function () {
            var row_data = fdtbletableitem{{$dom}}.getSelectedData();
            data = row_data[0];

            {{$dom}}.data.idItem = data.id;

            fslctmaterialitem{{$dom}}.set(data.material_code, data.material_name);
            $("#multiplication{{$dom}}").val(data.multiplication);

            hideModal('modalitem{{$dom}}');
            showModal('modalmanageitem{{$dom}}');
        },
        cancelItem: function () {
            hideModal('modalmanageitem{{$dom}}');
            showModal('modalitem{{$dom}}');
        },
        getDataFormItem: function () {
            return {
                'material': fslctmaterialitem{{$dom}}.get(),
                'multiplication': $("#multiplication{{$dom}}").val(),
                'id': {{$dom}}.data.idItem
            }
        },
        saveItem: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormItem();

            var url = {{$dom}}.url.save + '/' + {{$dom}}.data.id + '/item';
            if( {{$dom}}.data.idItem != 0 ){
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletableitem{{$dom}}.refresh();
                    hideModal('modalmanageitem{{$dom}}');
                    showModal('modalitem{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        deleteItem: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletableitem{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.save + '/' + {{$dom}}.data.id + '/item/delete/' + row[0].id;

            $.post(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletableitem{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        },
    }
}

</script>
