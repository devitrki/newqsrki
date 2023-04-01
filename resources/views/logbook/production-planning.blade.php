<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0">
            <x-tools>
                <x-slot name="right">
                    <x-row-tools>
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Plant">
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Date">
                                        <x-pickerdate :dom="$dom" compid="fdate" data-value="{{ date('Y/m/d') }}" clear="false" />
                                    </x-row-vertical>
                                    <x-row-vertical label="Product">
                                        <x-select :dom="$dom" compid="fproduct" type="serverside" url="logbook/mapping/product-production-planning/select" size="sm" dropdowncompid="tabledata" :default="[$first_product, $first_product]"/>
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
                </x-slot>
            </x-tools>
            {{-- frame --}}
            <div class="framereport" id="frame{{$dom}}">

            </div>
            {{-- frame --}}
        </div>
    </div>
</x-card-scroll>

<!-- modal -->
<!-- end modal -->
<script>
    $( document ).ready(function() {
        {{$dom}}.func.filter();
    });

    {{$dom}} = {
        data: {

        },
        url: {
            view: "logbook/production-planning/dataview"
        },
        event: {
        },
        func: {
            filter: function () {
                loading('start', '{{ __("Loading") }}', 'process');

                var product = fslctfproduct{{$dom}}.get().replace(" ", "%20") ;

                var url = {{$dom}}.url.view + '?plant-id=' + fslctfplant{{$dom}}.get() +
                        '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd') +
                        '&product=' + product;

                $('#frame{{$dom}}').load( url, function (response, status, xhr) {
                    dropdown.hide('hfiltertabledata{{$dom}}');
                    loading("stop");
                });
            }
        }
    }
</script>
