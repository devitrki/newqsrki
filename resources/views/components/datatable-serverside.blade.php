<div class="table-responsive pdtble{{ $compid.$dom }} {!! $getTagHeight($height) !!}>
    <table class="table nowrap table-striped table-bordered" id="datatable{{ $compid.$dom }}" style="width:100%">
        <thead>
            <tr>
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

        @if($footer != 'true')
            var height{{ $compid.$dom }} = 34;
        @else
            var height{{ $compid.$dom }} = 68;
        @endif

        idtble{{ $compid.$dom }} = $('#datatable{{ $compid.$dom }}').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! $url !!}'
            },
            columns: [
                @foreach( $columns as $col )
                {
                    @isset( $col["data"] )
                    data: '{{ $col["data"] }}',
                    @endisset
                    @isset( $col["searchable"] )
                    searchable: {{ $col["searchable"] }},
                    @endisset
                    @isset( $col["orderable"] )
                    orderable: {{ $col["orderable"] }},
                    @endisset
                    @isset( $col["width"] )
                    width: '{{ $col["width"] }}',
                    @endisset
                    @isset( $col["class"] )
                    className: '{{ $col["class"] }}',
                    @endisset
                    @isset( $col["format"] )
                    @if( $col["format"] == 'datetime' )
                    render: function (data, type, row, meta) {
                        if (data) {
                            return moment.utc(data).local().format('DD/MM/YYYY HH:mm:ss');
                        } else {
                            return '';
                        }

                    }
                    @endif
                    @if( $col["format"] == 'date' )
                    render: function (data, type, row, meta) {
                        if (data) {
                            return moment.utc(data).local().format('DD/MM/YYYY');
                        } else {
                            return '';
                        }

                    }
                    @endif
                    @if( $col["format"] == 'postingdate' )
                    render: function (data, type, row, meta) {
                        if (data && row.submit != 0) {
                            return moment.utc(data).local().format('DD/MM/YYYY HH:mm:ss');
                        } else {
                            return '';
                        }

                    }
                    @endif
                    @endisset
                },
                @endforeach
            ],
            @if( !empty($order) )
                order: [[ {{ $order[0] }}, "{{ $order[1] }}" ]],
            @else
                ordering: false,
            @endif
            scrollY: $( '.pdtble{{ $compid.$dom }}' ).height() - height{{ $compid.$dom }},
            buttons: [
                'copy',
                'print',
            ],
            @if($footer != 'true')
            paging: false,
            info: false,
            @else
            lengthMenu: [
                [
                    @foreach($lengthMenu[0] as $len)
                        {{ $len }},
                    @endforeach
                ],
                [
                    @foreach($lengthMenu[1] as $len)
                        {{ $len }},
                    @endforeach
                ]
            ],
            @endif
            @if( $rowReorder == 'true' )
            rowReorder: {
                update: false,
            },
            @endif
            @if( $colReorder == 'true' )
            colReorder: {{ $colReorder }},
            @endif
            @if( $scroller == 'true' )
            scroller: {{ $scroller }},
            @endif
            @if( $fixedColumns[0] )
            fixedColumns:   {
                leftColumns: {{ $fixedColumns[1] }}
            },
            @endif
            @if( $select[0] )
            select: {
                style: '{{ $select[1] }}'
            },
            @endif
        });

        // event for options
        $('#searchDtble{{ $compid.$dom }}').keyup(function(e){
            if(e.keyCode == 13)
            {
                idtble{{ $compid.$dom }}.search($(this).val()).draw() ;
            }
        })
        $('#btnCopyDtble{{ $compid.$dom }}').on("click", function () {
            idtble{{ $compid.$dom }}.button( '.buttons-copy' ).trigger();
        })
        $('#btnPrintDtble{{ $compid.$dom }}').on("click", function () {
            idtble{{ $compid.$dom }}.button( '.buttons-print' ).trigger();
        })

        $(window).resize(function() {
            $('#datatable{{ $compid.$dom }}').DataTable( {
                scrollY: $( '.pdtble{{ $compid.$dom }}' ).height() - height{{ $compid.$dom }},
                retrieve: true,
            });
        });
        @if($tabmenu != '')
        $(document).on('shown.bs.tab', 'a[href="#tab{{$tabmenu}}"]', function (e) {
            fdtble{{ $compid.$dom }}.refresh();
        })
        @endif
        @if($compidmodal != '')
        $('#{{ $compidmodal.$dom }}').on('shown.bs.modal', function () {
            fdtble{{ $compid.$dom }}.refresh();
        });
        @endif

        fdtble{{ $compid.$dom }} = {
            draw: function () {
                idtble{{ $compid . $dom }}.draw();
            },
            refresh: function () {
                idtble{{ $compid . $dom }}.ajax.reload();
            },
            adjust: function () {
                idtble{{ $compid . $dom }}.columns.adjust();
            },
            changeUrl: function(url){
                idtble{{ $compid . $dom  }}.ajax.url( url ).load();
            },
            getRowIndex: function(index){
                return idtble{{ $compid.$dom }}.row( index ).data();
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
            },
            changeNameColumn: function (index, text) {
                idtble{{ $compid.$dom }}.columns(index).header().to$().text(text);
            }
        }

        @if($dblclick)
        // event double click
        var touchtime{{ $compid.$dom }} = new Date().getTime();
        var lastTarget{{ $compid.$dom }} = '';

        $("#datatable{{ $compid.$dom }} tbody").on("click", 'tr', function(e) {
            if ( (lastTarget{{ $compid.$dom }} == e.target) && ( (new Date().getTime()) - touchtime{{ $compid.$dom }}) < 800) {
                // double click occurred
                var data = idtble{{ $compid.$dom }}.row( this ).data();
                touchtime{{ $compid.$dom }} = 0;

                callback{{ $compid.$dom }}(data);
            } else {
                // not a double click so set as a new first click
                touchtime{{ $compid.$dom }} = new Date().getTime();
                lastTarget{{ $compid.$dom }} = e.target;
            }
        });
        @endif
    });
</script>
@endif
