<div class="table-responsive pdtble{{ $compid.$dom }} {!! $getTagHeight($height) !!}>
    <table class="table nowrap table-bordered" id="datatable{{ $compid.$dom }}" style="width:100%">
        <thead>
            <tr>
                @if($number)
                    <th></th>
                @endif
                @foreach( $columns as $col )
                <th>{{ __($col['label']) }}</th>
                @endforeach
            </tr>
        </thead>
    </table>
</div>

@if( $init )
<script>
    $(document).ready(function() {
        var height{{ $compid.$dom }} = 34;
        var dataset{{ $compid.$dom }} = [];

        idtble{{ $compid.$dom }} = $('#datatable{{ $compid.$dom }}').DataTable({
            data: dataset{{ $compid.$dom }},
            scrollY: $( '.pdtble{{ $compid.$dom }}' ).height() - height{{ $compid.$dom }},
            paging: false,
            info: false,
            ordering: false,
            select: {
                style: '{{ $select[1] }}'
            },
            @if (sizeof($className) > 0)
            columnDefs: [
                @foreach ( $className as $c )
                { className: "{{$c['class']}}", targets: {{$c['target']}} },
                @endforeach
            ],
            @endif

        });

        @if($number)
        idtble{{ $compid.$dom }}.on( 'order.dt search.dt', function () {
            idtble{{ $compid.$dom }}.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
        @endif

        @if($compidmodal != '')
        $('#{{ $compidmodal.$dom }}').on('shown.bs.modal', function () {
            fdtble{{ $compid.$dom }}.refresh();
        });
        @endif

        $('#searchDtble{{ $compid.$dom }}').keyup(function(e){
            if(e.keyCode == 13)
            {
                idtble{{ $compid.$dom }}.search($(this).val()).draw() ;
            }
        })

        fdtble{{ $compid.$dom }} = {
            draw: function () {
                idtble{{ $compid . $dom }}.draw();
            },
            add: function (item) {
                dataset{{ $compid.$dom }}.push(item);
            },
            update: function (index, item) {
                dataset{{ $compid.$dom }}[index] = item;
            },
            remove: function (index) {
                dataset{{ $compid.$dom }}.splice(index, 1);
            },
            clear: function (index) {
                dataset{{ $compid.$dom }} = [];
            },
            refresh: function () {
                idtble{{ $compid . $dom }}.clear();
                idtble{{ $compid . $dom }}.rows.add(dataset{{ $compid.$dom }});
                idtble{{ $compid . $dom }}.draw();
            },
            getRowIndex: function(index){
                var indexes = idtble{{ $compid.$dom }}.row( { selected: true }  ).indexes();
                var index = -1;
                if(indexes.length > 0){
                    index = indexes[0];
                }
                return index;
            },
            getArrayData: function () {
                return dataset{{ $compid.$dom }};
            },
            getAllData: function () {
                var data = idtble{{ $compid.$dom }}.rows().data();
                var alldata = [];
                for (var i=0; i < data.length ;i++){
                    alldata.push(data[i]);
                }
                return alldata;
            },
            getSelectedData : function () {
                var data = idtble{{ $compid.$dom }}.rows( { selected: true } ).data();
                var selected = [];
                for (var i=0; i < data.length ;i++){
                    selected.push(data[i]);
                }
                return selected;
            },
            getSelectedCount : function () {
                var data = idtble{{ $compid.$dom }}.rows( { selected: true } ).data();
                return data.length;
            }
        }

    });
</script>
@endif
