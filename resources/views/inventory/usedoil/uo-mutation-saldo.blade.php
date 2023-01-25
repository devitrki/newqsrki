<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Mutation Saldo" icon="bx bx-plus-circle" :onclick="$dom. '.event.mutation()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.mutation()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Mutation Saldo') }}</a>
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
                                    <x-row-vertical label="From">
                                        <x-pickerdate :dom="$dom" compid="ffrom" data-value="{{ date('Y/m/d', strtotime('-30 days')) }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Until">
                                        <x-pickerdate :dom="$dom" compid="funtil" data-value="{{ date('Y/m/d') }}" clear="false" />
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
                    [[
                        'label' => 'mutation date',
                        'data' => 'date',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'date',
                    ],[
                        'label' => 'Vendor Sender',
                        'data' => 'vendor_sender_name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Vendor Receiver',
                        'data' => 'vendor_receiver_name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Nominal',
                        'data' => 'nominal_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Description',
                        'data' => 'description',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/usedoil/uo-mutation-saldo/dtble?from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Mutation Saldo Vendor">
    <x-form-horizontal>
        <x-row-horizontal label="Vendor Sender">
            <x-select :dom="$dom" compid="vendor_sender" type="serverside" url="inventory/usedoil/uo-vendor/select" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Vendor Receiver">
            <x-select :dom="$dom" compid="vendor_receiver" type="serverside" url="inventory/usedoil/uo-vendor/select" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Nominal">
            <input type="number" class="form-control form-control-sm" id="nominal{{$dom}}">
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
<!-- end modal -->

<script>
{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "inventory/usedoil/uo-mutation-saldo",
        datatable: "inventory/usedoil/uo-mutation-saldo/dtble"
    },
    event: {
        mutation: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        }
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?from=' + pickerdateffrom{{$dom}}.get('select', 'yyyy/mm/dd') + '&until=' + pickerdatefuntil{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctvendor_sender{{$dom}}.clear();
            fslctvendor_receiver{{$dom}}.clear();
            $("#nominal{{$dom}}").val('');
            $("#description{{$dom}}").val('');
        },
        getDataForm: function () {
            return {
                'vendor_sender': fslctvendor_sender{{$dom}}.get(),
                'vendor_receiver': fslctvendor_receiver{{$dom}}.get(),
                'nominal': $("#nominal{{$dom}}").val(),
                'description': $("#description{{$dom}}").val(),
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
        }
    }
}

</script>
