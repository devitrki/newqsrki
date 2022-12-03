<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Save" icon="bx bx-save" :onclick="$dom. '.func.save()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Copy" icon="bx bx-copy" :onclick="$dom. '.event.copy()'" />
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
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.func.save()" ><i class="bx bx-save mr-50"></i>{{ __('Save') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.copy()" ><i class="bx bx-copy mr-50"></i>{{ __('Copy') }}</a>
                                @endcan
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
                                    <x-row-vertical label="Role">
                                        <x-select :dom="$dom" compid="frole" type="serverside" url="application/authentication/role/select?superadmin=true" :default="[$role->id, $role->name]" size="sm" dropdowncompid="tabledata" />
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
                </x-slot>
            </x-tools>
            @php
                $columns =
                    [[
                        'label' => 'Menu',
                        'data' => 'menu',
                    ],
                    [
                        'label' => 'Parent Menu',
                        'data' => 'parent',
                    ]];
                foreach ($permissions as $k => $v) {
                    $columns[] = [
                        'label' => $v->name,
                        'data' => 'p-'.$v->id,
                    ];
                }
                $url = "application/authentication/permissionmenu/dtble/".$role->id;
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" :url="$url" number="false" footer="false"/>
        </div>
    </div>
</x-card-scroll>

<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Copy">
    <x-form-horizontal>
        <x-row-horizontal label="From Role">
            <x-select :dom="$dom" compid="fromrole" type="serverside" url="application/authentication/role/select" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="To Role">
            <x-select :dom="$dom" compid="torole" type="serverside" url="application/authentication/role/select" size="sm"/>
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.copy()">
            <span>{{ __('Copy') }}</span>
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
        save: "application/authentication/permissionmenu",
        copy: "application/authentication/permissionmenu/copy",
        datatable: "application/authentication/permissionmenu/dtble/",
    },
    event: {
        copy: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        }
    },
    func: {
        reset: function () {
            fslctfromrole{{$dom}}.clear();
            fslcttorole{{$dom}}.clear();
        },
        filter: function () {
            var url = {{$dom}}.url.datatable + fslctfrole{{$dom}}.get();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        getDataCheckbox: function () {
            var datas = {};
            @php
            foreach ($permissions as $k => $v) {
                echo 'var ap'. $v->id .' = [];';
                echo '$("input:checkbox[name=p' . $v->id . ']:checked").each(function(){';
                    echo 'ap'. $v->id .'.push($(this).val());';
                echo '});';
                echo 'datas.p'. $v->id .' = ap'. $v->id .';';
            }
            @endphp
            return datas;
        },
        save: function () {

            loading('start', '{{ __("Save") }}', 'process');

            var data = {{$dom}}.func.getDataCheckbox();
            data.roleid = fslctfrole{{$dom}}.get();
            var url = {{$dom}}.url.save;

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    toastr.success(res.message, 'Permission Menu');
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        getDataForm: function () {
            return {
                'fromrole': fslctfromrole{{$dom}}.get(),
                'torole': fslcttorole{{$dom}}.get(),
                'id': {{$dom}}.data.id
            }
        },
        copy: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.copy;

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
        }
    }
}

</script>
