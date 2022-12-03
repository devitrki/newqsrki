<div {{ $attributes->merge(['class' => 'tools-action']) }} >
    @isset($left)
    <div class="tools-action-left d-flex align-items-center">
        <ul class="list-inline m-0 d-flex">
            {{ $left }}
        </ul>
    </div>
    @endisset
    @isset($right)
    <div class="tools-action-right d-flex flex-grow-1 align-items-center justify-content-end">
        <ul class="list-inline list-inline-right m-0 d-flex ">
            {{ $right }}
        </ul>
    </div>
    @endisset
</div>