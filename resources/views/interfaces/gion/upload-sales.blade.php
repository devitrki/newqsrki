<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <div class="box-g-head text-center pt-4">
                <h4>Upload Sales GION to Accurate</h4>
                <div class="col-sm-4 offset-sm-4 mt-2">
                    <x-form-vertical>
                        <x-row-vertical label="Select Branch">
                            <x-select :dom="$dom" compid="branch" type="serverside" url="interfaces/gion/accurate/mapping-branch/select" size="sm"/>
                        </x-row-vertical>
                        <x-row-vertical label="Select Date">
                            <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                        </x-row-vertical>
                    </x-form-vertical>
                    <hr>
                    <h5>Status Connection</h5>
                    <div class="border">
                        <h6 class="my-1" id="connection-status">Waiting</h6>
                    </div>
                    <hr>
                    <div class="col-12 mb-1">
                        <button class="btn btn-info ml-1" onclick="{{$dom}}.func.connect()">
                            <span>{{ __('Connect Connection to Engine Local') }}</span>
                        </button>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-secondary ml-1" onclick="{{$dom}}.func.upload()">
                            <span>{{ __('Upload Sales') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-card-scroll>

<script>
{{$dom}} = {
   data: {
        // 0 = not connected
        // 1 = connected
        // 2 = waiting
        connection : 2,
        connectionStatus : 'Waiting',
    },
    url: {
        local: "{{ $url_local }}",
        save: 'interfaces/gion/accurate/upload-sales'
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
    },
    func: {
        connect: function () {
            loading("start");
            $.ajax({
                url: {{$dom}}.url.local + '/check',
                type: 'GET',
                success: function (res) {
                    loading("stop");
                    if( res.status == 'success' ){
                        {{$dom}}.data.connectionStatus = 'Connected';
                        {{$dom}}.data.connection = 1;
                        $("#connection-status").html({{$dom}}.data.connectionStatus);
                        message.success("Connected to Engine Local");
                    } else {
                        {{$dom}}.data.connectionStatus = 'Not Connected';
                        {{$dom}}.data.connection = 0;
                        $("#connection-status").html({{$dom}}.data.connectionStatus);
                        message.info(res.message);
                    }
                },
                error: function (request, status, error) {
                    loading("stop");
                    {{$dom}}.data.connectionStatus = 'Not Connected';
                    {{$dom}}.data.connection = 0;
                    $("#connection-status").html({{$dom}}.data.connectionStatus);
                    message.info("please check the local engine must be active");
                }
            });
        },
        upload: function () {
            if({{$dom}}.data.connection == 2 || {{$dom}}.data.connection == 0){
                message.info("Please connect engine local first");
                return false;
            }

            loading("start");

            var data = {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
            };

            $.ajax({
                url: {{$dom}}.url.local + '/data',
                type: 'POST',
                data: data,
                success: function (res) {
                    if( res.status == 'success' ){
                        if(res.data.tickets.length < 1) {
                            loading("stop");
                            message.info("No transaction on date " + pickerdatedate{{$dom}}.get('select', 'dd/mm/yyyy'));
                            return false;
                        }

                        data_upload_sales = {
                            'tickets' : JSON.stringify(res.data.tickets),
                            'orders' : JSON.stringify(res.data.orders),
                            'payments' : JSON.stringify(res.data.payments),
                            'calculations' : JSON.stringify(res.data.calculations),
                            'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                            'branch': fslctbranch{{$dom}}.get()
                        }

                        $.post( {{$dom}}.url.save, data_upload_sales, function (r) {
                            loading("stop");
                            if( r.status == 'success' ){
                                message.success(r.message);
                            } else {
                                message.info(r.message);
                            }
                        }, 'json');

                    } else {
                        loading("stop");
                        message.info(res.message);
                    }
                },
                error: function (request, status, error) {
                    loading("stop");
                    {{$dom}}.data.connectionStatus = 'Not Connected',
                    {{$dom}}.data.connection = 0,
                    $("#connection-status").html({{$dom}}.data.connectionStatus);
                    message.info("Please connect engine local first");
                }
            });
        },
        getDataForm: function () {
            return {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
            }
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#name{{$dom}}").val(data.name);
            $("#sap{{$dom}}").val(data.sap);
            showModal('modalmanage{{$dom}}');
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

        }
    }
}
</script>
