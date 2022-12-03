<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.create()'" />
                    </x-row-tools>
                    @hasanyrole('superadmin|pettycash staff')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create Multiple" icon="bx bx-duplicate" :onclick="$dom. '.event.createMultiple()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @endcan
                    @can('u'.$menu_id)
                    @hasanyrole('superadmin|pettycash staff')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.edit()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @hasanyrole('superadmin|store manager')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Edit No. PO" icon="bx bx-rename" :onclick="$dom. '.event.editPo()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @endcan
                    @can('ua'.$menu_id)
                    @hasanyrole('superadmin|area manager')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="UnApprove" icon="bx bx-x" :onclick="$dom. '.event.unapprove()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @hasanyrole('superadmin|pettycash staff')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject" icon="bx bx-x" :onclick="$dom. '.event.reject()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @endcan
                    @can('a'.$menu_id)
                    @hasanyrole('superadmin|area manager')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Approve" icon="bx bx-check" :onclick="$dom. '.event.approve()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @hasanyrole('superadmin|pettycash staff')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Submit" icon="bx bx-upload" :onclick="$dom. '.event.submit()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Description Reject" icon="bx bx-error" :onclick="$dom. '.event.descReject()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Preview" icon="bx bx-show-alt" :onclick="$dom. '.event.preview()'" />
                    </x-row-tools>
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
                                @hasanyrole('superadmin|pettycash staff')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.createMultiple()" ><i class="bx bx-duplicate mr-50"></i>{{ __('Create Multiple') }}</a>
                                @endhasanyrole
                                @endcan
                                @can('u'.$menu_id)
                                @hasanyrole('superadmin|pettycash staff')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                @endhasanyrole
                                @hasanyrole('superadmin|store manager')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.editPo()" ><i class="bx bx-rename mr-50"></i>{{ __('Edit No. PO') }}</a>
                                @endhasanyrole
                                @endcan
                                @can('ua'.$menu_id)
                                @hasanyrole('superadmin|area manager')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.unapprove()" ><i class="bx bx-x mr-50"></i>{{ __('UnApprove') }}</a>
                                @endhasanyrole
                                @hasanyrole('superadmin|pettycash staff')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.reject()" ><i class="bx bx-x mr-50"></i>{{ __('Reject') }}</a>
                                @endhasanyrole
                                @endcan
                                @can('a'.$menu_id)
                                @hasanyrole('superadmin|area manager')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.approve()" ><i class="bx bx-check mr-50"></i>{{ __('Approve') }}</a>
                                @endhasanyrole
                                @hasanyrole('superadmin|pettycash staff')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.submit()" ><i class="bx bx-upload mr-50"></i>{{ __('Submit') }}</a>
                                @endhasanyrole
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.descReject()" ><i class="bx bx-error mr-50"></i>{{ __('Description Reject') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-show-alt mr-50"></i>{{ __('Preview') }}</a>
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
                                    <x-row-vertical label="Plant">
                                        @hasanyrole('superadmin|pettycash staff')
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&ext=all" size="sm" dropdowncompid="tabledata" :default="[0, __('All')]" />
                                        @else
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]" />
                                        @endhasanyrole
                                    </x-row-vertical>
                                    <x-row-vertical label="From Date">
                                        <x-pickerdate :dom="$dom" compid="ffromdate" data-value="{{ date('Y/m/d', strtotime('-1 days')) }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Until Date">
                                        <x-pickerdate :dom="$dom" compid="funtildate" data-value="{{ date('Y/m/d') }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Transaction Type">
                                        @php
                                            $options = [
                                                        ['id' => 3, 'text' => __('All Transaction') ],
                                                        ['id' => 0, 'text' => 'Credit'],
                                                        ['id' => 1, 'text' => 'Debit'],
                                                        ['id' => 2, 'text' => 'Credit By PO'],
                                                    ];
                                        @endphp
                                        <x-select :dom="$dom" compid="ftransactiontype" type="array" :options="$options" size="sm"/>
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
                        'label' => 'sbmt',
                        'data' => 'submit_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'appr',
                        'data' => 'approve_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant',
                        'data' => 'plant',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'trans id',
                        'data' => 'id',
                        'searchable' => 'true',
                        'orderable' => 'false',
                        'class' => 'text-right'
                    ],[
                        'label' => 'doc.number',
                        'data' => 'document_number',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'no. po',
                        'data' => 'document_po',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'remark',
                        'data' => 'description',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'no. vouc',
                        'data' => 'voucher_number',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'date',
                        'data' => 'date_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'type',
                        'data' => 'type_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic fa',
                        'data' => 'receive_pic',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'debit',
                        'data' => 'debit_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'text-right'
                    ],[
                        'label' => 'credit',
                        'data' => 'kredit_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'text-right'
                    ],[
                        'label' => 'saldo',
                        'data' => 'saldo_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'text-right'
                    ]];
            @endphp
            @hasanyrole('superadmin|pettycash staff')
            @php
            $selectInit = [true, 'multiple'];
            $urlInit = "financeacc/pettycash/dtble?plant_id=0&from-date=" . date('Y/m/d', strtotime('-1 days')) . "&until-date=" . date('Y/m/d') . "&transaction-type=3";
            @endphp
            @else
            @php
            $selectInit = [true, 'single'];
            $urlInit = "financeacc/pettycash/dtble?plant_id=" . $first_plant_id . "&from-date=" . date('Y/m/d', strtotime('-1 days')) . "&until-date=" . date('Y/m/d') . "&transaction-type=3";
            @endphp
            @endhasanyrole
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" :url="$urlInit" :select="$selectInit" footer="false" scroller="true" number="false" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Petty Cash" size="full">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-5">
                <x-row-horizontal label="Plant">
                    <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?auth=true" size="sm" :default="[$first_plant_id, $first_plant_name]"/>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Type">
                    @php
                        $options = [
                                    ['id' => 0, 'text' => 'Credit'],
                                    ['id' => 1, 'text' => 'Debit'],
                                    ['id' => 2, 'text' => 'Credit By PO']
                                ];
                    @endphp
                    <x-select :dom="$dom" compid="type" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-0 col-md-6 col-lg-7">
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <label>{{ __('Transaction') }}</label>
                    </div>
                    <div class="col-12">
                        <div id="tools{{$dom}}">
                            <x-tools class="border">
                                <x-slot name="left">
                                    <x-row-tools>
                                        <div style="width:52vw;max-width: 20rem;" id="skredit">
                                            @php
                                                $urlGlKredit = 'master/gl/select?map=true&plant=' . $first_plant_id;
                                            @endphp
                                            <x-select :dom="$dom" type="serverside" compid="glkredit" :url="$urlGlKredit" size="sm"/>
                                        </div>
                                        <div style="width:52vw;max-width: 20rem;" id="sdebit" class="d-none">
                                            @php
                                                $options = [
                                                            ['id' => '0', 'text' => '0 - Debit']
                                                        ];
                                            @endphp
                                            <x-select :dom="$dom" compid="gldebit" type="array" :options="$options" size="sm"/>
                                        </div>
                                        <div style="width:52vw;max-width: 20rem;" id="skreditpo" class="d-none">
                                            @php
                                                $options = [
                                                            ['id' => '1', 'text' => '1 - Other']
                                                        ];
                                            @endphp
                                            <x-select :dom="$dom" compid="glkreditpo" type="array" :options="$options" size="sm"/>
                                        </div>
                                    </x-row-tools>
                                    <x-row-tools>
                                        <x-button-tools tooltip="Delete GL" icon="bx bx-trash" :onclick="$dom. '.func.removeGl()'" />
                                    </x-row-tools>
                                </x-slot>
                            </x-tools>
                        </div>
                        @php
                            $columns =
                                [[
                                    'label' => 'GL Code',
                                    'data' => 'gl_code',
                                ],[
                                    'label' => 'GL Description',
                                    'data' => 'gl_desc',
                                ],[
                                    'label' => 'Credit',
                                    'data' => 'kredit_input',
                                    'class' => 'input'
                                ],[
                                    'label' => 'Debit',
                                    'data' => 'debit_input',
                                    'class' => 'input'
                                ],[
                                    'label' => 'PIC',
                                    'data' => 'pic_input',
                                    'class' => 'input'
                                ],[
                                    'label' => 'No. Voucher',
                                    'data' => 'voucher_input',
                                    'class' => 'input'
                                ],[
                                    'label' => 'Remark',
                                    'data' => 'description_input',
                                    'class' => 'input'
                                ]];

                            $className = [
                                [
                                    'class' => 'input',
                                    'target' => 3
                                ],
                                [
                                    'class' => 'input',
                                    'target' => 4
                                ],
                                [
                                    'class' => 'input',
                                    'target' => 5
                                ],
                                [
                                    'class' => 'input',
                                    'target' => 6
                                ],
                                [
                                    'class' => 'input',
                                    'target' => 7
                                ],
                            ]
                        @endphp
                        <x-datatable-source :dom="$dom" compid="tablepetty" :columns="$columns" url="" compidmodal="modalmanage" footer="false" height="285" :select="[true, 'single']" :className="$className" />
                    </div>
                </div>
            </div>
        </div>
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

