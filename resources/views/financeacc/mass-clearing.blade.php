<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Upload" icon="bx bx-upload" :onclick="$dom. '.event.upload()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Show Detail" icon="bx bx-show-alt" :onclick="$dom. '.event.show()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Download Mass Clearing" icon="bx bx-download" :onclick="$dom. '.event.download()'" />
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
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.upload()" ><i class="bx bx-upload mr-50"></i>{{ __('Upload') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.show()" ><i class="bx bx-show-alt mr-50"></i>{{ __('Show Detail') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.download()" ><i class="bx bx-download mr-50"></i>{{ __('Download Mass Clearing') }}</a>
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
                        'label' => 'PIC',
                        'data' => 'name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'description',
                        'data' => 'description',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'upload time',
                        'data' => 'upload_time',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'start process time',
                        'data' => 'time_process_start_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'finish process time',
                        'data' => 'time_process_finish_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'status generate',
                        'data' => 'status_generate_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="financeacc/mass-clearing/dtble" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Mass Clearing">
    <x-form-horizontal>
        <x-row-horizontal label="Description">
            <textarea class="form-control form-control-sm" id="description{{$dom}}" rows="1"></textarea>
        </x-row-horizontal>
        <x-row-horizontal label="File Excel">
            <button type="button" class="btn btn-outline-success btn-sm mr-1 mb-1" id="fileexam{{$dom}}"><i class="bx bx-spreadsheet"></i><span class="align-middle ml-25">{{ __('Download example file upload') }}</span></button>
            <input type="file" class="form-control form-control-sm" id="fileexcel{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()">
            <span>{{ __('Upload') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalpreview" title="Mass Clearing Detail" size="full">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-row-horizontal label="PIC">
                    <input type="text" class="form-control form-control-sm" id="dpic{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Description">
                    <textarea class="form-control form-control-sm" id="ddescription{{$dom}}" rows="1" readonly></textarea>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Upload Time">
                    <input type="text" class="form-control form-control-sm" id="dupload_time{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Status Generate">
                    <input type="text" class="form-control form-control-sm" id="dstatus_generate{{$dom}}" readonly>
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
                                            'label' => 'status process',
                                            'data' => 'status_process_desc',
                                            'searchable' => 'false',
                                            'orderable' => 'false',
                                        ],[
                                            'label' => 'outlet',
                                            'data' => 'outlet_name',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'bank gl',
                                            'data' => 'bank_in_bank_gl',
                                            'orderable' => 'true',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'bank date',
                                            'data' => 'bank_in_date_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'description',
                                            'data' => 'bank_in_description',
                                            'orderable' => 'true',
                                            'searchable' => 'false',
                                        ],
                                        [
                                            'label' => 'sales date',
                                            'data' => 'sales_date',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'sales month',
                                            'data' => 'sales_month',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'sales year',
                                            'data' => 'sales_year',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'special gl',
                                            'data' => 'special_gl',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'outlet code',
                                            'data' => 'customer_code',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'nominal',
                                            'data' => 'nominal_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'charge',
                                            'data' => 'charge_desc',
                                            'orderable' => 'false',
                                            'searchable' => 'false',
                                        ],[
                                            'label' => 'status generate',
                                            'data' => 'status_generate_desc',
                                            'searchable' => 'false',
                                            'orderable' => 'false',
                                        ]];
                        @endphp
                        <x-datatable-serverside :dom="$dom" compid="tablepreview" :columns="$columns" compidmodal="modalpreview" url=""  footer="false" height="300" number="false"/>
                    </div>
                </div>
            </div>
        </div>
    </x-form-horizontal>
</x-modal>
<!-- end modal -->

<script>
$( "#fileexam{{ $dom }}" ).click(function() {
    window.location.href = 'financeacc/mass-clearing/download/template';
});

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "financeacc/mass-clearing",
    },
    event: {
        upload: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        },
        download: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.status_generate != '2' ){
                message.info("File cannot be downloaded, Please wait.");
                return false;
            }

            window.location.href = 'financeacc/mass-clearing/download/generate/' + data.id;

        },
        show: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var status = '';
            if( data.status_generate == 0 ){
                status = "Waiting";
            } else if( data.status_generate == 1 ){
                status = "On Process";
            } else{
                status = "Finish";
            }

            $("#ddescription{{$dom}}").val(data.description);
            $("#dpic{{$dom}}").val(data.name);
            $("#dupload_time{{$dom}}").val(data.upload_time);
            $("#dstatus_generate{{$dom}}").val(status);

            var url = {{$dom}}.url.save + '/preview/dtble?id=' + data.id;
            fdtbletablepreview{{$dom}}.changeUrl(url);

            showModal('modalpreview{{$dom}}');
        },
    },
    func: {
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#fileexcel{{$dom}}").val('');
            $("#description{{$dom}}").val('');
        },
        save: function () {
            hideErrors();
            loadingModal('start');

            var data = new FormData();
            var files = $('#fileexcel{{$dom}}')[0].files[0];
            var file = '';
            if( typeof files != 'undefined' ){
                file = files;
            }
            data.append('file_excel',file);
            data.append('description', $("#description{{$dom}}").val());

            var url = {{$dom}}.url.save;

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
                        hideModal('modalmanage{{$dom}}');
                        message.success(res.message);
                    } else {
                        $("#fileexcel{{$dom}}").val('');
                        message.info(res.message);
                    }
                },
            });
        },
    }
}

</script>
