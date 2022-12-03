<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @hasanyrole('store manager|area manager')
                    @role('store manager')
                    {{-- outlet --}}
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Send" icon="bx bx-send" :onclick="$dom. '.event.send()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject Send" icon="bx bx-reset" :onclick="$dom. '.event.rejectSend()'" />
                    </x-row-tools>
                    @if($mutation)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Accept" icon="bx bx-check" :onclick="$dom. '.event.accept()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject Accept" icon="bx bx-x" :onclick="$dom. '.event.rejectAccept()'" />
                    </x-row-tools>
                    @endif
                    @endrole
                    @role('area manager')
                    {{-- AM --}}
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Approve" icon="bx bx-check" :onclick="$dom. '.event.approve()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="UnApprove" icon="bx bx-x" :onclick="$dom. '.event.unapprove()'" />
                    </x-row-tools>
                    @endrole
                    @else
                    @if( $position == 'Head of Department' || $position == 'Regional Manager' )
                    {{-- HOD / RM --}}
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Approve Request" icon="bx bx-check" :onclick="$dom. '.event.approveRequest()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="UnApprove Request" icon="bx bx-x" :onclick="$dom. '.event.unapproveRequest()'" />
                    </x-row-tools>
                    @else

                    @if ( sizeof($user_validators) <= 0 )
                    {{-- admin department --}}
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Send" icon="bx bx-send" :onclick="$dom. '.event.send()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject Send" icon="bx bx-reset" :onclick="$dom. '.event.rejectSend()'" />
                    </x-row-tools>
                    @if($mutation)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Accept" icon="bx bx-check" :onclick="$dom. '.event.accept()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject Accept" icon="bx bx-x" :onclick="$dom. '.event.rejectAccept()'" />
                    </x-row-tools>
                    @endif
                    @else
                    {{-- validator --}}
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Confirmation Request" icon="bx bx-check" :onclick="$dom. '.event.confirmationValidator()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Assign Request" icon="bx bx-label" :onclick="$dom. '.event.assignValidator()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject Request" icon="bx bx-x" :onclick="$dom. '.event.rejectValidator()'" />
                    </x-row-tools>
                    @endif

                    @endif
                    @endhasanyrole
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @hasanyrole('superadmin|store manager|area manager')
                                @role('store manager')
                                {{-- outlet --}}
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.send()" ><i class="bx bx-send mr-50"></i>{{ __('Send') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.rejectSend()" ><i class="bx bx-reset mr-50"></i>{{ __('Reject Send') }}</a>
                                @if($mutation)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.accept()" ><i class="bx bx-check mr-50"></i>{{ __('Accept') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.rejectAccept()" ><i class="bx bx-x mr-50"></i>{{ __('Reject Accept') }}</a>
                                @endif
                                @endrole
                                @role('area manager')
                                {{-- AM --}}
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.approve()" ><i class="bx bx-check mr-50"></i>{{ __('Approve') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.unapprove()" ><i class="bx bx-x mr-50"></i>{{ __('UnApprove') }}</a>
                                @endrole
                                @else
                                @if( $position == 'Head of Department' || $position == 'Regional Manager' )
                                {{-- HOD / RM --}}
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.approveRequest()" ><i class="bx bx-check mr-50"></i>{{ __('Approve Request') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.unapproveRequest()" ><i class="bx bx-x mr-50"></i>{{ __('UnApprove Request') }}</a>
                                @else
                                {{-- validator --}}
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.confirmationValidator()" ><i class="bx bx-check mr-50"></i>{{ __('Confirmation Request') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.assignValidator()" ><i class="bx bx-label mr-50"></i>{{ __('Assign Request') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.rejectValidator()" ><i class="bx bx-x mr-50"></i>{{ __('Reject Request') }}</a>
                                @endif
                                @endhasanyrole
                                <a class="dropdown-item" href="javascript:void(0)" onclick=fdtbletabledata{{$dom}}.refresh()><i class="bx bx-revision mr-50"></i>{{ __('Refresh') }}</a>
                            </div>
                        </div>
                    </x-row-tools>
                </x-slot>
                <x-slot name="right">
                    @hasanyrole('store manager|area manager')
                    <x-row-tools>
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Plant">
                                        @role('store manager')
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
                                        @else
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&ext=all" size="sm" dropdowncompid="tabledata" :default="[0, __('All')]"/>
                                        @endrole
                                    </x-row-vertical>
                                </x-form-vertical>
                                <x-form-vertical>
                                    <x-row-vertical label="Type">
                                        <select class="form-control form-control-sm" id="ftype{{$dom}}">
                                            @role('store manager')
                                            <option value="all">{{ __('All') }}</option>
                                            <option value="confirmation_sender">Confirmation Sender</option>
                                            <option value="accepted_receiver">Accepted Receiver</option>
                                            @endrole
                                            @role('area manager')
                                            <option value="all">{{ __('All') }}</option>
                                            <option value="approval_sender">Approval Sender</option>
                                            <option value="approval_receiver">Approval Receiver</option>
                                            @endrole
                                        </select>
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
                    @endhasanyrole
                    <x-row-tools>
                        <x-dropdown-export :dom="$dom" dtblecompid="tabledata" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-input-search :dom="$dom" dtblecompid="tabledata" />
                    </x-row-tools>
                </x-slot>
            </x-tools>

            @hasanyrole('store manager|area manager')
            @php
                $columns =
                    [[
                        'label' => 'type',
                        'data' => 'type',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
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
                        'label' => 'est Transfer date',
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
                        'label' => 'condition asset Transfer',
                        'data' => 'condition_send',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'note request',
                        'data' => 'note_request',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            @role('store manager')
            @php
            $url = 'financeacc/asset/mutation/dtble?plant_id=' . $first_plant_id . '&type=all';
            @endphp
            @else
            @php
            $url = 'financeacc/asset/mutation/dtble?plant_id=0&type=all';
            @endphp
            @endrole
            @else
            @php
                $columns =
                    [[
                        'label' => 'type',
                        'data' => 'type',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
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
                        'label' => 'est Transfer date',
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
                        'label' => 'request note',
                        'data' => 'note_request',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];

                $url = 'financeacc/asset/mutation/dtble';
            @endphp
            @endhasanyrole
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" :url="$url" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Accept Asset Transfer" size="lg">
    <x-form-horizontal>
        <input type="text" id="acqty_web{{$dom}}" hidden>

        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="acrequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="acplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="accost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="acdate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="acplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="accost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="acvalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="acest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="PIC Sender">
                    <input type="text" class="form-control form-control-sm" id="acpic_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="acnote_request{{$dom}}" rows="2" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="acasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="acasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="acasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="acspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="acqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="acuom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Confirmation Accept Asset Transfer')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Condition Asset Receive">
                    <select class="form-control form-control-sm" id="accondition_asset_receive{{$dom}}">
                        <option value="Good Condition">{{ __('Good Condition') }}</option>
                        <option value="Bad Condition">{{ __('Bad Condition') }}</option>
                    </select>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="PIC Receiver">
                    <input type="text" class="form-control form-control-sm" id="acpic_receiver{{$dom}}">
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.accept()">
            <span>{{ __('Accept Receiver Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalunapproverequest" title="UnApprove Request Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="urequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="uplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="ucost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="udate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="uplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="ucost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="uvalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="uest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="unote_request{{$dom}}" rows="5" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="uasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="uasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="uasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="uspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="uqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="uuom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="Reason Rejected">
                    <textarea class="form-control form-control-sm" id="ureason_rejected{{$dom}}" rows="3"></textarea>
                </x-row-vertical>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.unapproveRequest()">
            <span>{{ __('UnApprove Request Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalassign" title="Assign Request to Validator" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="arequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="aplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="acost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="adate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="aplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="acost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="avalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="aest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="anote_request{{$dom}}" rows="5" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="aasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="aasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="aasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="aspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="aqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="auom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Assign Validator Asset Transfer')" class="mt-0" />
            </div>
            <div class="col-12">
                <x-row-vertical label="Assign Validator">
                    <x-select :dom="$dom" compid="avalidator" type="serverside" url="financeacc/asset/validator/select" size="sm"/>
                </x-row-vertical>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.assignValidator()">
            <span>{{ __('Assign validator') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalconfirmation" title="Confirmation Request" size="lg">
    <x-form-horizontal>
        {{-- hidden input --}}
        <input type="text" id="cqty_web{{$dom}}" hidden>

        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="crequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="cplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="ccost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="cdate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="cplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="ccost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="cvalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="cest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="cnote_request{{$dom}}" rows="5" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="casset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="casset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="casset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="cspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="cqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="cuom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 text-center">
                <button type="button" class="btn btn-primary mb-1 mt-1 btn-sm" onclick="{{$dom}}.event.changeAssetRequest()">
                    <span>{{ __('Change Asset') }}</span>
                </button>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.confirmationValidator()">
            <span>{{ __('Confirmation Validator') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalrejectvalidator" title="Reject Request Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="rvrequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="rvplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="rvcost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="rvdate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="rvplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="rvcost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="rvvalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="rvest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="rvnote_request{{$dom}}" rows="5" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="rvasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="rvasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="rvasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="rvspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="rvqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="rvuom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="Reason Rejected">
                    <textarea class="form-control form-control-sm" id="rvreason_rejected{{$dom}}" rows="3"></textarea>
                </x-row-vertical>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.rejectValidatorRequest()">
            <span>{{ __('Reject Request Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalunapprove" title="Reject Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="uarequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="uaplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="uacost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="uadate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="uaplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="uacost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="uavalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="uaest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="uanote_request{{$dom}}" rows="5" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="uaasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="uaasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="uaasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="uaspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="uaqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="uauom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="Reason Rejected">
                    <textarea class="form-control form-control-sm" id="uareason_rejected{{$dom}}" rows="3"></textarea>
                </x-row-vertical>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.unapprove()">
            <span>{{ __('Reject Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalsend" title="Confirmation Sender" size="lg">
    <x-form-horizontal>
        {{-- hidden input --}}
        <input type="text" id="sqty_web{{$dom}}" hidden>

        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="srequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="splant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="scost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="sdate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="splant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="scost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="svalidator{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="snote_request{{$dom}}" rows="2" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="sasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="sasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="sasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="sspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="sqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="suom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Confirmation Sender Asset Transfer')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="PIC Sender">
                    <input type="text" class="form-control form-control-sm" id="spic_sender{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <x-pickerdate :dom="$dom" compid="sest_send_date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Condition Asset Transfer">
                    <select class="form-control form-control-sm" id="scondition_asset_send{{$dom}}">
                        <option value="Good Condition">{{ __('Good Condition') }}</option>
                        <option value="Bad Condition">{{ __('Bad Condition') }}</option>
                    </select>
                </x-row-horizontal>
                <x-row-horizontal label="Remark">
                    <textarea class="form-control form-control-sm" id="sremark{{$dom}}" rows="2"></textarea>
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.send()">
            <span>{{ __('Confirmation Send Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalrejectsend" title="Reject Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="rsrequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="rsplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="rscost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="rsdate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="rsplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="rscost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="rsvalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="rsest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="rsnote_request{{$dom}}" rows="5" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="rsasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="rsasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="rsasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="rsspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="rsqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="rsuom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="Reason Rejected">
                    <textarea class="form-control form-control-sm" id="rsreason_rejected{{$dom}}" rows="3"></textarea>
                </x-row-vertical>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.rejectSend()">
            <span>{{ __('Reject Send Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalrejectaccept" title="Reject Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Requestor">
                    <input type="text" class="form-control form-control-sm" id="rarequestor{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="raplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="racost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date Request">
                    <input type="text" class="form-control form-control-sm" id="radate_request{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="raplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="racost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="ravalidator{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Est Transfer Date">
                    <input type="text" class="form-control form-control-sm" id="raest_send_date{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="ranote_request{{$dom}}" rows="5" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="raasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="raasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="raasset_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="raspec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="raqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="rauom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="Reason Rejected">
                    <textarea class="form-control form-control-sm" id="rareason_rejected{{$dom}}" rows="3"></textarea>
                </x-row-vertical>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.rejectAccept()">
            <span>{{ __('Reject Accept Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalreject" title="Reject Asset" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Asset Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="rplant{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center">
                    <input type="text" class="form-control form-control-sm" id="rcost_center{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="rspec_user{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="rnumber{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Sub Number">
                    <input type="text" class="form-control form-control-sm" id="rsub_number{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Description">
                    <input type="text" class="form-control form-control-sm" id="rdescription{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="text" class="form-control form-control-sm" id="rqty_mutation{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Uom">
                    <input type="text" class="form-control form-control-sm" id="ruom{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer')" class="mt-0" />
            </div>
            <div class="col-12 col-md-12">
                <x-row-horizontal label="Note Reject">
                    <textarea class="form-control form-control-sm" id="reason{{$dom}}" rows="3"></textarea>
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.unapprove()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

{{-- data reference asset --}}
@php
    $columns =
        [
            [
                'label' => 'Status Transfer',
                'data' => 'status_mutation_desc',
                'searchable' => 'false',
                'orderable' => 'false',
            ],[
                'label' => 'asset number',
                'data' => 'number',
                'searchable' => 'true',
                'orderable' => 'true',
            ],[
                'label' => 'sub number',
                'data' => 'number_sub',
                'searchable' => 'false',
                'orderable' => 'false',
            ],[
                'label' => 'asset name',
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
            ]
        ];

        $urlRefAsset = 'financeacc/asset/list/dtble';
    @endphp
@endphp
<x-data-reference :dom="$dom" compid="ref_asset" title="Data Asset" size="full" :url="$urlRefAsset" :columns="$columns" height="450"></x-data-reference>
<!-- end modal -->

<script>
// event double click data reference asset
function callbacktableref_asset{{$dom}}(data) {
    if(data.qty_web < 1){
        message.info(" {{ __('Qty of this asset is zero, cannot be transferred.') }} ");
        return false;
    }

    if(data.status_request == 0){
        message.info(" {{ __('This asset is in the process of being request, cannot be transferred.') }} ");
        return false;
    }

    var dataCheck = {
        'number' : data.number,
        'number_sub' : data.number_sub,
        'plant_id' : data.plant_id,
    };

    var url = "financeacc/asset/list/check";

    $.post( url, dataCheck, function (res) {
        var check = false;
        if( res.status == 'success' ){
            check = res.data.check;
        }

        if(check){
            message.info(" {{ __('This asset is in the process of being transferred, cannot be requested.') }} ");
            return false;
        }

        // available for mutation
        $("#casset_number{{$dom}}").val(data.number);
        $("#casset_sub_number{{$dom}}").val(data.number_sub);
        $("#casset_name{{$dom}}").val(data.description);
        $("#cspec_user{{$dom}}").val(data.spec_user);
        $("#cremark{{$dom}}").val(data.remark);
        $("#cuom{{$dom}}").val(data.uom);
        $("#cqty_web{{$dom}}").val(data.qty_web);

        hideModal('modalref_asset{{$dom}}');

    }, 'json');

}
{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "financeacc/asset/mutation",
        refAsset: "{{$urlRefAsset}}",
    },
    event: {
        edit: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        approveRequest: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been approved cannot be canceled.",
                            "{{$dom}}.func.approveRequest");

        },
        unapproveRequest: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been unapproved cannot be canceled.",
                            "{{$dom}}.event.showUnApproveRequest");

        },
        showUnApproveRequest: function () {
            {{$dom}}.func.setUnApproveRequest();
            showModal('modalunapproverequest{{$dom}}');
        },
        assignValidator: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.setAssignValidator();
        },
        changeAssetRequest: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var url = {{$dom}}.url.refAsset + '?plant_id=' + data.from_plant_id + '&cost_center_code=' + data.from_cost_center_code;
            fdtbletableref_asset{{$dom}}.changeUrl(url);

            showModal('modalref_asset{{$dom}}');
        },
        confirmationValidator: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.setComfirmValidator();
        },
        rejectValidator: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been rejected cannot be canceled.",
                            "{{$dom}}.event.showRejectValidator");

        },
        showRejectValidator: function () {
            {{$dom}}.func.setRejectValidator();
            showModal('modalrejectvalidator{{$dom}}');
        },
        approve: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been approved cannot be canceled.",
                            "{{$dom}}.func.approve");

        },
        unapprove: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been unapproved cannot be canceled.",
                            "{{$dom}}.event.showUnapprove");

        },
        showUnapprove: function () {
            {{$dom}}.func.setUnApprove();
            showModal('modalunapprove{{$dom}}');
        },
        send: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.type != "Confirmation Sender"){
                message.info(" {{ __('This transaction cannot confirmation sender.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been confirm send cannot be canceled.",
                            "{{$dom}}.event.showSend");
        },
        rejectSend: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been rejected cannot be canceled.",
                            "{{$dom}}.event.showRejectSend");

        },
        showRejectSend: function () {
            {{$dom}}.func.setRejectSend();
            showModal('modalrejectsend{{$dom}}');
        },
        rejectAccept: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been rejected cannot be canceled.",
                            "{{$dom}}.event.showRejectAccept");

        },
        showRejectAccept: function () {
            {{$dom}}.func.setRejectAccept();
            showModal('modalrejectaccept{{$dom}}');
        },
        showSend: function () {
            {{$dom}}.func.setSend();
            showModal('modalsend{{$dom}}');
        },
        accept: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.type != "Accepted Receiver"){
                message.info(" {{ __('This transaction cannot accept receiver.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been accepted cannot be canceled.",
                            "{{$dom}}.event.showAccept");

        },
        reject: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been rejected cannot be canceled.",
                            "{{$dom}}.event.showReject");

        },
        showAccept: function () {
            {{$dom}}.func.set();
            showModal('modalmanage{{$dom}}');
        },
        showReject: function () {
            {{$dom}}.func.setReject();
            showModal('modalreject{{$dom}}');
        }
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.save + '/dtble?plant_id=' + $("#select2fplant{{$dom}}").val() + '&type=' + $("#ftype{{$dom}}").val() ;
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#acplant_sender{{$dom}}").val(data.plant_from);
            $("#acplant_receiver{{$dom}}").val(data.plant_to);
            $("#accost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#accost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#acasset_number{{$dom}}").val(data.number);
            $("#acasset_sub_number{{$dom}}").val(data.number_sub);
            $("#acasset_name{{$dom}}").val(data.description);
            $("#acspec_user{{$dom}}").val(data.spec_user);
            $("#acest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#acqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#acnote_request{{$dom}}").val(data.note_request);
            $("#acvalidator{{$dom}}").val(data.validator);
            $("#acrequestor{{$dom}}").val(data.requestor_desc);
            $("#acdate_request{{$dom}}").val(data.date_request_desc);
            $("#acuom{{$dom}}").val(data.uom);
            $("#acqty_web{{$dom}}").val(data.qty_web);
            $("#acpic_sender{{$dom}}").val(data.pic_sender);
            $("#accondition_asset_receive{{$dom}}").val('');
            $("#acpic_receiver{{$dom}}").val('');
        },
        setUnApproveRequest: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#uplant_sender{{$dom}}").val(data.plant_from);
            $("#uplant_receiver{{$dom}}").val(data.plant_to);
            $("#ucost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#ucost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#uasset_number{{$dom}}").val(data.number);
            $("#uasset_sub_number{{$dom}}").val(data.number_sub);
            $("#uasset_name{{$dom}}").val(data.description);
            $("#uspec_user{{$dom}}").val(data.spec_user);
            $("#uqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#unote_request{{$dom}}").val(data.note_request);
            $("#uvalidator{{$dom}}").val(data.validator);
            $("#uest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#urequestor{{$dom}}").val(data.requestor_desc);
            $("#udate_request{{$dom}}").val(data.date_request_desc);
            $("#uuom{{$dom}}").val(data.uom);

            $("#ureason_rejected{{$dom}}").val('');
        },
        setRejectValidator: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#rvplant_sender{{$dom}}").val(data.plant_from);
            $("#rvplant_receiver{{$dom}}").val(data.plant_to);
            $("#rvcost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#rvcost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#rvasset_number{{$dom}}").val(data.number);
            $("#rvasset_sub_number{{$dom}}").val(data.number_sub);
            $("#rvasset_name{{$dom}}").val(data.description);
            $("#rvspec_user{{$dom}}").val(data.spec_user);
            $("#rvqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#rvnote_request{{$dom}}").val(data.note_request);
            $("#rvvalidator{{$dom}}").val(data.validator);
            $("#rvest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#rvrequestor{{$dom}}").val(data.requestor_desc);
            $("#rvdate_request{{$dom}}").val(data.date_request_desc);
            $("#rvuom{{$dom}}").val(data.uom);

            $("#rvreason_rejected{{$dom}}").val('');
        },
        setAssignValidator: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#aplant_sender{{$dom}}").val(data.plant_from);
            $("#aplant_receiver{{$dom}}").val(data.plant_to);
            $("#acost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#acost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#aasset_number{{$dom}}").val(data.number);
            $("#aasset_sub_number{{$dom}}").val(data.number_sub);
            $("#aasset_name{{$dom}}").val(data.description);
            $("#aspec_user{{$dom}}").val(data.spec_user);
            $("#aqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#anote_request{{$dom}}").val(data.note_request);
            $("#avalidator{{$dom}}").val(data.validator);
            $("#aest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#arequestor{{$dom}}").val(data.requestor_desc);
            $("#adate_request{{$dom}}").val(data.date_request_desc);
            $("#auom{{$dom}}").val(data.uom);

            fslctavalidator{{$dom}}.clear();

            showModal('modalassign{{$dom}}');
        },
        setComfirmValidator: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#cplant_sender{{$dom}}").val(data.plant_from);
            $("#cplant_receiver{{$dom}}").val(data.plant_to);
            $("#ccost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#ccost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#casset_number{{$dom}}").val(data.number);
            $("#casset_sub_number{{$dom}}").val(data.number_sub);
            $("#casset_name{{$dom}}").val(data.description);
            $("#cspec_user{{$dom}}").val(data.spec_user);
            $("#cqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#cnote_request{{$dom}}").val(data.note_request);
            $("#cvalidator{{$dom}}").val(data.validator);
            $("#cest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#crequestor{{$dom}}").val(data.requestor_desc);
            $("#cdate_request{{$dom}}").val(data.date_request_desc);
            $("#cuom{{$dom}}").val(data.uom);
            $("#cqty_web{{$dom}}").val(data.qty_web);

            showModal('modalconfirmation{{$dom}}');
        },
        setUnApprove: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#uaplant_sender{{$dom}}").val(data.plant_from);
            $("#uaplant_receiver{{$dom}}").val(data.plant_to);
            $("#uacost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#uacost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#uaasset_number{{$dom}}").val(data.number);
            $("#uaasset_sub_number{{$dom}}").val(data.number_sub);
            $("#uaasset_name{{$dom}}").val(data.description);
            $("#uaspec_user{{$dom}}").val(data.spec_user);
            $("#uaqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#uanote_request{{$dom}}").val(data.note_request);
            $("#uavalidator{{$dom}}").val(data.validator);
            $("#uaest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#uarequestor{{$dom}}").val(data.requestor_desc);
            $("#uadate_request{{$dom}}").val(data.date_request_desc);
            $("#uauom{{$dom}}").val(data.uom);
            $("#uareason_rejected{{$dom}}").val('');
        },
        setSend: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#splant_sender{{$dom}}").val(data.plant_from);
            $("#splant_receiver{{$dom}}").val(data.plant_to);
            $("#scost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#scost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#sasset_number{{$dom}}").val(data.number);
            $("#sasset_sub_number{{$dom}}").val(data.number_sub);
            $("#sasset_name{{$dom}}").val(data.description);
            $("#sspec_user{{$dom}}").val(data.spec_user);
            $("#sqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#snote_request{{$dom}}").val(data.note_request);
            $("#svalidator{{$dom}}").val(data.validator);
            $("#srequestor{{$dom}}").val(data.requestor_desc);
            $("#sdate_request{{$dom}}").val(data.date_request_desc);
            $("#suom{{$dom}}").val(data.uom);
            $("#sqty_web{{$dom}}").val(data.qty_web);
            $("#sremark{{$dom}}").val('');
            $("#spic_sender{{$dom}}").val('');
            $("#scondition_asset_send{{$dom}}").val('');
            pickerdatesest_send_date{{$dom}}.set('select', data.date_send_est, { format: 'yyyy-mm-dd' });

            showModal('modalsend{{$dom}}');
        },
        setRejectSend: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#rsplant_sender{{$dom}}").val(data.plant_from);
            $("#rsplant_receiver{{$dom}}").val(data.plant_to);
            $("#rscost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#rscost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#rsasset_number{{$dom}}").val(data.number);
            $("#rsasset_sub_number{{$dom}}").val(data.number_sub);
            $("#rsasset_name{{$dom}}").val(data.description);
            $("#rsspec_user{{$dom}}").val(data.spec_user);
            $("#rsqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#rsnote_request{{$dom}}").val(data.note_request);
            $("#rsvalidator{{$dom}}").val(data.validator);
            $("#rsest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#rsrequestor{{$dom}}").val(data.requestor_desc);
            $("#rsdate_request{{$dom}}").val(data.date_request_desc);
            $("#rsuom{{$dom}}").val(data.uom);
            $("#rsreason_rejected{{$dom}}").val('');
        },
        setRejectAccept: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#raplant_sender{{$dom}}").val(data.plant_from);
            $("#raplant_receiver{{$dom}}").val(data.plant_to);
            $("#racost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#racost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#raasset_number{{$dom}}").val(data.number);
            $("#raasset_sub_number{{$dom}}").val(data.number_sub);
            $("#raasset_name{{$dom}}").val(data.description);
            $("#raspec_user{{$dom}}").val(data.spec_user);
            $("#raqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#ranote_request{{$dom}}").val(data.note_request);
            $("#ravalidator{{$dom}}").val(data.validator);
            $("#raest_send_date{{$dom}}").val(data.date_send_est_desc);
            $("#rarequestor{{$dom}}").val(data.requestor_desc);
            $("#radate_request{{$dom}}").val(data.date_request_desc);
            $("#rauom{{$dom}}").val(data.uom);
            $("#rareason_rejected{{$dom}}").val('');
        },
        setReject: function () {
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            data = row[0];

            $("#rplant{{$dom}}").val(data.from_plant_initital + ' ' + data.from_plant_name);
            $("#rcost_center{{$dom}}").val(data.from_cost_center_desc);
            $("#rspec_user{{$dom}}").val(data.spec_user);
            $("#rremark{{$dom}}").val(data.remark);
            $("#rnumber{{$dom}}").val(data.number);
            $("#rsub_number{{$dom}}").val(data.number_sub);
            $("#rdescription{{$dom}}").val(data.description);
            $("#rqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#ruom{{$dom}}").val(data.uom);

            $("#reason{{$dom}}").val('');
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
        approveRequest: function () {
            hideErrors();
            loading('start', '{{ __("Approve Asset Transfer Request") }}', 'process');

            var row = fdtbletabledata{{$dom}}.getSelectedData();
            var data = row[0];

            var data = {
                'id': data.id
            };

            var url = {{$dom}}.url.save + '/approve-request';

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        unapproveRequest: function () {
            console.log("masok");
            hideErrors();
            loadingModal('start');

            var data = {
                'reason_rejected': $("#ureason_rejected{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/unapprove-request';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalunapproverequest{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        assignValidator: function () {
            hideErrors();

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var newValidor = fslctavalidator{{$dom}}.get();

            if( data.asset_validator_id == newValidor ){
                message.info(" {{ __('The assign validator is the same as the current validator') }} ");
                return false;
            }

            loadingModal('start');

            var data = {
                'assign_validator': newValidor,
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/assign';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalassign{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        confirmationValidator: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'number': $("#casset_number{{$dom}}").val(),
                'number_sub': $("#casset_sub_number{{$dom}}").val(),
                'description': $("#casset_name{{$dom}}").val(),
                'spec_user': $("#cspec_user{{$dom}}").val(),
                'uom': $("#cuom{{$dom}}").val(),
                'qty_web': $("#cqty_web{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/confirmation';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalconfirmation{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        rejectValidatorRequest: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'reason_rejected': $("#rvreason_rejected{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/reject-validator';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalrejectvalidator{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        approve: function () {
            hideErrors();
            loading('start', '{{ __("Approve Asset Transfer") }}', 'process');

            var row = fdtbletabledata{{$dom}}.getSelectedData();
            var data = row[0];

            var data = {
                'id': data.id
            };

            var url = {{$dom}}.url.save + '/approve-am';

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        unapprove: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'reason_rejected': $("#uareason_rejected{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/unapprove-am';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalunapprove{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        send: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'remark': $("#sremark{{$dom}}").val(),
                'pic_sender': $("#spic_sender{{$dom}}").val(),
                'condition_asset_send': $("#scondition_asset_send{{$dom}}").val(),
                'est_send_date': pickerdatesest_send_date{{$dom}}.get('select', 'yyyy/mm/dd'),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/send';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){

                    var row = fdtbletabledata{{$dom}}.getSelectedData();
                    var data = row[0];
                    window.open('financeacc/asset/mutation/preview?number=' + data.number + '&sub=' + data.number_sub + '&plant_id=' + data.from_plant_id);

                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalsend{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        rejectSend: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'reason_rejected': $("#rsreason_rejected{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/reject-send';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalrejectsend{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        accept: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'pic_receiver': $("#acpic_receiver{{$dom}}").val(),
                'condition_asset_receive': $("#accondition_asset_receive{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/accept';

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
        rejectAccept: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'reason_rejected': $("#rareason_rejected{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/reject-accept';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalrejectaccept{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
    }
}

</script>
