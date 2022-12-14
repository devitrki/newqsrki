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
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Set Content" icon="bx bx-file" :onclick="$dom. '.event.setContent()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Send Notification" icon="bx bx-send" :onclick="$dom. '.event.send()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.setContent()" ><i class="bx bx-file mr-50"></i>{{ __('Set Content') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.send()" ><i class="bx bx-send mr-50"></i>{{ __('Send Notification') }}</a>
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
                    ],[
                        'label' => 'key',
                        'data' => 'key',
                    ],[
                        'label' => 'description',
                        'data' => 'description',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="application/notification-system/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Notification System">
    <x-form-horizontal>
        <x-row-horizontal label="Name">
            <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Key">
            <input type="text" class="form-control form-control-sm" id="key{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Description">
            <textarea class="form-control form-control-sm" id="description{{$dom}}" rows="3"></textarea>
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

<x-modal :dom="$dom" compid="modalcontent" title="Content Notification System" size="lg">
    <x-tools class="border">
        <x-slot name="left">
            <x-row-tools>
                <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.createContent()'" />
            </x-row-tools>
            <x-row-tools>
                <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.editContent()'" />
            </x-row-tools>
            <x-row-tools>
                <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.deleteContent()'" />
            </x-row-tools>
        </x-slot>
    </x-tools>
    @php
        $columns =
            [[
                'label' => 'lang',
                'data' => 'lang',
            ],[
                'label' => 'title',
                'data' => 'title',
            ]];
    @endphp
    <x-datatable-serverside :dom="$dom" compid="tablecontent" :columns="$columns" url="" compidmodal="modalcontent" footer="false" height="300" :select="[true, 'single']" />
</x-modal>

<x-modal :dom="$dom" compid="modalmanagecontent" title="Content Notification System" close="false">
    <x-form-vertical>
        <x-row-vertical label="Title">
            <input type="text" class="form-control form-control-sm" id="title{{$dom}}">
        </x-row-vertical>
        <x-row-vertical label="Language">
            <x-select :dom="$dom" compid="language" type="serverside" url="master/language/select" size="sm"/>
        </x-row-vertical>
        <x-row-vertical label="Content">
            <div class="row">
                <div class="col-sm-12">
                    <div id="snow-wrapper">
                        <div id="snow-container{{$dom}}">
                            <div class="quill-toolbar">
                                <span class="ql-formats">
                                    <select class="ql-header">
                                        <option value="1">Heading</option>
                                        <option value="2">Subheading</option>
                                        <option selected>Normal</option>
                                    </select>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-list" value="ordered"></button>
                                    <button class="ql-list" value="bullet"></button>
                                </span>
                            </div>
                            <div class="editor">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-row-vertical>
    </x-form-vertical>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" onclick="{{$dom}}.func.cancelContent()">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveContent()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalsend" title="Send Notification">
    <x-form-horizontal>
        <x-row-horizontal label="Role">
            <x-select :dom="$dom" compid="role" multiple type="serverside" url="application/authentication/role/select?superadmin=true" size="sm"/>
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.send()">
            <span>{{ __('Send') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
var snowEditor{{$dom}} = new Quill('#snow-container{{$dom}} .editor', {
    bounds: '#snow-container{{$dom}} .editor',
    modules: {
        'formula': true,
        'syntax': true,
        'toolbar': '#snow-container{{$dom}} .quill-toolbar'
    },
    theme: 'snow'
});

{{$dom}} = {
    data: {
        id: 0,
        idContent: 0,
    },
    url: {
        save: "application/notification-system",
        saveContent: "application/notification-system/content",
        dtbleContent: "application/notification-system/dtble/content/",
        send: "application/notification-system/send",
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
        setContent: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            urlDtble = {{$dom}}.url.dtbleContent + data.id;
            fdtbletablecontent{{$dom}}.changeUrl(urlDtble);

            showModal('modalcontent{{$dom}}');
        },
        createContent: function () {
            {{$dom}}.func.resetContent();
            hideModal('modalcontent{{$dom}}');
            showModal('modalmanagecontent{{$dom}}');
        },
        editContent: function () {
            var rows = fdtbletablecontent{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.resetContent();
            {{$dom}}.func.setContent();
        },
        deleteContent: function () {
            var rows = fdtbletablecontent{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.deleteContent");

        },
        send: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.setSend();
        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#name{{$dom}}").val('');
            $("#key{{$dom}}").val('');
            $("#description{{$dom}}").val('');
        },
        resetContent: function () {
            {{$dom}}.data.idContent = 0;
            $("#title{{$dom}}").val('');
            snowEditor{{$dom}}.root.innerHTML = "";
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#name{{$dom}}").val(data.name);
            $("#key{{$dom}}").val(data.key);
            $("#description{{$dom}}").val(data.description);
            showModal('modalmanage{{$dom}}');
        },
        setSend: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#name{{$dom}}").val(data.name);
            $("#key{{$dom}}").val(data.key);
            $("#description{{$dom}}").val(data.description);
            showModal('modalsend{{$dom}}');
        },
        setContent: function () {
            var row_data = fdtbletablecontent{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.idContent = data.id;

            $("#title{{$dom}}").val(data.title);
            fslctlanguage{{$dom}}.set(data.languange_id, data.lang);

            snowEditor{{$dom}}.root.innerHTML = data.content;

            hideModal('modalcontent{{$dom}}');
            showModal('modalmanagecontent{{$dom}}');
        },
        getDataForm: function () {
            return {
                'name': $("#name{{$dom}}").val(),
                'key': $("#key{{$dom}}").val(),
                'description': $("#description{{$dom}}").val(),
                'id': {{$dom}}.data.id
            }
        },
        getDataFormContent: function () {
            return {
                'title': $("#title{{$dom}}").val(),
                'language': fslctlanguage{{$dom}}.get(),
                'content': snowEditor{{$dom}}.root.innerHTML,
                'notification_system_id': {{$dom}}.data.id,
                'id': {{$dom}}.data.idContent
            }
        },
        getDataFormSend: function () {
            return {
                'role': fslctrole{{$dom}}.get().join(),
                'id': {{$dom}}.data.id
            }
        },
        cancelContent: function () {
            hideModal('modalmanagecontent{{$dom}}');
            showModal('modalcontent{{$dom}}');
        },
        saveContent: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormContent();

            var url = {{$dom}}.url.saveContent;
            if( {{$dom}}.data.idContent != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletablecontent{{$dom}}.refresh();
                    hideModal('modalmanagecontent{{$dom}}');
                    showModal('modalcontent{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
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
        send: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormSend();

            var url = {{$dom}}.url.send;

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalsend{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        deleteContent: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletablecontent{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.saveContent + '/' + row[0].id;

            $.post(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletablecontent{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
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
