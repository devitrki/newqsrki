<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="right">
                    <x-row-tools>
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Store">
                                        <x-select :dom="$dom" compid="fstore" type="serverside" url="master/plant/select?type=outlet&auth=true&ext=all&pos=aloha" size="sm" :default="[0, __('All')]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="From Date">
                                        <x-pickerdate :dom="$dom" compid="ffromdate" data-value="{{ date('Y/m/d', strtotime('-1 days')) }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Until Date">
                                        <x-pickerdate :dom="$dom" compid="funtildate" data-value="{{ date('Y/m/d', strtotime('-1 days')) }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Status">
                                        @php
                                            $options = [
                                                        ['id' => 'A', 'text' => __('All')],
                                                        ['id' => 'S', 'text' => __('Success')],
                                                        ['id' => 'E', 'text' => __('Error')],
                                                    ];
                                        @endphp
                                        <x-select :dom="$dom" compid="fstatus" type="array" :options="$options" size="sm"/>
                                    </x-row-vertical>
                                </x-form-vertical>
                            </div>
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.show()">
                                    <span>{{ __('Show') }}</span>
                                </button>
                            </div>
                        </x-dropdown-filter>
                    </x-row-tools>
                </x-slot>
            </x-tools>

            {{-- frame --}}
            <div class="framereport" id="frame{{$dom}}">

            </div>
            {{-- frame --}}

        </div>
    </div>
</x-card-scroll>

<script>
$( document ).ready(function() {
    {{$dom}}.func.show();
});

{{$dom}} = {
   data: {
        export: '',
    },
    url: {
        report: "report/interfaces/transaction-log-sap-aloha/report",
    },
    func: {
        show: function () {
            dropdown.hide('hfiltertabledata{{$dom}}');
            loading('start', '{{ __("Generate Report") }}', 'process');

            var url = {{$dom}}.url.report +
                       '?store=' + fslctfstore{{$dom}}.get() +
                       '&from-date=' + pickerdateffromdate{{$dom}}.get('select', 'yyyy/mm/dd') +
                       '&until-date=' + pickerdatefuntildate{{$dom}}.get('select', 'yyyy/mm/dd') +
                       '&status=' + fslctfstatus{{$dom}}.get();
            $('#frame{{$dom}}').load( url, function (response, status, xhr) {
                loading("stop");
            });
        }
    }
}

</script>
