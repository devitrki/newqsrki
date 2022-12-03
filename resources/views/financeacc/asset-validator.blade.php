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
                        <x-button-tools tooltip="Set PIC DC" icon="bx bx-file" :onclick="$dom. '.event.setPic()'" />
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
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                @endcan
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.setPic()" ><i class="bx bx-file mr-50"></i>{{ __('Set PIC DC') }}</a>
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
                    [[
                        'label' => 'name',
                        'data' => 'name',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="financeacc/asset/validator/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Asset Validator">
    <x-form-horizontal>
        <x-row-horizontal label="Name">
            <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
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

<x-modal :dom="$dom" compid="modalpic" title="Mapping PIC DC" size="lg">
    <x-tools class="border">
        <x-slot name="left">
            <x-row-tools>
                <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.createPic()'" />
            </x-row-tools>
            <x-row-tools>
                <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.editPic()'" />
            </x-row-tools>
            <x-row-tools>
                <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.deletePic()'" />
            </x-row-tools>
        </x-slot>
    </x-tools>
    @php
        $columns =
            [[
                'label' => 'Validator',
                'data' => 'name',
            ],[
                'label' => 'plant',
                'data' => 'plant',
            ],[
                'label' => 'pic',
                'data' => 'pic',
            ]];
    @endphp
    <x-datatable-serverside :dom="$dom" compid="tablepic" :columns="$columns" url="" compidmodal="modalpic" footer="false" height="300" :select="[true, 'single']" />
</x-modal>

<x-modal :dom="$dom" compid="modalmanagepic" title="Mapping PIC DC">
    <x-form-horizontal>
        <x-row-horizontal label="DC">
            <x-select :dom="$dom" compid="dc" type="serverside" url="master/plant/select?type=dc" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="PIC">
            <x-select :dom="$dom" compid="pic" type="serverside" multiple url="application/authentication/user/select?init=false" size="sm"/>
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" onclick="{{$dom}}.func.closePic()">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.savePic()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
{{$dom}} = {
   data: {
        id: 0,
        idPic: 0,
    },
    url: {
        save: "financeacc/asset/validator",
        savePic: "financeacc/asset/validator/pic",
        dtblePic: "financeacc/asset/validator/dtble/pic/",
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
        setPic: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            urlDtble = {{$dom}}.url.dtblePic + data.id;
            fdtbletablepic{{$dom}}.changeUrl(urlDtble);

            showModal('modalpic{{$dom}}');
        },
        createPic: function () {
            {{$dom}}.func.resetPic();
            hideModal('modalpic{{$dom}}');
            showModal('modalmanagepic{{$dom}}');
        },
        editPic: function () {
            var rows = fdtbletablepic{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.resetPic();
            {{$dom}}.func.setPic();
        },
        deletePic: function () {
            var rows = fdtbletablepic{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.deletePic");

        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#name{{$dom}}").val('');
        },
        resetPic: function () {
            {{$dom}}.data.idPic = 0;
            fslctdc{{$dom}}.clear();
            fslctpic{{$dom}}.clear();
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#name{{$dom}}").val(data.name);
            showModal('modalmanage{{$dom}}');
        },
        setPic: function () {
            var row_data = fdtbletablepic{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.idPic = data.id;

            fslctdc{{$dom}}.set(data.plant_id, data.plant);

            var pic_validators = data.pic_validators.split(",");
            var pic_validator_names = data.pic_validator_names.split(",");

            for (var i = 0; i < pic_validators.length; i++) {
                fslctpic{{$dom}}.set(pic_validators[i], pic_validator_names[i]);
            }

            hideModal('modalpic{{$dom}}');
            showModal('modalmanagepic{{$dom}}');
        },
        getDataForm: function () {
            return {
                'name': $("#name{{$dom}}").val(),
                'id': {{$dom}}.data.id
            }
        },
        getDataFormPic: function () {
            return {
                'dc': fslctdc{{$dom}}.get(),
                'pic': fslctpic{{$dom}}.get().join(),
                'asset_validator_id': {{$dom}}.data.id,
                'id': {{$dom}}.data.idPic
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
        closePic: function () {
            hideModal('modalmanagepic{{$dom}}');
            showModal('modalpic{{$dom}}');
        },
        savePic: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormPic();
            var url = {{$dom}}.url.savePic;
            if( {{$dom}}.data.idPic != 0 ){
                url += '/' + {{$dom}}.data.idPic;
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletablepic{{$dom}}.refresh();
                    hideModal('modalmanagepic{{$dom}}');
                    showModal('modalpic{{$dom}}');
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
        deletePic: function () {
            loadingModal('start');
            var row = fdtbletablepic{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.savePic + '/' + row[0].id;

            $.post(url, data, function (res) {
                loadingModal("stop");
                if (res.status == 'success') {
                    fdtbletablepic{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        }
    }
}

</script>
