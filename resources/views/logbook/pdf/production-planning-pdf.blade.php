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
                <tr>
                    <td><strong>Product :</strong> {{$item['header']['product']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            @empty($item['data'])
            @else
            <table>
                <tbody>
                    @for ($r = 0; $r < 5; $r++)
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td colspan="6" align="center">{{ $item['data']['lbProdTimes']['time'][$i] }}</td>
                            @endfor
                            @if($r == 4)
                            <td colspan="6" rowspan="8" align="center" style="border-right: 1px solid #FFFFFF;border-bottom: 1px solid #FFFFFF;">&nbsp;</td>
                            @endif
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td align="center">Planning</td>
                            <td>Quantity</td>
                            @foreach($item['data']['lbProdTimes']['quantity'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td rowspan="3" align="center">{{ $item['data']['lbProdTimes']['planning'][$i] }}</td>
                            <td>Exp & Prod Code</td>
                            @foreach($item['data']['lbProdTimes']['exp_prod_code'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td>Fryer</td>
                            @foreach($item['data']['lbProdTimes']['fryer'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td>Temperature</td>
                            @foreach($item['data']['lbProdTimes']['temperature'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td align="center">Total</td>
                            <td>Holding Time</td>
                            @foreach($item['data']['lbProdTimes']['holding_time'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td rowspan="2" align="center">{{ $item['data']['lbProdTimes']['actual'][$i] }}</td>
                            <td>Self Life</td>
                            @foreach($item['data']['lbProdTimes']['self_life'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td>Vendor</td>
                            @foreach($item['data']['lbProdTimes']['vendor'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>

            <table style="width: 100px;margin-top:20px;">
                <thead>
                    <tr>
                        <td align="center">Total Usage</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center">{{ $item['data']['lbProdPlan']->total_usage }}</td>
                    </tr>
                </tbody>
            </table>

            <h5 style="margin-bottom:10px;margin-left:5px;">CHICKEN INTERNAL TEMPERATURE</h5>
            <table class="no-border" style="width: 80%;">
                <tr>
                    <td class="no-border" style="vertical-align: baseline;width: 55%;">

                        <table>
                            <thead>
                                <tr>
                                    <td align="center">No</td>
                                    <td align="center">Food Name</td>
                                    <td align="center">Time</td>
                                    <td align="center">Fryer Temperature</td>
                                    <td align="center">Product Temperature</td>
                                    <td align="center">OK / Not OK (Product)</td>
                                    <td align="center">Corrective Action</td>
                                    <td align="center">PIC</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach($item['data']['lbProdTemps'] as $lbProdTemp)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $lbProdTemp->food_name }}</td>
                                    <td>{{ $lbProdTemp->time }}</td>
                                    <td>{{ $lbProdTemp->fryer_temp }}</td>
                                    <td>{{ $lbProdTemp->product_temp }}</td>
                                    <td>{{ $lbProdTemp->result }}</td>
                                    <td>{{ $lbProdTemp->corrective_action }}</td>
                                    <td>{{ $lbProdTemp->pic }}</td>
                                </tr>
                                @php
                                    $no++;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>

                    </td>
                    <td class="no-border" style="vertical-align: baseline;width: 45%;">

                        <table>
                            <thead>
                                <tr>
                                    <td colspan="4" align="center">Fryer Temperature Verification</td>
                                    <td rowspan="2" align="center">Note</td>
                                </tr>
                                <tr>
                                    <td align="center">Fryer</td>
                                    <td align="center">Shift 1</td>
                                    <td align="center">Shift 2</td>
                                    <td align="center">Shift 3</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item['data']['lbProdTempVerifys'] as $lbProdTempVerify)
                                <tr>
                                    <td>{{ $lbProdTempVerify->fryer }}</td>
                                    <td>{{ $lbProdTempVerify->shift1_temp }}</td>
                                    <td>{{ $lbProdTempVerify->shift2_temp }}</td>
                                    <td>{{ $lbProdTempVerify->shift3_temp }}</td>
                                    <td>{{ $lbProdTempVerify->note }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </td>
                </tr>
            </table>

            <h5 style="margin-bottom:10px;margin-left:5px;">OIL QUALITY CHECKING WITH VITO</h5>
            <table style="width: 70%;">
                <thead>
                    <tr>
                        <td rowspan="2" align="center">Time</td>
                        @foreach($item['data']['lfryers'] as $fryer)
                        <td colspan="4">Fryer : {{ $fryer }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        @for ($i = 0; $i < 4; $i++)
                        <td align="center">TMP (%)</td>
                        <td align="center">Temp (C)</td>
                        <td align="center">Change = X, Refill = L /lbs</td>
                        <td align="center">Filtration</td>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $item['data']['lbProdPlan']->time_check_quality }}</td>
                        @foreach($item['data']['lfryers'] as $fryer)
                        <td>{{ $item['data']['lbProdQualities'][$fryer]->tpm }}</td>
                        <td>{{ $item['data']['lbProdQualities'][$fryer]->temp }}</td>
                        <td>{{ $item['data']['lbProdQualities'][$fryer]->oil_status }}</td>
                        <td>{{ $item['data']['lbProdQualities'][$fryer]->filtration }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>

            <h5 style="margin-bottom:10px;margin-left:5px;">USED OIL</h5>
            <table style="margin-bottom: 40px;width: 60%;">
                <thead>
                    <tr>
                        <td rowspan="2" align="center">Stock First</td>
                        <td rowspan="2" align="center">Stock In GR</td>
                        <td colspan="5" align="center">Stock In</td>
                        <td rowspan="2" align="center">Stock Out</td>
                        <td rowspan="2" align="center">Stock Last</td>
                    </tr>
                    <tr>
                        <td align="center">Fryer A</td>
                        <td align="center">Fryer B</td>
                        <td align="center">Fryer C</td>
                        <td align="center">Fryer D</td>
                        <td align="center">Oil Change</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_first }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_in_gr }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_in_fryer_a }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_in_fryer_b }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_in_fryer_c }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_in_fryer_d }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_change_oil }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_out }}</td>
                        <td>{{ $item['data']['lbProdUsedOil']->stock_last }}</td>
                    </tr>
                </tbody>
            </table>
            @endempty
        </div>
    </div>
    @endforeach
@endsection
