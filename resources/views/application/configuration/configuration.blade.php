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
                        <x-button-tools tooltip="Copy" icon="bx bx-copy" :onclick="$dom. '.event.copy()'" />
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
                                @can('e'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.copy()" ><i class="bx bx-copy mr-50"></i>{{ __('Copy') }}</a>
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
                $columns =[
                    [
                        'label' => 'Company',
                        'data' => 'company_name',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'Group',
                        'data' => 'group_name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'for',
                        'data' => 'for',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'type',
                        'data' => 'type',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'label',
                        'data' => 'label',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'description',
                        'data' => 'description',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'key',
                        'data' => 'key',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'value',
                        'data' => 'value',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],
                    [
                        'label' => 'option',
                        'data' => 'option',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]
                ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="application/general-configuration/configuration/dtble" :select="[true, 'multiple']"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Configuration">
    <x-form-horizontal>
        <x-row-horizontal label="Company">
            <x-select :dom="$dom" compid="company" type="serverside" url="master/company/select" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Configuration Group">
            <x-select :dom="$dom" compid="configuration_group" type="serverside" url="application/general-configuration/configuration-group/select" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="For">
            <input type="text" class="form-control form-control-sm" id="for{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Type">
            @php
                $options = [
                                ['id' => 'text', 'text' => 'text'],
                                ['id' => 'select', 'text' => 'select'],
                                ['id' => 'select2', 'text' => 'select2'],
                                ['id' => 'textarea', 'text' => 'textarea'],
                            ];
            @endphp
            <x-select :dom="$dom" compid="type" type="array" :options="$options" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Label">
            <input type="text" class="form-control form-control-sm" id="label{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Description">
            <textarea class="form-control form-control-sm" id="description{{$dom}}" rows="3"></textarea>
        </x-row-horizontal>
        <x-row-horizontal label="Key">
            <input type="text" class="form-control form-control-sm" id="key{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Value">
            <textarea class="form-control form-control-sm" id="value{{$dom}}" rows="3"></textarea>
        </x-row-horizontal>
        <x-row-horizontal label="Option">
            <textarea class="form-control form-control-sm" id="option{{$dom}}" rows="3"></textarea>
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

<x-modal :dom="$dom" compid="modalcopy" title="Configuration Copy">
    <x-form-horizontal>
        <x-row-horizontal label="Company To">
            <x-select :dom="$dom" compid="company_to" type="serverside" url="master/company/select" size="sm"/>
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
        save: "application/general-configuration/configuration",
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

            if( rows > 1  ){
                message.info(" {{ __('Please select one transaction to edit') }} ");
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

            if( rows > 1  ){
                message.info(" {{ __('Please select one transaction to edit') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.delete");

        },
        copy: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.resetCopy();
            showModal('modalcopy{{$dom}}');
        }
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#for{{$dom}}").val('');
            $("#label{{$dom}}").val('');
            $("#description{{$dom}}").val('');
            $("#key{{$dom}}").val('');
            $("#value{{$dom}}").val('');
            $("#option{{$dom}}").val('');

            fslctcompany{{$dom}}.clear();
            fslctconfiguration_group{{$dom}}.clear();
            fslcttype{{$dom}}.clear();
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#for{{$dom}}").val(data.for);
            $("#label{{$dom}}").val(data.label);
            $("#description{{$dom}}").val(data.description);
            $("#key{{$dom}}").val(data.key);
            $("#value{{$dom}}").val(data.value);
            $("#option{{$dom}}").val(data.option);

            fslctcompany{{$dom}}.set(data.company_id, data.company_name);
            fslctconfiguration_group{{$dom}}.set(data.configuration_group_id, data.group_name);
            fslcttype{{$dom}}.set(data.type, data.type);

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'for': $("#for{{$dom}}").val(),
                'label': $("#label{{$dom}}").val(),
                'description': $("#description{{$dom}}").val(),
                'key': $("#key{{$dom}}").val(),
                'value': $("#value{{$dom}}").val(),
                'option': $("#option{{$dom}}").val(),
                'company': fslctcompany{{$dom}}.get(),
                'configuration_group': fslctconfiguration_group{{$dom}}.get(),
                'type': fslcttype{{$dom}}.get(),
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
        resetCopy: function () {
            fslctcompany_to{{$dom}}.clear();
        },
        copy: function () {
            hideErrors();
            loadingModal('start');

            var datas = fdtbletabledata{{$dom}}.getSelectedData();

            var idCopies = [];

            datas.map(function (d) {
                idCopies.push(d.id);
            });

            var data = {
                'id_copies': JSON.stringify(idCopies),
                'company_to': fslctcompany_to{{$dom}}.get(),
            };

            var url = {{$dom}}.url.save + '/copy';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalcopy{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
    }
}

</script>
