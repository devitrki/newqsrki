<div class="btn-group">
    <button type="button" id="hbtnfilter{{$dtblecompid . $dom}}" class="btn btn-sm btn-outline-default action-icon bw-thin" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="bx bx-filter-alt"></i></button>
    <div class="dropdown-menu dropdown-menu-filter" id="hfilter{{$dtblecompid . $dom}}">
        <div class="users-list-filter px-1">
            <form>
                <div class="row py-1">
                    {{ $slot }}
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$("#hbtnfilter{{$dtblecompid . $dom}}").on("click change", function(e) {
    hideErrors();
});
</script>