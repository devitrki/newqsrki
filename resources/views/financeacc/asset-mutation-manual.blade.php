<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Confirm" icon="bx bx-check" :onclick="$dom. '.event.confirm()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.confirm()" ><i class="bx bx-check mr-50"></i>{{ __('Confirm') }}</a>
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
                        'label' => 'asset number',
                        'data' => 'number',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'sub number',
                        'data' => 'number_sub',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'description',
                        'data' => 'description',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'spec / user',
                        'data' => 'spec_user',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'remark',
                        'data' => 'remark',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'qty transfer',
                        'data' => 'qty_mutation',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'uom',
                        'data' => 'uom',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant sender',
                        'data' => 'plant_from',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'cost center sender',
                        'data' => 'from_cost_center_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic sender',
                        'data' => 'pic_sender',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant receiver',
                        'data' => 'plant_to',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'cost center receiver',
                        'data' => 'to_cost_center_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'requestor',
                        'data' => 'requestor_desc',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'validator',
                        'data' => 'validator',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approver 1',
                        'data' => 'approver1',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approver 2',
                        'data' => 'approver2',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'est transfer date',
                        'data' => 'date_send_est_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'request date',
                        'data' => 'date_request_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approve 1 date',
                        'data' => 'date_approve_first_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'confirm validator date',
                        'data' => 'date_confirmation_validator_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approve 2 date',
                        'data' => 'date_approve_second_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'confirm sender date',
                        'data' => 'date_confirmation_sender_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'condition asset transfer',
                        'data' => 'condition_send',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'accepted receiver date',
                        'data' => 'date_accept_receiver_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'condition asset receive',
                        'data' => 'condition_receive',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'note request',
                        'data' => 'note_request',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="financeacc/asset/mutationmanual/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<!-- end modal -->

<script>
{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "financeacc/asset/mutationmanual",
    },
    event: {
        confirm: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been confirmed cannot be restored.",
                            "{{$dom}}.func.confirm");

        },
    },
    func: {
        confirm: function () {
            loading('start', '{{ __("Confirm") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/confirm/' + row[0].id;

            $.get(url, function (res) {
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
