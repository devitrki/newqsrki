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
                                'label' => 'name',
                                'data' => 'name',
                            ],[
                                'label' => 'top value',
                                'data' => 'top_value',
                            ],[
                                'label' => 'bottom value',
                                'data' => 'bottom_value',
                            ],[
                                'label' => 'top value center',
                                'data' => 'top_value_center',
                            ],[
                                'label' => 'bottom value center',
                                'data' => 'bottom_value_center',
                            ],[
                                'label' => 'interval',
                                'data' => 'interval',
                            ],[
                                'label' => 'uom',
                                'data' => 'uom',
                            ],[
                                'label' => 'status',
                                'data' => 'status_desc',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/mapping/storage-temp/dtble" :select="[true, 'single']"/>
        </div>
    </div>
</x-card-scroll>

<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Storage Temperature">
    <x-form-horizontal>
        <x-row-horizontal label="Name">
            <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Bottom Value">
            <input type="number" class="form-control form-control-sm" id="bottom_value{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Top Value">
            <input type="number" class="form-control form-control-sm" id="top_value{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Bottom Value Center">
            <input type="number" class="form-control form-control-sm" id="bottom_value_center{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Top Value Center">
            <input type="number" class="form-control form-control-sm" id="top_value_center{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="interval">
            <input type="number" class="form-control form-control-sm" id="interval{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Uom">
            <input type="text" class="form-control form-control-sm" id="uom{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Status">
            <select class="form-control form-control-sm" id="status{{$dom}}">
                @php
                    $options = [
                                ['value' => 1, 'text' => 'Active'],
                                ['value' => 0, 'text' => 'Unactive']
                            ];
                @endphp
                @foreach( $options as $opt)
                    <option value="{{$opt['value']}}">{{$opt['text']}}</option>
                @endforeach
            </select>
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
<!-- end modal -->

<script>
{{$dom}} = {
    data: {
        id: 0,
    },
    url: {
        save: "logbook/mapping/storage-temp",
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
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#name{{$dom}}").val('');
            $("#bottom_value{{$dom}}").val('');
            $("#top_value{{$dom}}").val('');
            $("#bottom_value_center{{$dom}}").val('');
            $("#top_value_center{{$dom}}").val('');
            $("#interval{{$dom}}").val('');
            $("#uom{{$dom}}").val('');
            $("#status{{$dom}}").val('');
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;
            $("#name{{$dom}}").val(data.name);
            $("#bottom_value{{$dom}}").val(data.bottom_value);
            $("#top_value{{$dom}}").val(data.top_value);
            $("#bottom_value_center{{$dom}}").val(data.bottom_value_center);
            $("#top_value_center{{$dom}}").val(data.top_value_center);
            $("#interval{{$dom}}").val(data.interval);
            $("#uom{{$dom}}").val(data.uom);
            $("#status{{$dom}}").val(data.status);

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'name': $("#name{{$dom}}").val(),
                'bottom_value': $("#bottom_value{{$dom}}").val(),
                'top_value': $("#top_value{{$dom}}").val(),
                'bottom_value_center': $("#bottom_value_center{{$dom}}").val(),
                'top_value_center': $("#top_value_center{{$dom}}").val(),
                'interval': $("#interval{{$dom}}").val(),
                'uom': $("#uom{{$dom}}").val(),
                'status': $("#status{{$dom}}").val(),
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

        }
    }
}

</script>
