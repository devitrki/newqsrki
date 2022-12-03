<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <div class="box-g-head text-center pt-2">
                @if($checked['status'])
                <div class="col-sm-4 offset-sm-4">
                    <h4>Periode Asset SO {{ $periode['label_month'] . ' ' . $periode['year'] }}</h4>
                    <h4>{{ $plant['type'] . ' ' . $plant['name'] }}</h4>
                    <hr class="my-2">
                    <h5 class="mb-1">File List Asset SO</h5>
                    <x-form-vertical>
                        <x-row-vertical label="Select Cost Center">
                            <x-select :dom="$dom" compid="costcenter" type="serverside" async="false" url="financeacc/asset/so/select/costcenter/{{ $plant['id'] }}" size="sm"/>
                        </x-row-vertical>
                    </x-form-vertical>
                    <button class="btn btn-secondary btn-sm" onclick="{{$dom}}.func.download()">
                        <span>{{ __('Download Asset SO') }}</span>
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="{{$dom}}.func.preview()">
                        <span>{{ __('Preview Asset SO') }}</span>
                    </button>
                    <hr class="my-2">
                    <h5 class="mb-1">Update Asset SO</h5>
                    <x-form-vertical>
                        <x-row-vertical label="File Asset SO">
                            <input type="file" class="form-control form-control-sm" id="fileexcel{{$dom}}" accept=".xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" >
                        </x-row-vertical>
                    </x-form-vertical>
                    <button class="btn btn-secondary btn-sm" onclick="{{$dom}}.func.upload()">
                        <span>{{ __('Upload File Asset SO') }}</span>
                    </button>
                </div>

                @else
                {{-- failed --}}
                <div class="col-sm-6 offset-sm-3 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ $checked['message'] }}</h3>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Asset Mutation" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Asset Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="plant{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center">
                    <input type="text" class="form-control form-control-sm" id="cost_center{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="spec_user{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="number{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Sub Number">
                    <input type="text" class="form-control form-control-sm" id="sub_number{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Description">
                    <input type="text" class="form-control form-control-sm" id="description{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Web">
                    <input type="text" class="form-control form-control-sm" id="qty_web{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Uom">
                    <input type="text" class="form-control form-control-sm" id="uom{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Mutation')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Receiver">
                    <x-select :dom="$dom" compid="plant_receiver" type="serverside" url="master/plant/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <x-select :dom="$dom" compid="cost_center_receiver" type="array" size="sm" />
                </x-row-horizontal>
                <x-row-horizontal label="Sender">
                    <input type="text" class="form-control form-control-sm" id="sender{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Remark">
                    <textarea class="form-control form-control-sm" id="remark{{$dom}}" rows="5"></textarea>
                </x-row-horizontal>
                <x-row-horizontal label="Qty">
                    <input type="number" step=".001" class="form-control form-control-sm" id="qty_mutation{{$dom}}" min="0.001" max="1000000">
                </x-row-horizontal>
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

<x-modal :dom="$dom" compid="modalsync" title="Asset Sync">
    <x-form-horizontal>
        <x-row-horizontal label="Plant">
            <x-select :dom="$dom" compid="splant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.sync()">
            <span>{{ __('Sync') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
$('#select2fplant{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;
    $.get( 'financeacc/asset/costcenter/' + data.id, function (res) {
        fslctfcostcenter{{$dom}}.initWithData(res);
    });
});

$('#select2plant_receiver{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;
    $.get( 'financeacc/asset/costcenter/' + data.id, function (res) {
        fslctcost_center_receiver{{$dom}}.initWithData(res);
    });
});

$("#hbtnfiltertabledata{{$dom}}").on("click change", function(e) {
    plantId = fslctfplant{{$dom}}.get();
    ccCode = fslctfcostcenter{{$dom}}.get();

    if (ccCode == '' || ccCode == null) {
        $.get( 'financeacc/asset/costcenter/' + plantId, function (res) {
            fslctfcostcenter{{$dom}}.initWithData(res);
        });
    }
});

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "financeacc/asset/so",
    },
    event: {
        sync: function () {
            showModal('modalsync{{$dom}}');
        },
        mutation: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check qty zero
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.qty_web < 1){
                message.info(" {{ __('Qty of this asset is 0, cannot be transferred.') }} ");
                return false;
            }

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/check';

            $.post( url, data, function (res) {
                var check = false;
                if( res.status == 'success' ){
                    check = res.data.check;
                }

                if(check){
                    message.info(" {{ __('This asset is in the process of being transferred, cannot be mutated.') }} ");
                    return false;
                }

                {{$dom}}.func.reset();
                {{$dom}}.func.set();
                showModal('modalmanage{{$dom}}');

            }, 'json');

        },
        cancel: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/check';

            $.post( url, data, function (res) {
                var check = false;
                if( res.status == 'success' ){
                    check = res.data.check;
                }

                if(!check){
                    message.info(" {{ __('This asset has not been transferred, cannot be canceled.') }} ");
                    return false;
                }

                message.confirm("Are you sure ?",
                            "Data that has been canceled cannot be restored.",
                            "{{$dom}}.func.cancel");

            }, 'json');

        }
    },
    func: {
        download: function () {
            var costcenter = fslctcostcenter{{$dom}}.get();

            if(costcenter == '' || costcenter == null){
                message.info("{{ __('Please select cost center first') }}");
                return false;
            }

            var url = {{$dom}}.url.save + '/download?costcenter=' + costcenter +
                                                    '&plant-id={{ $plant["id"] }}' +
                                                    '&periode-month={{ $periode["month"] }}' +
                                                    '&periode-year={{ $periode["year"] }}';

            window.open(url);
        },
        preview: function () {
            var costcenter = fslctcostcenter{{$dom}}.get();

            if(costcenter == '' || costcenter == null){
                message.info("{{ __('Please select cost center first') }}");
                return false;
            }

            var url = {{$dom}}.url.save + '/preview?costcenter=' + costcenter +
                                                    '&plant-id={{ $plant["id"] }}' +
                                                    '&periode-month={{ $periode["month"] }}' +
                                                    '&periode-year={{ $periode["year"] }}';

            window.open(url);
        },
        upload: function () {
            loading('start');
            var url = {{$dom}}.url.save + '/upload';

            var data = new FormData();
            var files = $('#fileexcel{{$dom}}')[0].files[0];
            var file = '';
            if( typeof files != 'undefined' ){
                file = files;
            }
            data.append('file_excel',file);

            $.ajax({
                url: url,
                type: 'post',
                data: data,
                contentType: false,
                processData: false,
                success: function(res){
                    loading("stop");
                    $('#fileexcel{{$dom}}').val('');
                    if( res.status == 'success' ){
                        message.success(res.message);
                    } else {
                        message.info(res.message);
                    }
                },
                statusCode: {
                    422: function (data) {
                        loading('stop');
                        message.info("Please select file excel first");
                    },
                }
            });

        },
        sync: function () {
            hideErrors();
            loadingModal('start');

            var url = {{$dom}}.url.save + '/sync';
            var data = {
                'plant': fslctsplant{{$dom}}.get()
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalsync{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        filter: function () {
            var url = {{$dom}}.url.save + '/dtble?plant_id=' + fslctfplant{{$dom}}.get() + '&cost_center_code=' + fslctfcostcenter{{$dom}}.get();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#sender{{$dom}}").val('');
            $("#qty_mutation{{$dom}}").val('');
            $("#remark{{$dom}}").val('');
            $("#qty_mutation{{$dom}}").prop("disabled", false);
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            $("#plant{{$dom}}").val(data.plant);
            $("#cost_center{{$dom}}").val(data.cost_center_desc);
            $("#spec_user{{$dom}}").val(data.spec_user);
            $("#remark{{$dom}}").val(data.remark);
            $("#number{{$dom}}").val(data.number);
            $("#sub_number{{$dom}}").val(data.number_sub);
            $("#description{{$dom}}").val(data.description);
            $("#qty_web{{$dom}}").val(data.qty_web);

            if( data.qty_web <= 1 ){
                $("#qty_mutation{{$dom}}").val(data.qty_web);
                $("#qty_mutation{{$dom}}").prop("disabled", true);
            }

            plantId = fslctplant_receiver{{$dom}}.get();
            ccCode = fslctcost_center_receiver{{$dom}}.get();
            if (ccCode == '' || ccCode == null) {
                $.get( 'financeacc/asset/costcenter/' + plantId, function (res) {
                    fslctcost_center_receiver{{$dom}}.initWithData(res);
                });
            }
        },
        getDataForm: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var costcenter = $("#select2cost_center_receiver{{$dom}}").select2('data');
            var idCostCenter = "";
            var textCostCenter = "";
            if(Array.isArray(costcenter)){
                if(costcenter.length > 0){
                    idCostCenter = costcenter[0].id;
                    textCostCenter = costcenter[0].text;
                }
            }

            return {
                'plant_id': data.plant_id,
                'number': data.number,
                'number_sub': data.number_sub,
                'cost_center': data.cost_center,
                'qty_mutation': $("#qty_mutation{{$dom}}").val(),
                'plant_receiver': fslctplant_receiver{{$dom}}.get(),
                'plant_sender': data.plant_id,
                'cost_center_receiver': textCostCenter,
                'cost_center_code_receiver': idCostCenter,
                'remark': $("#remark{{$dom}}").val(),
                'sender': $("#sender{{$dom}}").val(),
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
        cancel: function () {
            loading('start', '{{ __("Cancel Asset Transfer") }}', 'process');

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/cancel';

            $.post( url, data, function (res) {
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
