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
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Send Manual" icon="bx bx-send" :onclick="$dom. '.event.send()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Download File Sales" icon="bx bx-download" :onclick="$dom. '.event.download()'" />
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
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.send()" ><i class="bx bx-send mr-50"></i>{{ __('Send Manual') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.download()" ><i class="bx bx-download mr-50"></i>{{ __('Download File Sales') }}</a>
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
                            'label' => 'plant',
                            'data' => 'description',
                            'searchable' => 'true',
                            'orderable' => 'false',
                        ],
                        [
                            'label' => 'template sales',
                            'data' => 'template_sales',
                            'searchable' => 'false',
                            'orderable' => 'false',
                        ],
                        [
                            'label' => 'target vendor',
                            'data' => 'target_vendor',
                            'searchable' => 'false',
                            'orderable' => 'false',
                        ],
                        [
                            'label' => 'prefix name store',
                            'data' => 'prefix_name_store',
                            'searchable' => 'true',
                            'orderable' => 'false',
                        ],
                        [
                            'label' => 'status',
                            'data' => 'status_desc',
                            'searchable' => 'false',
                            'orderable' => 'false',
                        ]
                    ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="external-vendor/send-vendor/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Send Vendor">
    <x-form-horizontal>
        <x-row-horizontal label="Plant">
            <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?type=outlet" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Template Sales">
            <x-select :dom="$dom" compid="template_sales" type="serverside" url="external-vendor/template-sales/select" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Target Vendor">
            <x-select :dom="$dom" compid="target_vendor" type="serverside" url="external-vendor/target-vendor/select" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Prefix Name Store">
            <input type="text" class="form-control form-control-sm" id="prefix_name_store{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Status">
            <select class="form-control form-control-sm" id="status{{$dom}}">
                <option value="1">Active</option>
                <option value="0">Not Active</option>
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

<x-modal :dom="$dom" compid="modalsend" title="Send Vendor Manual">
    <x-form-horizontal>
        <x-row-horizontal label="Plant">
            <input type="text" class="form-control form-control-sm" id="splant{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Template Sales">
            <input type="text" class="form-control form-control-sm" id="stemplate_sales{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Target Vendor">
            <input type="text" class="form-control form-control-sm" id="starget_vendor{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Date">
            <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
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

<x-modal :dom="$dom" compid="modaldownload" title="Download file Sales">
    <x-form-horizontal>
        <x-row-horizontal label="Plant">
            <input type="text" class="form-control form-control-sm" id="dplant{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Template Sales">
            <input type="text" class="form-control form-control-sm" id="dtemplate_sales{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Date">
            <x-pickerdate :dom="$dom" compid="ddate" data-value="{{ date('Y/m/d') }}" clear="false"/>
        </x-row-horizontal>
        <x-row-horizontal label="File Type">
            @php
                $options = [
                            ['id' => 'xlsx', 'text' => 'XLSX'],
                            ['id' => 'csv', 'text' => 'CSV'],
                            ['id' => 'xls', 'text' => 'XLS'],
                            ['id' => 'txt', 'text' => 'TXT'],
                        ];
            @endphp
            <x-select :dom="$dom" compid="dfiletype" type="array" :options="$options" size="sm"/>
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.download()">
            <span>{{ __('Download') }}</span>
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
        save: "external-vendor/send-vendor",
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
        send: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check status
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.status != 1){
                message.info(" {{ __('Tax Not Active.') }} ");
                return false;
            }

            {{$dom}}.func.setSend();
        },
        download: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check status
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.status != 1){
                message.info(" {{ __('Data Not Active.') }} ");
                return false;
            }

            {{$dom}}.func.setDownload();
        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctplant{{$dom}}.clear();
            fslcttemplate_sales{{$dom}}.clear();
            fslcttarget_vendor{{$dom}}.clear();
            $("#prefix_name_store{{$dom}}").val('');
            $("#status{{$dom}}").val('1');
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslcttemplate_sales{{$dom}}.set(data.template_sale_id, data.template_sales);
            fslcttarget_vendor{{$dom}}.set(data.target_vendor_id, data.target_vendor);
            fslctplant{{$dom}}.set(data.plant_id, data.initital + ' ' + data.short_name);
            $("#prefix_name_store{{$dom}}").val(data.prefix_name_store);
            $("#status{{$dom}}").val(data.status);
            showModal('modalmanage{{$dom}}');
        },
        setSend: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#splant{{$dom}}").val(data.description);
            $("#stemplate_sales{{$dom}}").val(data.template_sales);
            $("#starget_vendor{{$dom}}").val(data.target_vendor);
            $("#sftp{{$dom}}").val(data.name);

            showModal('modalsend{{$dom}}');
        },
        setDownload: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#dplant{{$dom}}").val(data.description);
            $("#dtemplate_sales{{$dom}}").val(data.template_sales);

            showModal('modaldownload{{$dom}}');
        },
        getDataForm: function () {
            return {
                'template_sales': fslcttemplate_sales{{$dom}}.get(),
                'target_vendor': fslcttarget_vendor{{$dom}}.get(),
                'plant': fslctplant{{$dom}}.get(),
                'prefix_name_store': $("#prefix_name_store{{$dom}}").val(),
                'status': $("#status{{$dom}}").val(),
                'id': {{$dom}}.data.id
            }
        },
        getDataSendTax: function () {
            return {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'id': {{$dom}}.data.id
            }
        },
        getDataDownloadTax: function () {
            return {
                'date': pickerdateddate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'file_type': fslctdfiletype{{$dom}}.get(),
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
        send: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataSendTax();
            var url = {{$dom}}.url.save + '/send';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalsend{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        download: function () {
            var data = {{$dom}}.func.getDataDownloadTax();
            var url = {{$dom}}.url.save + '/download?id=' + data.id + '&date=' + data.date + '&file-type=' + data.file_type;
            window.open(url)
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
