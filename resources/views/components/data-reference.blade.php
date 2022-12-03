<x-modal :dom="$dom" compid="modal{{$compid}}" :title="$title" :size="$size">
    <x-tools class="border">
        <x-slot name="right">
            <x-row-tools>
                <x-input-search :dom="$dom" dtblecompid="table{{$compid}}" />
            </x-row-tools>
        </x-slot>
    </x-tools>
    <x-datatable-serverside :dom="$dom" compid="table{{$compid}}" :columns="$columns" :url="$url" compidmodal="modal{{$compid}}" footer="false" :height="$height" :select="[true, 'single']" :dblclick="true" />
</x-modal>
