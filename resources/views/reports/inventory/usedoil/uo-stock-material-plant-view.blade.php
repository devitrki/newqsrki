<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="right">
                    <x-row-tools>
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Plant">
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
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

<!-- end modal -->
<script>
$( document ).ready(function() {
    {{$dom}}.func.show();
});

{{$dom}} = {
    url: {
        report: "report/inventory/uo-stock-material-plant/report",
    },
    func: {
        show: function () {
            loading('start', '{{ __("Generate Report") }}', 'process');

            var url ={{$dom}}.url.report + '?plant-id=' + fslctfplant{{$dom}}.get();
            $('#frame{{$dom}}').load( url, function (response, status, xhr) {
                dropdown.hide('hfiltertabledata{{$dom}}');
                loading("stop");
            });
        }
    }
}

</script>