<x-modal :dom="$dom" compid="modalcreatemultiple" title="Create Multiple Petty Cash">
    <x-form-horizontal>
        <x-row-horizontal label="File Excel">
            <button type="button" class="btn btn-outline-success btn-sm mr-1 mb-1" id="fileexam{{$dom}}"><i class="bx bx-spreadsheet"></i><span class="align-middle ml-25">Download example file upload</span></button>
            <input type="file" class="form-control form-control-sm" id="fileexcel{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveMultiple()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modaledit" title="Edit Petty Cash">
    <x-form-horizontal>
        <x-row-horizontal label="No. Voucher">
            <input type="text" class="form-control form-control-sm" id="eno_voucher{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Description">
            <input type="text" class="form-control form-control-sm" id="edescription{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveEdit()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modaleditpo" title="Edit No. PO Petty Cash">
    <x-form-horizontal>
        <x-row-horizontal label="No. PO">
            <input type="text" class="form-control form-control-sm" id="epno_po{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveEditNoPo()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modaldescreject" title="Reject Petty Cash" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data Transaction')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <input type="text" class="form-control form-control-sm" id="drplant{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <input type="text" class="form-control form-control-sm" id="drdate{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="GL Code">
                    <input type="text" class="form-control form-control-sm" id="drgl_code{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="GL Desc">
                    <input type="text" class="form-control form-control-sm" id="drgl_desc{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="No PO">
                    <input type="text" class="form-control form-control-sm" id="drno_po{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Type">
                    <input type="text" class="form-control form-control-sm" id="drtype{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Remark">
                    <input type="text" class="form-control form-control-sm" id="drremark{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="drpic{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="No. Voucher">
                    <input type="text" class="form-control form-control-sm" id="drno_voucher{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Credit">
                    <input type="text" class="form-control form-control-sm" id="drcredit{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Debit">
                    <input type="text" class="form-control form-control-sm" id="drdebit{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Saldo">
                    <input type="text" class="form-control form-control-sm" id="drsaldo{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Description Reject')" class="mt-0" />
            </div>
            <div class="col-12 mb-1">
                <textarea class="form-control form-control-sm" id="drdesc_reject{{$dom}}" readonly rows="3"></textarea>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Close') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalunapprove" title="UnApprove Petty Cash" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data Transaction')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <input type="text" class="form-control form-control-sm" id="uaplant{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <input type="text" class="form-control form-control-sm" id="uadate{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="GL Code">
                    <input type="text" class="form-control form-control-sm" id="uagl_code{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="GL Desc">
                    <input type="text" class="form-control form-control-sm" id="uagl_desc{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="No PO">
                    <input type="text" class="form-control form-control-sm" id="uano_po{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Type">
                    <input type="text" class="form-control form-control-sm" id="uatype{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Remark">
                    <input type="text" class="form-control form-control-sm" id="uaremark{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="uapic{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="No. Voucher">
                    <input type="text" class="form-control form-control-sm" id="uano_voucher{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Credit">
                    <input type="text" class="form-control form-control-sm" id="uacredit{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Debit">
                    <input type="text" class="form-control form-control-sm" id="uadebit{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Saldo">
                    <input type="text" class="form-control form-control-sm" id="uasaldo{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Description UnApprove')" class="mt-0" />
            </div>
            <div class="col-12 mb-1">
                <textarea class="form-control form-control-sm" id="uadesc_unapprove{{$dom}}" rows="3"></textarea>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Close') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveUnapprove()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalreject" title="Reject Petty Cash" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data Transaction')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <input type="text" class="form-control form-control-sm" id="rplant{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <input type="text" class="form-control form-control-sm" id="rdate{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="GL Code">
                    <input type="text" class="form-control form-control-sm" id="rgl_code{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="GL Desc">
                    <input type="text" class="form-control form-control-sm" id="rgl_desc{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="No PO">
                    <input type="text" class="form-control form-control-sm" id="rno_po{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Type">
                    <input type="text" class="form-control form-control-sm" id="rtype{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Remark">
                    <input type="text" class="form-control form-control-sm" id="rremark{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="rpic{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="No. Voucher">
                    <input type="text" class="form-control form-control-sm" id="rno_voucher{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Credit">
                    <input type="text" class="form-control form-control-sm" id="rcredit{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Debit">
                    <input type="text" class="form-control form-control-sm" id="rdebit{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Saldo">
                    <input type="text" class="form-control form-control-sm" id="rsaldo{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Description Reject')" class="mt-0" />
            </div>
            <div class="col-12 mb-1">
                <textarea class="form-control form-control-sm" id="rdesc_reject{{$dom}}" rows="3"></textarea>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Close') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveReject()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalpreview" title="Pettycash Preview" size="full">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <input type="text" class="form-control form-control-sm" id="pplant{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Type">
                    <input type="text" class="form-control form-control-sm" id="ptype{{$dom}}" readonly>
                </x-row-horizontal>
            </div>

            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date">
                    <input type="text" class="form-control form-control-sm" id="pdate{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Created Date">
                    <input type="text" class="form-control form-control-sm" id="pcreateddate{{$dom}}" readonly>
                </x-row-horizontal>
            </div>

            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <label>{{ __('TRANSACTION') }}</label>
                    </div>
                    <div class="col-12">
                        @php
                            $columns = [[
                                            'label' => 'gl code',
                                            'data' => 'gl_code',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'gl description',
                                            'data' => 'gl_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'credit',
                                            'data' => 'kredit',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'debit',
                                            'data' => 'debit',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'pic',
                                            'data' => 'pic',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'no. voucher',
                                            'data' => 'voucher_number',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'Remark',
                                            'data' => 'description',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'Approved Date',
                                            'data' => 'approved_at_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'UnApproved Date',
                                            'data' => 'unapproved_at_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'Submited Date',
                                            'data' => 'submited_at_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'Rejected Date',
                                            'data' => 'rejected_at_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ]
                                    ];
                        @endphp
                        <x-datatable-serverside :dom="$dom" compid="tablepreview" :columns="$columns" compidmodal="modalpreview" url=""  footer="false" height="300" number="false"/>
                    </div>
                </div>
            </div>
        </div>
    </x-form-horizontal>
</x-modal>

<x-modal :dom="$dom" compid="modalsubmit" title="Submit Petty Cash">
    <x-form-horizontal>
        <x-row-horizontal label="PIC FA">
            <input type="text" class="form-control form-control-sm" id="spic_fa{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Receive Date">
            <x-pickerdate :dom="$dom" compid="sreceive_date" data-value="{{ date('Y/m/d') }}" clear="false"/>
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.submit()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
$( "#fileexam{{ $dom }}" ).click(function() {
    window.location.href = 'financeacc/pettycash/download/template';
});

$('#select2plant{{$dom}}').on('select2:select', function (e) {
    var splant =  fslctplant{{$dom}}.get();
    fslctglkredit{{$dom}}.url = 'master/gl/select?map=true&plant=' + splant;
    fslctglkredit{{$dom}}.refresh();

    fdtbletablepetty{{$dom}}.clear();
    fdtbletablepetty{{$dom}}.refresh();
});

$('#select2type{{$dom}}').on('select2:select', function (e) {

    var type = fslcttype{{$dom}}.get();

    // clear selected option
    fslctglkredit{{$dom}}.clear();
    fslctgldebit{{$dom}}.clear();
    fslctglkreditpo{{$dom}}.clear();

    if( type == '0' ){
        // kredit
        $( "#skredit" ).removeClass( "d-none" );
        $( "#sdebit" ).addClass( "d-none" );
        $( "#skreditpo" ).addClass( "d-none" );

    } else if ( type == '1' ) {
        // debit
        $( "#skredit" ).addClass( "d-none" );
        $( "#sdebit" ).removeClass( "d-none" );
        $( "#skreditpo" ).addClass( "d-none" );

    } else {
        // kredit po
        $( "#skredit" ).addClass( "d-none" );
        $( "#sdebit" ).addClass( "d-none" );
        $( "#skreditpo" ).removeClass( "d-none" );

    }

    fdtbletablepetty{{$dom}}.clear();
    fdtbletablepetty{{$dom}}.refresh();

});

$('#select2glkredit{{$dom}}').on('select2:select', function (e) {
    var sglkredit =  $('#select2glkredit{{$dom}}').select2('data');

    var text = sglkredit[0].text;
    var split = text.split('-');

    var glCode = split[0].trim();
    var glDesc = split[1].trim();

    fslctglkredit{{$dom}}.clear();
    {{$dom}}.func.addGl(glCode, glDesc);
});

$('#select2gldebit{{$dom}}').on('select2:select', function (e) {
    var sgldebit =  $('#select2gldebit{{$dom}}').select2('data');

    var text = sgldebit[0].text;
    var split = text.split('-');

    var glCode = split[0].trim();
    var glDesc = split[1].trim();

    fslctgldebit{{$dom}}.clear();
    {{$dom}}.func.addGl(glCode, glDesc);
});

$('#select2glkreditpo{{$dom}}').on('select2:select', function (e) {
    var sglkreditpo =  $('#select2glkreditpo{{$dom}}').select2('data');

    var text = sglkreditpo[0].text;
    var split = text.split('-');

    var glCode = split[0].trim();
    var glDesc = split[1].trim();

    fslctglkreditpo{{$dom}}.clear();
    {{$dom}}.func.addGl(glCode, glDesc);
});

{{$dom}} = {
    data: {
        id: 0,
    },
    url: {
        save: "financeacc/pettycash",
    },
    event: {
        create: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        },
        createMultiple: function () {
            {{$dom}}.func.resetMultiple();
            showModal('modalcreatemultiple{{$dom}}');
        },
        preview: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            $("#pplant{{$dom}}").val(data.plant);
            $("#pdate{{$dom}}").val(data.date_desc);
            $("#ptype{{$dom}}").val(data.type_desc);
            $("#pcreateddate{{$dom}}").val(data.created_at_desc);

            var url = {{$dom}}.url.save + '/preview/dtble?transaction-id=' + data.transaction_id;
            fdtbletablepreview{{$dom}}.changeUrl(url);

            showModal('modalpreview{{$dom}}');

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

            {{$dom}}.func.set();
        },
        editPo: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.type != 2){
                message.info(" {{ __('Please choose transaction type credit by po') }} ");
                return false;
            }

            {{$dom}}.func.setEditPo();
        },
        descReject: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.approve != 2 && data.submit != 2){
                message.info(" {{ __('This transaction is not rejected.') }} ");
                return false;
            }

            {{$dom}}.func.setDescReject();
        },
        approve: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.approve != 0 || data.submit != 0){
                message.info(" {{ __('This transaction cannot be approve.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been approved cannot be restored.",
                            "{{$dom}}.func.approve");

        },
        unapprove: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.approve != 0 || data.submit != 0){
                message.info(" {{ __('This transaction cannot be unappprove.') }} ");
                return false;
            }

            {{$dom}}.func.setUnapprove();

        },
        reject: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();

            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            if( rows > 1  ){
                message.info(" {{ __('Please select one transaction to reject') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.approve != 1){
                message.info(" {{ __('This transaction not yet approved by am, cannot rejected.') }} ");
                return false;
            }

            if(data.submit != 0){
                message.info(" {{ __('This transaction has been processed, cannot rejected.') }} ");
                return false;
            }

            {{$dom}}.func.setReject();
        },
        submit: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();

            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var datas = fdtbletabledata{{$dom}}.getSelectedData();

            var plant = datas[0].plant_id;

            for (let i = 0; i < datas.length; i++) {

                // check approve am
                if (datas[i].approve != '1') {
                    message.info(" {{ __('This transaction not yet approved by am, cannot submited to SAP.') }} ");
                    return false;
                }

                // check submit status
                if(datas[i].submit != 0){
                    message.info(" {{ __('This transaction has been processed, cannot submited to SAP.') }} ");
                    return false;
                }

                // check plant
                if (datas[i].plant_id != plant) {
                    message.info(" {{ __('Submitted data must not different plant.') }} ");
                    return false;
                }

            }


            $("#spic_fa{{$dom}}").val('');

            showModal('modalsubmit{{$dom}}');

        },
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.save + '/dtble?plant_id=' + fslctfplant{{$dom}}.get() + '&from-date=' + pickerdateffromdate{{$dom}}.get('select', 'yyyy/mm/dd') + '&until-date=' + pickerdatefuntildate{{$dom}}.get('select', 'yyyy/mm/dd') + '&transaction-type=' + fslctftransactiontype{{$dom}}.get();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {

            {{$dom}}.data.id = 0;

            pickerdatedate{{$dom}}.set('select', '{{ date("Y-m-d") }}', { format: 'yyyy-mm-dd' });
            fdtbletablepetty{{$dom}}.clear();
            fdtbletablepetty{{$dom}}.refresh();

        },
        resetMultiple: function () {

            {{$dom}}.data.id = 0;
            $("#fileexcel{{$dom}}").val('');

        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#eno_voucher{{$dom}}").val(data.voucher_number);
            $("#edescription{{$dom}}").val(data.description);
            showModal('modaledit{{$dom}}');
        },
        setEditPo: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#epno_po{{$dom}}").val(data.document_po);
            showModal('modaleditpo{{$dom}}');
        },
        setDescReject: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#drplant{{$dom}}").val(data.plant);
            $("#drdate{{$dom}}").val(data.date_desc);
            $("#drgl_code{{$dom}}").val(data.gl_code);
            $("#drgl_desc{{$dom}}").val(data.gl_desc);
            $("#drno_po{{$dom}}").val(data.document_po);
            $("#drtype{{$dom}}").val(data.type_desc);
            $("#drremark{{$dom}}").val(data.description);
            $("#drpic{{$dom}}").val(data.pic);
            $("#drno_voucher{{$dom}}").val(data.voucher_number);
            $("#drcredit{{$dom}}").val(data.kredit_desc);
            $("#drdebit{{$dom}}").val(data.debit_desc);
            $("#drsaldo{{$dom}}").val(data.saldo_desc);
            $("#drdesc_reject{{$dom}}").val(data.description_reject);
            showModal('modaldescreject{{$dom}}');
        },
        setUnapprove: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#uaplant{{$dom}}").val(data.plant);
            $("#uadate{{$dom}}").val(data.date_desc);
            $("#uagl_code{{$dom}}").val(data.gl_code);
            $("#uagl_desc{{$dom}}").val(data.gl_desc);
            $("#uano_po{{$dom}}").val(data.document_po);
            $("#uatype{{$dom}}").val(data.type_desc);
            $("#uaremark{{$dom}}").val(data.description);
            $("#uapic{{$dom}}").val(data.pic);
            $("#uano_voucher{{$dom}}").val(data.voucher_number);
            $("#uacredit{{$dom}}").val(data.kredit_desc);
            $("#uadebit{{$dom}}").val(data.debit_desc);
            $("#uasaldo{{$dom}}").val(data.saldo_desc);
            $("#uadesc_unapprove{{$dom}}").val(data.description_reject);
            showModal('modalunapprove{{$dom}}');
        },
        setReject: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#rplant{{$dom}}").val(data.plant);
            $("#rdate{{$dom}}").val(data.date_desc);
            $("#rgl_code{{$dom}}").val(data.gl_code);
            $("#rgl_desc{{$dom}}").val(data.gl_desc);
            $("#rno_po{{$dom}}").val(data.document_po);
            $("#rtype{{$dom}}").val(data.type_desc);
            $("#rremark{{$dom}}").val(data.description);
            $("#rpic{{$dom}}").val(data.pic);
            $("#rno_voucher{{$dom}}").val(data.voucher_number);
            $("#rcredit{{$dom}}").val(data.kredit_desc);
            $("#rdebit{{$dom}}").val(data.debit_desc);
            $("#rsaldo{{$dom}}").val(data.saldo_desc);
            $("#rdesc_reject{{$dom}}").val(data.description_reject);
            showModal('modalreject{{$dom}}');
        },
        getDataForm: function () {
            var datas = fdtbletablepetty{{$dom}}.getAllData();

            var glCode = [];
            var glDesc = [];
            datas.map(function (data) {
                glCode.push(data[1]);
                glDesc.push(data[2]);
            });

            return {
                'plant': fslctplant{{$dom}}.get(),
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'type': fslcttype{{$dom}}.get(),
                'kredit': JSON.stringify($("input[name='kredit{{$dom}}[]']").map(function(){return $(this).val();}).get()),
                'debit': JSON.stringify($("input[name='debit{{$dom}}[]']").map(function(){return $(this).val();}).get()),
                'pic': JSON.stringify($("input[name='pic{{$dom}}[]']").map(function(){return $(this).val();}).get()),
                'voucher': JSON.stringify($("input[name='voucher{{$dom}}[]']").map(function(){return $(this).val();}).get()),
                'description': JSON.stringify($("input[name='description{{$dom}}[]']").map(function(){return $(this).val();}).get()),
                'gl_code': JSON.stringify(glCode),
                'gl_desc': JSON.stringify(glDesc),
                'id': {{$dom}}.data.id
            }
        },
        save: function () {
            hideErrors();

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save;
            if( {{$dom}}.data.id != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

            // check have transaction or not
            var glCode = JSON.parse(data.gl_code);
            if(glCode.length <= 0){
                message.info('{{ __("Please add transaction first") }}');
                return false
            }

            // check transaction value debit and kredit
            var kredit = JSON.parse(data.kredit);
            var debit = JSON.parse(data.debit);
            var pic = JSON.parse(data.pic);
            var description = JSON.parse(data.description);

            for (let i = 0; i < glCode.length; i++) {

                if( (!kredit[i] || kredit[i] <= 0) && (!debit[i] || debit[i] <= 0) ){
                    message.info('{{ __("Nominal credit and debit cannot be both empty") }}');
                    return false;
                }

                if( kredit[i] > 0 && debit[i] > 0 ){
                    message.info('{{ __("Nominal Kredit and debit cannot be filled in both, fill in one") }}');
                    return false;
                }

                if( !pic[i] && pic[i] == '' ){
                    message.info('{{ __("PIC cannot be empty.") }}');
                    return false;
                }

                if( !description[i] && description[i] == '' ){
                    message.info('{{ __("Remark cannot be empty.") }}');
                    return false;
                }

                if(data.type == '0'){
                    // kredit
                    if( glCode[i] == '21212000' || glCode[i] == '21217000' ){
                        if( debit[i] <= 0 ){
                            message.info('{{ __("GL code 21212000 and 21217000, nominal save on debit") }}');
                            return false;
                        }
                    }else{
                        if( kredit[i] <= 0 ){
                            message.info('{{ __("Type transaction credit, nominal save on credit") }}');
                            return false;
                        }
                    }

                } else if (data.type == '1') {
                    // debit
                    if( debit[i] <= 0 ){
                        message.info('{{ __("Type transaction debit, nominal save on debit") }}');
                        return false;
                    }
                } else {
                    // kredit by po
                    if( kredit[i] <= 0 ){
                        message.info('{{ __("Type transaction credit po, nominal save on credit") }}');
                        return false;
                    }
                }
            }

            loadingModal('start');

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
        },
        submit: function () {
            hideErrors();
            loadingModal('start');

            var datas = fdtbletabledata{{$dom}}.getSelectedData();

            var idSubmited = [];

            datas.map(function (d) {
                idSubmited.push(d.id);
            });

            var data = {
                'id_submited': JSON.stringify(idSubmited),
                'pic_fa': $("#spic_fa{{$dom}}").val(),
                'receive_date': pickerdatesreceive_date{{$dom}}.get('select', 'yyyy/mm/dd')
            };

            var url = {{$dom}}.url.save + '/submit';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalsubmit{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        saveMultiple: function () {
            hideErrors();
            loadingModal('start');

            var data = new FormData();
            var files = $('#fileexcel{{$dom}}')[0].files[0];
            var file = '';
            if( typeof files != 'undefined' ){
                file = files;
            }
            data.append('file_excel',file);

            var url = {{$dom}}.url.save + '/create-multiple';

            $.ajax({
                url: url,
                type: 'post',
                data: data,
                contentType: false,
                processData: false,
                success: function(res){
                    loadingModal("stop");
                    if( res.status == 'success' ){
                        fdtbletabledata{{$dom}}.refresh();
                        hideModal('modalcreatemultiple{{$dom}}');
                        message.success(res.message);
                    } else {
                        message.info(res.message);
                    }
                },
            });
        },
        saveEdit: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'no_voucher': $("#eno_voucher{{$dom}}").val(),
                'description': $("#edescription{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/edit';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modaledit{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        saveEditNoPo: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'no_po': $("#epno_po{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/edit-no-po';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modaleditpo{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        saveUnapprove: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'description_unapprove': $("#uadesc_unapprove{{$dom}}").val(),
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
                    message.info(res.message);
                }
            }, 'json');
        },
        saveReject: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'description_reject': $("#rdesc_reject{{$dom}}").val(),
                'id': {{$dom}}.data.id
            };

            var url = {{$dom}}.url.save + '/reject';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalreject{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        approve: function () {
            loading('start', '{{ __("Approve") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/approve/' + row[0].id;

            $.get(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        },
        addGl: function (glCode, glDesc) {
            loadingModal("start");

            var datas = fdtbletablepetty{{$dom}}.getAllData();
            var kredit = $("input[name='kredit{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var debit = $("input[name='debit{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var pic = $("input[name='pic{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var voucher = $("input[name='voucher{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var description = $("input[name='description{{$dom}}[]']").map(function(){return $(this).val();}).get();

            fdtbletablepetty{{$dom}}.clear();

            for (let i = 0; i < kredit.length; i++) {
                fdtbletablepetty{{$dom}}.add([
                    '',
                    datas[i][1],
                    datas[i][2],
                    '<input type="number" class="form-control form-control-sm mul" name="kredit{{$dom}}[]" value="' + kredit[i] + '" style="min-width: 6rem;">',
                    '<input type="number" class="form-control form-control-sm mul" name="debit{{$dom}}[]" value="' + debit[i] + '" style="min-width: 6rem;">',
                    '<input type="text" class="form-control form-control-sm mul" name="pic{{$dom}}[]" value="' + pic[i] + '" style="min-width: 6rem;">',
                    '<input type="text" class="form-control form-control-sm mul" name="voucher{{$dom}}[]" value="' + voucher[i] + '" style="min-width: 6rem;">',
                    '<input type="text" class="form-control form-control-sm mul" name="description{{$dom}}[]" value="' + description[i] + '" style="min-width: 6rem;">',
                ]);
            }

            fdtbletablepetty{{$dom}}.add([
                '',
                glCode,
                glDesc,
                '<input type="number" class="form-control form-control-sm mul" name="kredit{{$dom}}[]" value="0" style="min-width: 6rem;">',
                '<input type="number" class="form-control form-control-sm mul" name="debit{{$dom}}[]" value="0" style="min-width: 6rem;">',
                '<input type="text" class="form-control form-control-sm mul" name="pic{{$dom}}[]" style="min-width: 6rem;">',
                '<input type="text" class="form-control form-control-sm mul" name="voucher{{$dom}}[]" style="min-width: 6rem;">',
                '<input type="text" class="form-control form-control-sm mul" name="description{{$dom}}[]" style="min-width: 6rem;">',
            ]);

            fdtbletablepetty{{$dom}}.refresh();
            loadingModal("stop");
        },
        removeGl: function () {
            var rows = fdtbletablepetty{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var index = fdtbletablepetty{{$dom}}.getRowIndex();
            var datas = fdtbletablepetty{{$dom}}.getAllData();
            var kredit = $("input[name='kredit{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var debit = $("input[name='debit{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var pic = $("input[name='pic{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var voucher = $("input[name='voucher{{$dom}}[]']").map(function(){return $(this).val();}).get();
            var description = $("input[name='description{{$dom}}[]']").map(function(){return $(this).val();}).get();

            fdtbletablepetty{{$dom}}.clear();

            for (let i = 0; i < kredit.length; i++) {
                if( index != i ){
                    fdtbletablepetty{{$dom}}.add([
                        '',
                        datas[i][1],
                        datas[i][2],
                        '<input type="number" class="form-control form-control-sm mul" name="kredit{{$dom}}[]" value="' + kredit[i] + '" style="min-width: 6rem;">',
                        '<input type="number" class="form-control form-control-sm mul" name="debit{{$dom}}[]" value="' + debit[i] + '" style="min-width: 6rem;">',
                        '<input type="text" class="form-control form-control-sm mul" name="pic{{$dom}}[]" value="' + pic[i] + '" style="min-width: 6rem;">',
                        '<input type="text" class="form-control form-control-sm mul" name="voucher{{$dom}}[]" value="' + voucher[i] + '" style="min-width: 6rem;">',
                        '<input type="text" class="form-control form-control-sm mul" name="description{{$dom}}[]" value="' + description[i] + '" style="min-width: 6rem;">',
                    ]);
                }
            }

            fdtbletablepetty{{$dom}}.refresh();
        },
    }
}

</script>
