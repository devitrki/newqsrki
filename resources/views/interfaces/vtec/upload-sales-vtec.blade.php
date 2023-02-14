<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <div class="box-g-head text-center pt-4">
                <h4>Upload Sales Vtec</h4>
                <div class="col-sm-4 offset-sm-4 mt-2">
                    <x-form-vertical>
                        <x-row-vertical label="Select Store">
                            <x-select :dom="$dom" compid="store" type="serverside" url="master/plant/select?type=outlet&auth=true" size="sm"/>
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
                        <button class="btn btn-info ml-1" onclick="{{$dom}}.func.check()">
                            <span>{{ __('Check Configuration and Connection to Engine Local') }}</span>
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
        db : {
            host: '',
            user: '',
            password: '',
            port: '',
            database_name: '',
        },
        shop_code: '',
        flagSend: false
    },
    url: {
        local: "{{ $url_local }}",
        save: 'interfaces/vtec/upload-sales-vtec',
        check: 'interfaces/vtec/get-configuration-database',
    },
    func: {
        check: function () {
            var store = fslctstore{{$dom}}.get();

            if(store == '' || store == null){
                message.info("{{ __('Please select store first') }}");
                return false;
            }

            loading("start");

            $.get( {{$dom}}.url.check + '/' + store, function (res) {
                if( res.status == 'success' ){

                    {{$dom}}.data.db.host = res.data.host;
                    {{$dom}}.data.db.user = res.data.user;
                    {{$dom}}.data.db.password = res.data.password;
                    {{$dom}}.data.db.port = res.data.port;
                    {{$dom}}.data.db.database_name = res.data.database_name;
                    {{$dom}}.data.shop_code = res.data.shop_code;

                    $.ajax({
                        url: {{$dom}}.url.local + '/check',
                        type: 'GET',
                        success: function (res) {
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
                            loading("stop");
                        },
                        error: function (request, status, error) {
                            loading("stop");
                            {{$dom}}.data.connectionStatus = 'Not Connected';
                            {{$dom}}.data.connection = 0;
                            $("#connection-status").html({{$dom}}.data.connectionStatus);
                            message.info("{{ __('please check the local engine must be active') }}");
                        }
                    });

                } else {
                    loading("stop");
                    message.info(res.message);
                    return false;
                }
            }, 'json');
        },
        upload: function () {

            {{$dom}}.data.flagSend = false;

            var store = fslctstore{{$dom}}.get();

            if(store == '' || store == null){
                message.info("{{ __('Please select store first') }}");
                return false;
            }

            if({{$dom}}.data.connection == 2 || {{$dom}}.data.connection == 0){
                message.info("{{ __('Please connect engine local first') }}");
                return false;
            }

            loading("start");

            var data = {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'shop_code': {{$dom}}.data.shop_code,
                'user': {{$dom}}.data.db.user,
                'password': {{$dom}}.data.db.password,
                'server': {{$dom}}.data.db.host,
                'port': {{$dom}}.data.db.port,
                'database': {{$dom}}.data.db.database_name,
            };

            $.ajax({
                url: {{$dom}}.url.local + '/data',
                type: 'POST',
                data: data,
                success: function (res) {
                    console.log(res);
                    if( res.status == 'success' ){

                        if(res.data.order_transactions == null) {
                            loading("stop");
                            message.info("No transaction on date " + pickerdatedate{{$dom}}.get('select', 'dd/mm/yyyy'));
                            return false;
                        }

                        if(res.data.order_transactions.length < 1) {
                            loading("stop");
                            message.info("No transaction on date " + pickerdatedate{{$dom}}.get('select', 'dd/mm/yyyy'));
                            return false;
                        }

                        if(res.data.status_end_day != 1) {
                            loading("stop");
                            message.info("Please End Of Day First");
                            return false;
                        }

                        data_upload_sales = {
                            'order_transactions' : JSON.stringify(res.data.order_transactions),
                            'order_details' : JSON.stringify(res.data.order_details),
                            'order_pay_details' : JSON.stringify(res.data.order_pay_details),
                            'order_promotions' : JSON.stringify(res.data.order_promotions),
                            'order_statistics' : JSON.stringify(res.data.order_statistics),
                            'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                            'store': fslctstore{{$dom}}.get()
                        }

                        $.post( {{$dom}}.url.save, data_upload_sales, function (r) {
                            loading("stop");
                            if( r.status == 'success' ){

                                var urlReview = {{$dom}}.url.save + '/view?date=' + pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd') + '&plant-id=' + fslctstore{{$dom}}.get();
                                window.open(urlReview);

                                message.confirm("Apakah data penjualan sesuai ?",
                                    "Silahkan cek data yang akan diupload terlebih dahulu.",
                                    "{{$dom}}.func.confirmation");


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
                    message.info("{{ __('Please connect engine local first') }}");
                }
            });
        },
        confirmation: function () {
            if( {{$dom}}.data.flagSend == false ){
                {{$dom}}.data.flagSend = true;
                loading('start', '{{ __("Confirmation") }}', 'process');

                var data = {
                    'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                    'plant_id': fslctstore{{$dom}}.get(),
                };

                var url = {{$dom}}.url.save + '/confirmation';
                $.post(url, data, function (res) {
                    loading("stop");
                    if (res.status == 'success') {
                        message.success(res.message);
                    } else {
                        message.warning(res.message);
                    }
                }, 'json');
            }
        },
        getDataForm: function () {
            return {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
            }
        }
    }
}
</script>
