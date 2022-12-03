<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('u'.$menu_id)
                    @role('store manager')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Send Asset Mutation" icon="bx bx-send" :onclick="$dom. '.event.sendAssetMutation()'" />
                    </x-row-tools>
                    @elseif( $position == 'Head of Department' || $position == 'Regional Manager' )
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Approve Request" icon="bx bx-check" :onclick="$dom. '.event.approve()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="UnApprove Request" icon="bx bx-x" :onclick="$dom. '.event.unapprove()'" />
                    </x-row-tools>
                    @else
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Confirmation Request" icon="bx bx-check" :onclick="$dom. '.event.confirmationValidator()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Assign Request" icon="bx bx-label" :onclick="$dom. '.event.assignValidator()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject Request" icon="bx bx-x" :onclick="$dom. '.event.rejectValidator()'" />
                    </x-row-tools>
                    @endrole
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
                                @role('store manager')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.sendAssetMutation()" ><i class="bx bx-send mr-50"></i>{{ __('Send Asset Mutation') }}</a>
                                @elseif( $position == 'Head of Department' || $position == 'Regional Manager' )
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.approve()" ><i class="bx bx-check mr-50"></i>{{ __('Approve Request') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.unapprove()" ><i class="bx bx-x mr-50"></i>{{ __('UnApprove Request') }}</a>
                                @else
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.confirmationValidator()" ><i class="bx bx-check mr-50"></i>{{ __('Confirmation Validator') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.assignValidator()" ><i class="bx bx-label mr-50"></i>{{ __('Assign Validator') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.rejectValidator()" ><i class="bx bx-x mr-50"></i>{{ __('Reject Validator') }}</a>
                                @endrole
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
                $columns =
                    [[
                        'label' => 'asset name',
                        'data' => 'description',
                    ],[
                        'label' => 'asset number',
                        'data' => 'number',
                    ],[
                        'label' => 'asset sub number',
                        'data' => 'number_sub',
                    ],[
                        'label' => 'spec / user',
                        'data' => 'spec_user',
                    ],[
                        'label' => 'remark',
                        'data' => 'remark',
                    ],[
                        'label' => 'qty',
                        'data' => 'qty_mutation',
                    ],[
                        'label' => 'uom',
                        'data' => 'uom',
                    ],[
                        'label' => 'Plant Sender',
                        'data' => 'plant_sender',
                    ],[
                        'label' => 'Cost Center Sender',
                        'data' => 'from_cost_center',
                    ],[
                        'label' => 'Plant Receiver',
                        'data' => 'plant_receiver',
                    ],[
                        'label' => 'Cost Center Receiver',
                        'data' => 'to_cost_center',
                    ],[
                        'label' => 'Validator',
                        'data' => 'validator',
                    ],[
                        'label' => 'Request Date',
                        'data' => 'date_submit_desc',
                    ],[
                        'label' => 'Approval Date',
                        'data' => 'date_approval_desc',
                    ],[
                        'label' => 'Comfirm Validator Date',
                        'data' => 'date_confirmation_validator_desc',
                    ],[
                        'label' => 'Send Date',
                        'data' => 'date_send_desc',
                    ],[
                        'label' => 'request by',
                        'data' => 'request_by',
                    ],[
                        'label' => 'request note',
                        'data' => 'note_request',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="financeacc/asset/request/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalassign" title="Assign Request to Validator" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Mutation Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="aplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="acost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
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
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="anote_request{{$dom}}" rows="3" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Mutation Description')" class="mt-0" />
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
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="aspec_user{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Qty">
                    <input type="text" class="form-control form-control-sm" id="aqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="auom{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Remark">
                    <textarea class="form-control form-control-sm" id="aremark{{$dom}}" readonly rows="5"></textarea>
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
            <span>{{ __('Assign') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalconfirmation" title="Confirmation Request" size="lg">
    <x-form-horizontal>
        {{-- hidden input --}}
        <input type="text" id="cqty_web{{$dom}}" hidden>

        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Mutation Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="cplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="ccost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
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
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="cnote_request{{$dom}}" rows="3" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Mutation Description')" class="mt-0" />
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
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="cspec_user{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Qty">
                    <input type="text" class="form-control form-control-sm" id="cqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="cuom{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Remark">
                    <textarea class="form-control form-control-sm" id="cremark{{$dom}}" rows="5"></textarea>
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
            <span>{{ __('Confirmation') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalsend" title="Confirmation Send Request Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Transfer Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="splant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="scost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
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
                    <textarea class="form-control form-control-sm" id="snote_request{{$dom}}" rows="3" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Mutation Description')" class="mt-0" />
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
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="sspec_user{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Qty">
                    <input type="text" class="form-control form-control-sm" id="sqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="suom{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Remark">
                    <textarea class="form-control form-control-sm" id="sremark{{$dom}}" readonly rows="5"></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Sender">
                    <input type="text" class="form-control form-control-sm" id="sender{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Est. Send Date">
                    <x-pickerdate :dom="$dom" compid="est_send_date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.sendAssetRequest()">
            <span>{{ __('Confirmation Send') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalreject" title="Reject Request Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Mutation Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="rplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="rcost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Receiver">
                    <input type="text" class="form-control form-control-sm" id="rplant_receiver{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <input type="text" class="form-control form-control-sm" id="rcost_center_receiver{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <input type="text" class="form-control form-control-sm" id="rvalidator{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="rnote_request{{$dom}}" rows="3" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Mutation Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="rasset_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Sub Number">
                    <input type="text" class="form-control form-control-sm" id="rasset_sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Name">
                    <input type="text" class="form-control form-control-sm" id="rasset_name{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="rspec_user{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Qty">
                    <input type="text" class="form-control form-control-sm" id="rqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="ruom{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Remark">
                    <textarea class="form-control form-control-sm" id="rremark{{$dom}}" readonly rows="5"></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="Note Rejected">
                    <textarea class="form-control form-control-sm" id="note_rejected{{$dom}}" rows="3"></textarea>
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

<x-modal :dom="$dom" compid="modalunapprove" title="UnApprove Request Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Request Mutation Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="uplant_sender{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Sender">
                    <input type="text" class="form-control form-control-sm" id="ucost_center_sender{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
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
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="unote_request{{$dom}}" rows="3" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Mutation Description')" class="mt-0" />
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
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="uspec_user{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Qty">
                    <input type="text" class="form-control form-control-sm" id="uqty_mutation{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="UOM">
                    <input type="text" class="form-control form-control-sm" id="uuom{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Remark">
                    <textarea class="form-control form-control-sm" id="uremark{{$dom}}" readonly rows="5"></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="Note UnApprove">
                    <textarea class="form-control form-control-sm" id="unote_unapprove{{$dom}}" rows="3"></textarea>
                </x-row-vertical>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.unapprove()">
            <span>{{ __('UnApprove Request Asset Transfer') }}</span>
        </button>
    </x-slot>
</x-modal>

{{-- data reference asset --}}
@php
    $columns =
        [
            [
                'label' => 'Status Request',
                'data' => 'status_request_desc',
                'searchable' => 'false',
                'orderable' => 'false',
            ],[
                'label' => 'Status Mutation',
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
        save: "financeacc/asset/request",
        refAsset: "{{$urlRefAsset}}",
    },
    event: {
        assignValidator: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.setAssignValidator();
        },
        confirmationValidator: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.setComfirmValidator();
        },
        sendAssetMutation: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.setSendAssetMutation();
        },
        changeAssetRequest: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var url = {{$dom}}.url.refAsset + '?plant_id=' + data.from_plant_id + '&cost_center_code=' + data.from_cost_center_code;
            fdtbletableref_asset{{$dom}}.changeUrl(url);

            showModal('modalref_asset{{$dom}}');
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
            showModal('modalreject{{$dom}}');
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
                            "{{$dom}}.event.showUnApprove");

        },
        showUnApprove: function () {
            {{$dom}}.func.setUnApprove();
            showModal('modalunapprove{{$dom}}');
        },
    },
    func: {
        setUnApprove: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#uplant_sender{{$dom}}").val(data.plant_sender);
            $("#uplant_receiver{{$dom}}").val(data.plant_receiver);
            $("#ucost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#ucost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#uasset_number{{$dom}}").val(data.number);
            $("#uasset_sub_number{{$dom}}").val(data.number_sub);
            $("#uasset_name{{$dom}}").val(data.description);
            $("#uspec_user{{$dom}}").val(data.spec_user);
            $("#uqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#unote_request{{$dom}}").val(data.note_request);
            $("#uvalidator{{$dom}}").val(data.validator);
            $("#uremark{{$dom}}").val(data.remark);
            $("#uuom{{$dom}}").val(data.uom);

            $("#unote_unapprove{{$dom}}").val('');
        },
        setRejectValidator: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#rplant_sender{{$dom}}").val(data.plant_sender);
            $("#rplant_receiver{{$dom}}").val(data.plant_receiver);
            $("#rcost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#rcost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#rasset_number{{$dom}}").val(data.number);
            $("#rasset_sub_number{{$dom}}").val(data.number_sub);
            $("#rasset_name{{$dom}}").val(data.description);
            $("#rspec_user{{$dom}}").val(data.spec_user);
            $("#rqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#rnote_request{{$dom}}").val(data.note_request);
            $("#rvalidator{{$dom}}").val(data.validator);
            $("#rremark{{$dom}}").val(data.remark);
            $("#ruom{{$dom}}").val(data.uom);

            $("#note_rejected{{$dom}}").val('');
        },
        setAssignValidator: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#aplant_sender{{$dom}}").val(data.plant_sender);
            $("#aplant_receiver{{$dom}}").val(data.plant_receiver);
            $("#acost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#acost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#aasset_number{{$dom}}").val(data.number);
            $("#aasset_sub_number{{$dom}}").val(data.number_sub);
            $("#aasset_name{{$dom}}").val(data.description);
            $("#aspec_user{{$dom}}").val(data.spec_user);
            $("#aqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#anote_request{{$dom}}").val(data.note_request);
            $("#avalidator{{$dom}}").val(data.validator);
            $("#aremark{{$dom}}").val(data.remark);
            $("#auom{{$dom}}").val(data.uom);

            fslctavalidator{{$dom}}.clear();

            showModal('modalassign{{$dom}}');
        },
        setComfirmValidator: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#cplant_sender{{$dom}}").val(data.plant_sender);
            $("#cplant_receiver{{$dom}}").val(data.plant_receiver);
            $("#ccost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#ccost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#casset_number{{$dom}}").val(data.number);
            $("#casset_sub_number{{$dom}}").val(data.number_sub);
            $("#casset_name{{$dom}}").val(data.description);
            $("#cspec_user{{$dom}}").val(data.spec_user);
            $("#cqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#cnote_request{{$dom}}").val(data.note_request);
            $("#cvalidator{{$dom}}").val(data.validator);
            $("#cremark{{$dom}}").val(data.remark);
            $("#cuom{{$dom}}").val(data.uom);
            $("#cqty_web{{$dom}}").val(data.qty_web);

            showModal('modalconfirmation{{$dom}}');
        },
        setSendAssetMutation: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#splant_sender{{$dom}}").val(data.plant_sender);
            $("#splant_receiver{{$dom}}").val(data.plant_receiver);
            $("#scost_center_sender{{$dom}}").val(data.from_cost_center);
            $("#scost_center_receiver{{$dom}}").val(data.to_cost_center);
            $("#sasset_number{{$dom}}").val(data.number);
            $("#sasset_sub_number{{$dom}}").val(data.number_sub);
            $("#sasset_name{{$dom}}").val(data.description);
            $("#sspec_user{{$dom}}").val(data.spec_user);
            $("#sqty_mutation{{$dom}}").val(data.qty_mutation);
            $("#snote_request{{$dom}}").val(data.note_request);
            $("#svalidator{{$dom}}").val(data.validator);
            $("#sremark{{$dom}}").val(data.remark);
            $("#suom{{$dom}}").val(data.uom);

            showModal('modalsend{{$dom}}');
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
                'remark': $("#cremark{{$dom}}").val(),
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
        sendAssetRequest: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'sender': $("#sender{{$dom}}").val(),
                'est_send_date': pickerdateest_send_date{{$dom}}.get('select', 'yyyy/mm/dd'),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/send';

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
        rejectValidatorRequest: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'note_rejected': $("#note_rejected{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/reject-validator';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalreject{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        approve: function () {

            hideErrors();
            loading('start', '{{ __("Approve Asset Transfer Request") }}', 'process');

            var row = fdtbletabledata{{$dom}}.getSelectedData();
            var data = row[0];

            var data = {
                'id': data.id
            };

            var url = {{$dom}}.url.save + '/approve';

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
                'note_unapprove': $("#unote_unapprove{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/unapprove';

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
    }
}

</script>
