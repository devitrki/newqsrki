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
                        <x-button-tools tooltip="Configuration" icon="bx bx-cog" :onclick="$dom. '.event.showConf()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.showConf()" ><i class="bx bx-cog mr-50"></i>{{ __('Configuration') }}</a>
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
                            'label' => 'name',
                            'data' => 'name',
                            'searchable' => 'true',
                            'orderable' => 'false',
                        ],
                        [
                            'label' => 'code',
                            'data' => 'code',
                            'searchable' => 'true',
                            'orderable' => 'false',
                        ],
                    ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="master/pos/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="POS">
    <x-form-horizontal>
        <x-row-horizontal label="Name">
            <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Code">
            <input type="text" class="form-control form-control-sm" id="code{{$dom}}">
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

<x-modal :dom="$dom" compid="modalconf" title="POS" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <label>{{ __('Configuration') }}</label>
            </div>
            <div class="col-12">
                <x-tools class="border">
                    <x-slot name="left">
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.createConf()'" />
                        </x-row-tools>
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.editConf()'" />
                        </x-row-tools>
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.deleteConf()'" />
                        </x-row-tools>
                        <x-row-tools class="d-none d-sm-block">
                            <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletableconf'.$dom.'.refresh()'" />
                        </x-row-tools>
                    </x-slot>
                </x-tools>
                @php
                    $columns =
                        [
                            [
                                'label' => 'key',
                                'data' => 'key',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'value',
                                'data' => 'value',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                        ];
                @endphp
                <x-datatable-serverside :dom="$dom" compid="tableconf" :columns="$columns" url="" compidmodal="modalconf" footer="false" height="400" :select="[true, 'single']"/>
            </div>
        </div>
    </x-form-horizontal>
</x-modal>

<x-modal :dom="$dom" compid="modalmanageconf" title="POS Configuration" close="false">
    <x-form-horizontal>
        <x-row-horizontal label="Key">
            <input type="text" class="form-control form-control-sm" id="key{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Value">
            <input type="text" class="form-control form-control-sm" id="value{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" onclick="{{$dom}}.func.cancelConf()">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveConf()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
{{$dom}} = {
   data: {
        id: 0
    },
    url: {
        save: "master/pos",
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
        showConf: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.initConf();
            showModal('modalconf{{$dom}}');
        },
        createConf: function () {
            {{$dom}}.func.resetConf();

            hideModal('modalconf{{$dom}}');
            showModal('modalmanageconf{{$dom}}');
        },
        editConf: function () {
            var rows = fdtbletableconf{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.resetConf();
            {{$dom}}.func.setConf();
        },
        deleteConf: function () {
            var rows = fdtbletableconf{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.deleteConf");

        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#name{{$dom}}").val('');
            $("#code{{$dom}}").val('');
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#name{{$dom}}").val(data.name);
            $("#code{{$dom}}").val(data.code);
            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'name': $("#name{{$dom}}").val(),
                'code': $("#code{{$dom}}").val(),
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
        initConf: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#modalconf{{$dom}}-title").html("Pos " + data.name);

            fdtbletableconf{{$dom}}.changeUrl({{$dom}}.url.save + '/' + data.id + '/configuration/dtble');
        },
        resetConf: function () {
            {{$dom}}.data.idConf = 0;
            $("#key{{$dom}}").val('');
            $("#value{{$dom}}").val('');

            $("#key{{$dom}}").prop("disabled", false);
        },
        setConf: function () {
            var row_data = fdtbletableconf{{$dom}}.getSelectedData();
            data = row_data[0];

            $("#key{{$dom}}").val(data.key);
            $("#value{{$dom}}").val(data.value);

            $("#key{{$dom}}").prop("disabled", true);

            hideModal('modalconf{{$dom}}');
            showModal('modalmanageconf{{$dom}}');
        },
        cancelConf: function () {
            hideModal('modalmanageconf{{$dom}}');
            showModal('modalconf{{$dom}}');
        },
        getDataFormConf: function () {
            return {
                'key': $("#key{{$dom}}").val(),
                'value': $("#value{{$dom}}").val()
            }
        },
        saveConf: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormConf();

            var url = {{$dom}}.url.save + '/' + {{$dom}}.data.id + '/configuration';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletableconf{{$dom}}.refresh();
                    hideModal('modalmanageconf{{$dom}}');
                    showModal('modalconf{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        deleteConf: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletableconf{{$dom}}.getSelectedData();

            var data = {
                'key': row[0].key
            };

            var url = {{$dom}}.url.save + '/' + {{$dom}}.data.id + '/configuration/delete';

            $.post(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletableconf{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        },
    }
}

</script>
