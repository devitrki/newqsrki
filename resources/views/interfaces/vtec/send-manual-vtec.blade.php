<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Send Manual to SAP" icon="bx bx-send" :onclick="$dom. '.event.sendSap()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Send Manual to Data Warehouse" icon="bx bx-data" :onclick="$dom. '.event.sendWH()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="View Format SAP" icon="bx bx-show-alt" :onclick="$dom. '.event.view()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.sendSap()" ><i class="bx bx-send mr-50"></i>{{ __('Send Manual to SAP') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.sendWH()" ><i class="bx bx-data mr-50"></i>{{ __('Send Manual to Data Warehouse') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.view()" ><i class="bx bx-show-alt mr-50"></i>{{ __('View Format SAP') }}</a>
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
                                    <x-row-vertical label="Store">
                                        <x-select :dom="$dom" compid="fstore" type="serverside" url="master/plant/select?type=outlet&ext=all" size="sm" dropdowncompid="tabledata" :default="[0, __('All')]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="From">
                                        <x-pickerdate :dom="$dom" compid="ffrom" data-value="{{ date('Y/m/d', strtotime('-1 days')) }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Until">
                                        <x-pickerdate :dom="$dom" compid="funtil" data-value="{{ date('Y/m/d', strtotime('-1 days')) }}" clear="false" />
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
                            'label' => 'date',
                            'data' => 'date',
                        ],
                        [
                            'label' => 'store code',
                            'data' => 'customer_code',
                        ],
                        [
                            'label' => 'store name',
                            'data' => 'plant',
                        ],
                        [
                            'label' => 'status sap',
                            'data' => 'status_sap',
                        ],
                        [
                            'label' => 'status warehouse',
                            'data' => 'status_warehouse',
                        ]
                    ];
                $url = "interfaces/vtec/send-manual-vtec/dtble?from=" . date('Y/m/d',strtotime("-1 days")) . "&until=" . date('Y/m/d',strtotime("-1 days")) . '&plant-id=0';
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" :url="$url" :select="[true, 'multi']" />
        </div>
    </div>
</x-card-scroll>

<script>
{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "interfaces/vtec/send-manual-vtec",
        dtble: "interfaces/vtec/send-manual-vtec/dtble",
    },
    event: {
        view: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            if( rows > 1  ){
                message.info(" {{ __('Selected Data Cannot Be More Than One') }} ");
                return false;
            }
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var url = {{$dom}}.url.save + '/view?date=' + data.SaleDate + '&plant-id=' + data.plant_id
            window.open(url)

        },
        sendSap: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure send to SAP?",
                            "Data that has been sended cannot be canceled.",
                            "{{$dom}}.func.sendSap");

        },
        sendWH: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure send to Data Warehouse?",
                            "Data that has been sended cannot be canceled.",
                            "{{$dom}}.func.sendWH");

        },
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.dtble + '?plant-id=' + fslctfstore{{$dom}}.get() + '&from=' + pickerdateffrom{{$dom}}.get('select', 'yyyy/mm/dd') + '&until=' + pickerdatefuntil{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        sendSap: function () {
            loading('start', '{{ __("Send to SAP") }}', 'process');

            var rows = fdtbletabledata{{$dom}}.getSelectedData();
            var url = {{$dom}}.url.save;

            var datas = [];
            for (let i = 0; i < rows.length; i++) {
                var data = {
                    'plant_id': rows[i].plant_id,
                    'date': rows[i].SaleDate
                };
                datas.push(data);
            }

            dataPost = {
                'data' : JSON.stringify(datas),
                'send' : 1
            };

            $.post( url, dataPost, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        sendWH: function () {
            loading('start', '{{ __("Send to Data Warehouse") }}', 'process');

            var rows = fdtbletabledata{{$dom}}.getSelectedData();
            var url = {{$dom}}.url.save;

            var datas = [];
            for (let i = 0; i < rows.length; i++) {
                var data = {
                    'plant_id': rows[i].plant_id,
                    'date': rows[i].SaleDate
                };
                datas.push(data);
            }

            dataPost = {
                'data' : JSON.stringify(datas),
                'send' : 2
            };

            $.post( url, dataPost, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        }
    }
}

</script>
