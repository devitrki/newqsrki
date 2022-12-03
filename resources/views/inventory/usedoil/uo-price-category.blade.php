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
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.setDetail()" ><i class="bx bx-file mr-50"></i>{{ __('Set Detail') }}</a>
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
                        'label' => 'category price',
                        'data' => 'name',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/usedoil/uo-price-category/dtble" :select="[true, 'single']" :dblclick="true"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Category Price" size="lg">
    <x-form-horizontal>
        <x-row-horizontal label="Name">
            <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    @php
        $columns =
            [[
                'label' => 'material code',
                'data' => 'code',
            ],[
                'label' => 'material name',
                'data' => 'name',
            ],[
                'label' => 'material uom',
                'data' => 'uom',
            ],[
                'label' => 'price',
                'data' => 'price_input',
                'class' => 'input'
            ]];
    @endphp
    <x-datatable-serverside :dom="$dom" compid="tabledetail" :columns="$columns" url="" compidmodal="modalmanage" footer="false" height="150" />

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

// event double click comp datatable
function callbacktabledata{{$dom}}(data) {
    {{$dom}}.func.set(data);
}

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "inventory/usedoil/uo-price-category",
        dtbleDetail: "inventory/usedoil/uo-price-category/dtble/detail/",
    },
    event: {
        create: function () {
            {{$dom}}.func.reset();

            urlDtble = {{$dom}}.url.dtbleDetail + '0';
            fdtbletabledetail{{$dom}}.changeUrl(urlDtble);

            showModal('modalmanage{{$dom}}');
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
        },
        set: function (data) {
            {{$dom}}.data.id = data.id;

            urlDtble = {{$dom}}.url.dtbleDetail + data.id;
            fdtbletabledetail{{$dom}}.changeUrl(urlDtble);

            $("#name{{$dom}}").val(data.name);
            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            var datas = fdtbletabledetail{{$dom}}.getAllData();
            var material_id = [];
            datas.map(function (data) {
                material_id.push(data.id);
            });

            return {
                'name': $("#name{{$dom}}").val(),
                'material_id': material_id,
                'price': $("input[name='uopricecategory[]']").map(function(){return $(this).val();}).get(),
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

        }
    }
}

</script>
