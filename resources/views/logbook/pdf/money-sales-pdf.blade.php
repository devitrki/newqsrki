@extends('reports.templates.pdf.template-logbook')
@section('style')
    <style>
        main .body table td.bold {
            font-weight: 700;
        }
        main .body table th.no-border,
        main .body table td.no-border {
            border: 0px solid #ddd;
        }
    </style>
@endsection
@section('title')
{{ $title }}
@endsection
@section('content')
    @foreach($data['items'] as $item)
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Outlet :</strong> {{$item['header']['plant']}}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{$item['header']['date']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            @empty($item['data'])
            @else
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">&nbsp;</th>
                        <th colspan="4">Opening Cashier</th>
                        <th colspan="4">Midnight Cashier</th>
                        <th colspan="4">Closing Cashier</th>
                    </tr>
                    <tr>
                        {{-- Opening cashier --}}
                        <th>Cashier 1</th>
                        <th>Cashier 2</th>
                        <th>Cashier 3</th>
                        <th>Cashier 4</th>
                        {{-- Midnight cashier --}}
                        <th>Cashier 1</th>
                        <th>Cashier 2</th>
                        <th>Cashier 3</th>
                        <th>Cashier 4</th>
                        {{-- Closing cashier --}}
                        <th>Cashier 1</th>
                        <th>Cashier 2</th>
                        <th>Cashier 3</th>
                        <th>Cashier 4</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item['data']['cashier_dets'] as $i => $t)
                    <tr>
                        @for($i = 0; $i <= 12; $i++)
                        <td>{{ $t[$i] }}</td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <table>
                <tr>
                    @for($i = 0; $i < 3; $i++)
                    <td colspan="2" class="bold">Total Sales : </td>
                    <td colspan="2" class="bold" align="center">Brankas Money </td>
                    @endfor
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row2'] as $v)
                    <td colspan="2">{{ $v[0] }}</td>
                    <td class="bold" align="center">Value</td>
                    <td class="bold" align="center">Total</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row3'] as $v)
                    <td class="bold" align="center">Total Non Cash : </td>
                    <td class="bold" align="center">Total Cash Sales : </td>
                    <td class="bold">Rp 100</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row4'] as $v)
                    <td rowspan="2">{{ $v[0] }}</td>
                    <td rowspan="2">{{ $v[1] }}</td>
                    <td class="bold">Rp 200</td>
                    <td>{{ $v[2] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row5'] as $v)
                    <td class="bold">Rp 500</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row6'] as $v)
                    <td class="bold" align="center">Brankas Money : </td>
                    <td class="bold" align="center">Pending PC : </td>
                    <td class="bold">Rp 1.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row7'] as $v)
                    <td rowspan="2">{{ $v[0] }}</td>
                    <td rowspan="2">{{ $v[1] }}</td>
                    <td class="bold">Rp 2.000</td>
                    <td>{{ $v[2] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row8'] as $v)
                    <td class="bold">Rp 5.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row9'] as $v)
                    <td class="bold" align="center">Hand Over By : </td>
                    <td class="bold" align="center">Received By : </td>
                    <td class="bold">Rp 10.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row10'] as $v)
                    <td class="bold" rowspan="3">{{ $v[0] }}</td>
                    <td class="bold" rowspan="3">{{ $v[1] }}</td>
                    <td class="bold">Rp 20.000</td>
                    <td>{{ $v[2] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row11'] as $v)
                    <td class="bold">Rp 50.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($item['data']['cashiers']['row12'] as $v)
                    <td class="bold">Rp 100.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
            </table>
            <br>

            <table class="no-border">
                <tr>
                    <td class="no-border" style="vertical-align: baseline;width: 20%;">
                        <table>
                            <tr>
                                <td colspan="2" class="bold">Money Deposit Report</td>
                            </tr>
                            <tr>
                                <td class="bold">Name</td>
                                <td>{{ $item['data']['lbMonSls']->name }}</td>
                            </tr>
                            <tr>
                                <td class="bold">NIK</td>
                                <td>{{ $item['data']['lbMonSls']->nik }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Function</td>
                                <td>{{ $item['data']['lbMonSls']->function }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Total Money</td>
                                <td>{{  ( is_numeric($item['data']['lbMonSls']->total_money)  ) ? App\Library\Helper::convertNumberToInd($item['data']['lbMonSls']->total_money, '', 0) : $item['data']['lbMonSls']->total_money }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Deposit Date</td>
                                <td>{{ ($item['data']['lbMonSls']->deposit_date) ? App\Library\Helper::DateConvertFormat($item['data']['lbMonSls']->deposit_date, 'Y-m-d', 'd-m-Y') : $item['data']['lbMonSls']->deposit_date }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Deposit To</td>
                                <td>{{ $item['data']['lbMonSls']->deposit_to }}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="no-border" style="vertical-align: baseline;width: 40%;">
                        <table>
                            <tr>
                                <td class="bold">Date</td>
                                <td class="bold">Day</td>
                                <td class="bold">Total Cash</td>
                                <td class="bold">Total Non Cash</td>
                                <td class="bold">Total Sales</td>
                                <td class="bold">Hand Over By</td>
                                <td class="bold">Received By</td>
                            </tr>
                            @foreach($item['data']['lbMonSlsDets'] as $lbMonSlsDet)
                            <tr>
                                <td>{{ $lbMonSlsDet->date }}</td>
                                <td>{{ $lbMonSlsDet->day }}</td>
                                <td>{{  ( is_numeric($lbMonSlsDet->cash)  ) ? App\Library\Helper::convertNumberToInd($lbMonSlsDet->cash, 'Rp. ', 0) : $lbMonSlsDet->cash }}</td>
                                <td>{{  ( is_numeric($lbMonSlsDet->total_non_cash)  ) ? App\Library\Helper::convertNumberToInd($lbMonSlsDet->total_non_cash, 'Rp. ', 0) : $lbMonSlsDet->total_non_cash }}</td>
                                <td>{{  ( is_numeric($lbMonSlsDet->total_sales)  ) ? App\Library\Helper::convertNumberToInd($lbMonSlsDet->total_sales, 'Rp. ', 0) : $lbMonSlsDet->total_sales }}</td>
                                <td>{{ $lbMonSlsDet->hand_over_by }}</td>
                                <td>{{ $lbMonSlsDet->received_by }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </td>
                    <td class="no-border" style="vertical-align: baseline;width: 20%;">
                        <table>
                            <tr>
                                <td colspan="2" class="bold" align="center">Memo</td>
                            </tr>
                            <tr>
                                <td class="bold">DP Birthday</td>
                                <td>{{  ( is_numeric($item['data']['lbMonSls']->dp_ulang_tahun)  ) ? App\Library\Helper::convertNumberToInd($item['data']['lbMonSls']->dp_ulang_tahun, 'Rp. ', 0) : $item['data']['lbMonSls']->dp_ulang_tahun }}</td>
                            </tr>
                            <tr>
                                <td class="bold">DP Big Order</td>
                                <td>{{  ( is_numeric($item['data']['lbMonSls']->dp_big_order)  ) ? App\Library\Helper::convertNumberToInd($item['data']['lbMonSls']->dp_big_order, 'Rp. ', 0) : $item['data']['lbMonSls']->dp_big_order }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Total</td>
                                <td>{{  ( is_numeric($item['data']['lbMonSls']->dp_ulang_tahun + $item['data']['lbMonSls']->dp_big_order)  ) ? App\Library\Helper::convertNumberToInd($item['data']['lbMonSls']->dp_ulang_tahun + $item['data']['lbMonSls']->dp_big_order, 'Rp. ', 0) : $lbMonSls->dp_ulang_tahun + $lbMonSls->dp_big_order }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @endempty
        </div>
    </div>
    @endforeach
@endsection
