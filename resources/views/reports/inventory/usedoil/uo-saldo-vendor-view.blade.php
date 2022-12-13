<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">

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
        report: "report/inventory/uo-saldo-vendor/report",
    },
    func: {
        show: function () {
            loading('start', '{{ __("Generate Report") }}', 'process');

            var url ={{$dom}}.url.report;
            $('#frame{{$dom}}').load( url, function (response, status, xhr) {
                dropdown.hide('hfiltertabledata{{$dom}}');
                loading("stop");
            });
        }
    }
}

</script>
