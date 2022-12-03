<div class="card card-tabs fit-screen-tabs">
    @isset($header)
    <div class="card-header">
        {{ $header }}
    </div>
    @endisset
    <div class="card-content overflow-auto">
        {{ $slot }}
    </div>
</div>
