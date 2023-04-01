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
            </table>
        </div>
        <div class="body">
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
                    @foreach($cashier_dets as $i => $t)
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
                    @foreach($cashiers['row2'] as $v)
                    <td colspan="2">{{ $v[0] }}</td>
                    <td class="bold" align="center">Value</td>
                    <td class="bold" align="center">Total</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row3'] as $v)
                    <td class="bold" align="center">Total Non Cash : </td>
                    <td class="bold" align="center">Total Cash Sales : </td>
                    <td class="bold">Rp 100</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row4'] as $v)
                    <td rowspan="2">{{ $v[0] }}</td>
                    <td rowspan="2">{{ $v[1] }}</td>
                    <td class="bold">Rp 200</td>
                    <td>{{ $v[2] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row5'] as $v)
                    <td class="bold">Rp 500</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row6'] as $v)
                    <td class="bold" align="center">Brankas Money : </td>
                    <td class="bold" align="center">Pending PC : </td>
                    <td class="bold">Rp 1.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row7'] as $v)
                    <td rowspan="2">{{ $v[0] }}</td>
                    <td rowspan="2">{{ $v[1] }}</td>
                    <td class="bold">Rp 2.000</td>
                    <td>{{ $v[2] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row8'] as $v)
                    <td class="bold">Rp 5.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row9'] as $v)
                    <td class="bold" align="center">Hand Over By : </td>
                    <td class="bold" align="center">Received By : </td>
                    <td class="bold">Rp 10.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row10'] as $v)
                    <td class="bold" rowspan="3">{{ $v[0] }}</td>
                    <td class="bold" rowspan="3">{{ $v[1] }}</td>
                    <td class="bold">Rp 20.000</td>
                    <td>{{ $v[2] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row11'] as $v)
                    <td class="bold">Rp 50.000</td>
                    <td>{{ $v[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($cashiers['row12'] as $v)
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
                                <td>{{ $lbMonSls->name }}</td>
                            </tr>
                            <tr>
                                <td class="bold">NIK</td>
                                <td>{{ $lbMonSls->nik }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Function</td>
                                <td>{{ $lbMonSls->function }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Total Money</td>
                                <td>{{  ( is_numeric($lbMonSls->total_money)  ) ? App\Library\Helper::convertNumberToInd($lbMonSls->total_money, '', 0) : $lbMonSls->total_money }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Deposit Date</td>
                                <td>{{ ($lbMonSls->deposit_date) ? App\Library\Helper::DateConvertFormat($lbMonSls->deposit_date, 'Y-m-d', 'd-m-Y') : $lbMonSls->deposit_date }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Deposit To</td>
                                <td>{{ $lbMonSls->deposit_to }}</td>
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
                            @foreach($lbMonSlsDets as $lbMonSlsDet)
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
                                <td>{{  ( is_numeric($lbMonSls->dp_ulang_tahun)  ) ? App\Library\Helper::convertNumberToInd($lbMonSls->dp_ulang_tahun, 'Rp. ', 0) : $lbMonSls->dp_ulang_tahun }}</td>
                            </tr>
                            <tr>
                                <td class="bold">DP Big Order</td>
                                <td>{{  ( is_numeric($lbMonSls->dp_big_order)  ) ? App\Library\Helper::convertNumberToInd($lbMonSls->dp_big_order, 'Rp. ', 0) : $lbMonSls->dp_big_order }}</td>
                            </tr>
                            <tr>
                                <td class="bold">Total</td>
                                <td>{{  ( is_numeric($lbMonSls->dp_ulang_tahun + $lbMonSls->dp_big_order)  ) ? App\Library\Helper::convertNumberToInd($lbMonSls->dp_ulang_tahun + $lbMonSls->dp_big_order, 'Rp. ', 0) : $lbMonSls->dp_ulang_tahun + $lbMonSls->dp_big_order }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </div>
    </div>
	</body>
</html>
