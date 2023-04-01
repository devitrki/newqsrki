<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>{{ config('app.name') }}</title>
		<style>
        @page {
            margin:20px;
        }

        .page-break {
            page-break-after: always;
        }

		body {
			font-family: "Rubik", sans-serif;
			line-height: 1.25;
			font-size: 12px;
			color: #000000;
			background-color: #ffffff;
		}

        .report {
            width: 100%;
        }

        .report .header table {
            width: 100%;
            border: 1px solid #ddd;
        }

        .report .header table td.logo {
            width: 30%;
        }

        .report .header table td.title {
            width: 70%;
            text-align: center;
        }

        .report .header img{
            width:250px;
            height: auto;
            background-position: center;
            max-height: 70px;
            min-height: 70px;
        }

        .report .header h1 {
            font-size: 16px;
        }

        .report .desc {
            margin: 1rem;
        }

        .report .desc table {
            width: 100%;
        }

        .report .body table {
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
            font-size: 11px;
        }

        .report .body table thead tr {
            background-color: #ececec;
        }

        .report .body table tr {
            padding: .35em;
        }

        .report .body table th,
        .report .body table td {
            padding: .425em;
            border: 1px solid #ddd;
        }

        .report .body table th.no-border,
        .report .body table td.no-border {
            padding: .425em;
            border: 0px solid #ddd;
        }

        .report .body table th {
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
            text-align: center;
        }

        .report .body table td.bold {
            font-weight: 700;
        }

		</style>
	</head>
	<body>
    <div class="report">
        <div class="header">
            <table>
                <tr>
                    <td class="logo"><img src="{{ asset( 'images/logo/rki.png' ) }}" alt="Richeese Kuliner Indonesia"></td>
                    <td class="title"><h1>{{ $title }}</h1></td>
                </tr>
            </table>
        </div>
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Outlet :</strong> {{$header['outlet']}}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{$header['date']}}</td>
                </tr>
                <tr>
                    <td><strong>Product :</strong> {{$header['product']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <tbody>
                    @for ($r = 0; $r < 5; $r++)
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td colspan="6" align="center">{{ $lbProdTimes['time'][$i] }}</td>
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td align="center">Planning</td>
                            <td>Quantity</td>
                            @foreach($lbProdTimes['quantity'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td rowspan="3" align="center">{{ $lbProdTimes['planning'][$i] }}</td>
                            <td>Exp & Prod Code</td>
                            @foreach($lbProdTimes['exp_prod_code'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td>Fryer</td>
                            @foreach($lbProdTimes['fryer'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td>Temperature</td>
                            @foreach($lbProdTimes['temperature'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td align="center">Total</td>
                            <td>Holding Time</td>
                            @foreach($lbProdTimes['holding_time'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td rowspan="2" align="center">{{ $lbProdTimes['actual'][$i] }}</td>
                            <td>Self Life</td>
                            @foreach($lbProdTimes['self_life'][$i] as $value)
                            <td>{{ $value }}</td>
                            @endforeach
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = $r * 5; $i < ($r * 5) + (($r == 4) ? 4 : 5); $i++)
                            <td>Vendor</td>
                            @foreach($lbProdTimes['vendor'][$i] as $value)
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
                        <td align="center">{{ $lbProdPlan->total_usage }}</td>
                    </tr>
                </tbody>
            </table>

            <h5 style="margin-bottom:0px;margin-left:5px;">CHICKEN INTERNAL TEMPERATURE</h5>
            <table class="no-border">
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
                                @foreach($lbProdTemps as $lbProdTemp)
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
                                @foreach($lbProdTempVerifys as $lbProdTempVerify)
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

            <h5 style="margin-bottom:0px;margin-left:5px;">OIL QUALITY CHECKING WITH VITO</h5>
            <table>
                <thead>
                    <tr>
                        <td rowspan="2" align="center">Time</td>
                        @foreach($lfryers as $fryer)
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
                        <td>{{ $lbProdPlan->time_check_quality }}</td>
                        @foreach($lfryers as $fryer)
                        <td>{{ $lbProdQualities[$fryer]->tpm }}</td>
                        <td>{{ $lbProdQualities[$fryer]->temp }}</td>
                        <td>{{ $lbProdQualities[$fryer]->oil_status }}</td>
                        <td>{{ $lbProdQualities[$fryer]->filtration }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>

            <h5 style="margin-bottom:0px;margin-left:5px;">USED OIL</h5>
            <table style="margin-bottom: 40px;">
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
                        <td>{{ $lbProdUsedOil->stock_first }}</td>
                        <td>{{ $lbProdUsedOil->stock_in_gr }}</td>
                        <td>{{ $lbProdUsedOil->stock_in_fryer_a }}</td>
                        <td>{{ $lbProdUsedOil->stock_in_fryer_b }}</td>
                        <td>{{ $lbProdUsedOil->stock_in_fryer_c }}</td>
                        <td>{{ $lbProdUsedOil->stock_in_fryer_d }}</td>
                        <td>{{ $lbProdUsedOil->stock_change_oil }}</td>
                        <td>{{ $lbProdUsedOil->stock_out }}</td>
                        <td>{{ $lbProdUsedOil->stock_last }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
	</body>
</html>
