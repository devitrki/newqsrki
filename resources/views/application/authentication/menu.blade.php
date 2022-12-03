<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 col-sm-4 p-0 border-right">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools>
                        <h6 class="text-vertical">Structure Menu</h6>
                    </x-row-tools>
                </x-slot>
                <x-slot name="right">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-loader" :onclick="$dom. '.func.refreshTreeview()'" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-button-tools tooltip="Collapse All" icon="bx bx-folder" :onclick="$dom. '.func.collapse()'" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-button-tools tooltip="Expand All" icon="bx bx-folder-open" :onclick="$dom. '.func.expand()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            <!-- treeview menu -->
            <div class="height-min-tools overflow-auto">
                <div id="treeview"></div>
            </div>
        </div>
        <div class="col-12 col-sm-8 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create Menu" icon="bx bx-file-blank" :onclick="$dom. '.event.createMenu()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create Folder" icon="bx bx-folder" :onclick="$dom. '.event.createFolder()'" />
                    </x-row-tools>
                    @endcan
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Edit Menu / Folder" icon="bx bx-edit" :onclick="$dom. '.event.edit()'" />
                    </x-row-tools>
                    @endcan
                    @can('d'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Remove Menu / Folder" icon="bx bx-trash" :onclick="$dom. '.event.delete()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbledetailmenu'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.createMenu()" ><i class="bx bx-file-blank mr-50"></i>{{ __('Create Menu') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.createFolder()" ><i class="bx bx-folder mr-50"></i>{{ __('Create Folder') }}</a>
                                @endcan
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit Menu / Folder') }}</a>
                                @endcan
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete Menu / Folder') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick=fdtbledetailmenu{{$dom}}.refresh()><i class="bx bx-revision mr-50"></i>{{ __('Refresh') }}</a>
                            </div>
                        </div>
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns =
                    [[
                        'label' => 'Name',
                        'data' => 'name',
                    ],[
                        'label' => 'Type',
                        'data' => 'type',
                    ],[
                        'label' => 'URL',
                        'data' => 'url',
                    ],[
                        'label' => 'Description',
                        'data' => 'description',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="detailmenu" :tabmenu="$menu_id" :columns="$columns" url="auth/menu/dtble?parentid=0" :select="[true, 'single']" row-reorder="true" footer="false" number="false" />
        </div>
    </div>
</x-card-scroll>

<!-- modal -->
<x-modal :dom="$dom" compid="modalCreateMenu" title="Create Menu">
    <x-form-horizontal>
        <x-row-horizontal label="Menu">
            <input type="text" class="form-control form-control-sm" id="menu{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Description">
            <input type="text" class="form-control form-control-sm" id="description{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="URL">
            <input type="text" class="form-control form-control-sm" id="url{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveCreateMenu()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalCreateFolder" title="Create Folder">
    <x-form-horizontal>
        <x-row-horizontal label="Menu">
            <input type="text" class="form-control form-control-sm" id="fmenu{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Description">
            <input type="text" class="form-control form-control-sm" id="fdescription{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveCreateFolder()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<!-- end modal -->

<script>

// treeview
$.get('auth/menu/treeview/json', function (res) {
    var json = res;
    $('#treeview').treeview({
        selectedBackColor: ['#758daa'],
        showBorder: false,
        levels: 2,
        expandIcon: 'bx bx-folder-plus',
        collapseIcon: 'bx bx-folder-minus',
        data: res
    });
    $('#treeview').on('nodeSelected', function(event, data) {
        {{$dom}}.data.parentid = data.data.id;
        fdtbledetailmenu{{$dom}}.changeUrl('auth/menu/dtble?parentid=' + data.data.id);
    });
});

// datatable drag
$('#datatabledetailmenu{{ $dom }}').on('row-reorder.dt', function (e, data, nodes) {
    loading('start');
    var resort = [];
    for (let i = 0; i < data.length; i++) {
        rowdataold = fdtbledetailmenu{{$dom}}.getRowIndex(data[i].oldPosition);
        rowdatanew = fdtbledetailmenu{{$dom}}.getRowIndex(data[i].newPosition );
        resort.push(rowdataold.id + ',' + rowdatanew.id);
    }

    if(resort.length > 0){
        var data = {
            'arsort' : resort,
        };

        $.post( {{$dom}}.url.changeSort, data, function (res) {
            loading('stop');
            if( res.status == 'success' ){
                fdtbledetailmenu{{$dom}}.refresh();
                toastr.success(res.message, 'Change Sort');
            } else {
                toastr.failed(res.message, 'Change Sort');
            }
        }, 'json');

    } else {
        loading('stop');
    }

});

{{$dom}} = {
    data: {
        parentid: 0,
        id: 0,
    },
    url: {
        changeSort: "auth/menu/sort/change",
        createMenu: "auth/menu/create",
        createFolder: "auth/menu/folder/create",
        delete: "auth/menu/delete/",
        edit: "auth/menu/edit",
    },
    event: {
        createMenu: function () {
            {{$dom}}.func.reset();
            showModal('modalCreateMenu{{$dom}}');
        },
        createFolder: function () {
            {{$dom}}.func.reset();
            showModal('modalCreateFolder{{$dom}}');
        },
        edit: function () {
            var rows = fdtbledetailmenu{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        delete: function () {
            var rows = fdtbledetailmenu{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Deleting menu will also remove all user auth/permission to the menu.",
                            "{{$dom}}.func.delete");

        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#menu{{$dom}}").val('');
            $("#description{{$dom}}").val('');
            $("#url{{$dom}}").val('');
            $("#fmenu{{$dom}}").val('');
            $("#fdescription{{$dom}}").val('');
        },
        set: function () {
            var row_data = fdtbledetailmenu{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;
            if(data.type.toLowerCase() != 'folder'){
                $("#menu{{$dom}}").val(data.name);
                $("#description{{$dom}}").val(data.description);
                $("#url{{$dom}}").val(data.url);
                showModal('modalCreateMenu{{$dom}}');
            }else{
                $("#fmenu{{$dom}}").val(data.name);
                $("#fdescription{{$dom}}").val(data.description);
                showModal('modalCreateFolder{{$dom}}');
            }
        },
        expand: function(){
            var levels = $('#select-expand-all-levels').val();
            $('#treeview').treeview('expandAll', {
                levels: 99
            });
        },
        collapse: function(){
            $('#treeview').treeview('collapseAll');
        },
        getDataFormCreateMenu: function () {
            return {
                'menu': $("#menu{{$dom}}").val(),
                'description': $("#description{{$dom}}").val(),
                'url': $("#url{{$dom}}").val(),
                'parentid': {{$dom}}.data.parentid,
                'id': {{$dom}}.data.id
            }
        },
        getDataFormCreateFolder: function () {
            return {
                'menu': $("#fmenu{{$dom}}").val(),
                'description': $("#fdescription{{$dom}}").val(),
                'url': "",
                'parentid': {{$dom}}.data.parentid,
                'id': {{$dom}}.data.id
            }
        },
        saveCreateMenu: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormCreateMenu();
            var url = "";
            if( {{$dom}}.data.id != 0 ){
                url = {{$dom}}.url.edit;
            } else {
                url = {{$dom}}.url.createMenu;
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbledetailmenu{{$dom}}.refresh();
                    {{$dom}}.func.refreshTreeview();
                    hideModal('modalCreateMenu{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        saveCreateFolder: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormCreateFolder();
            var url = "";

            if( {{$dom}}.data.id != 0 ){
                url = {{$dom}}.url.edit;
            } else {
                url = {{$dom}}.url.createFolder;
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbledetailmenu{{$dom}}.refresh();
                    {{$dom}}.func.refreshTreeview();
                    hideModal('modalCreateFolder{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        refreshTreeview: function () {
            $.get('auth/menu/treeview/json', function (res) {
                var json = res;
                $('#treeview').treeview({
                    selectedBackColor: ['#758daa'],
                    showBorder: false,
                    levels: 2,
                    expandIcon: 'bx bx-folder-plus',
                    collapseIcon: 'bx bx-folder-minus',
                    data: res
                });
                $('#treeview').on('nodeSelected', function(event, data) {
                    {{$dom}}.data.parentid = data.data.id;
                    fdtbledetailmenu{{$dom}}.changeUrl('auth/menu/dtble?parentid=' + data.data.id);
                });
            });
        },
        delete: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbledetailmenu{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.delete + row[0].id;

            $.post(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbledetailmenu{{$dom}}.refresh();
                    {{$dom}}.func.refreshTreeview();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        }
    }
}

</script>
