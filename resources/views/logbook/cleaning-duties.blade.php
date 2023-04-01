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
                                </x-form-vertical>
                                <x-row-vertical label="Section">
                                    <select class="form-control form-control-sm" id="fsection{{$dom}}">
                                        @php
                                            $options = [
                                                        ['value' => 'Cashier', 'text' => 'Cashier'],
                                                        ['value' => 'Lobby', 'text' => 'Lobby'],
                                                        ['value' => 'Kitchen', 'text' => 'Kitchen']
                                                    ];
                                        @endphp
                                        @foreach( $options as $i => $opt)
                                            @if($i == 0)
                                            <option value="{{$opt['value']}}" selected>{{$opt['text']}}</option>
                                            @else
                                            <option value="{{$opt['value']}}">{{$opt['text']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </x-row-vertical>
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
    setTimeout(function(){ 
        {{$dom}}.func.filter();
    }, 500);
});

{{$dom}} = {
    data: {

    },
    url: {
        view: "logbook/operational/cleaning-duties/dataview"
    },
    event: {
    },
    func: {
        filter: function () {
            loading('start', '{{ __("Loading") }}', 'process');

            var url = {{$dom}}.url.view + '?plant-id=' + fslctfplant{{$dom}}.get() +
                    '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd') +
                    '&section=' + $("#fsection{{$dom}}").val();

            $('#frame{{$dom}}').load( url, function (response, status, xhr) {
                dropdown.hide('hfiltertabledata{{$dom}}');
                loading("stop");
            });
        }
    }
}
</script>
