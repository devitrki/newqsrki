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

        .report .body table th {
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
            text-align: center;
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
                    <td width="33.3%"><strong>Outlet :</strong> {{$header['outlet']}}</td>
                    <td width="33.3%"><strong>Date :</strong> {{$header['date']}}</td>
                    <td width="33.3%"><strong>MOD :</strong> {{$header['mod']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">Product Name</th>
                        <th rowspan="2">OUM</th>
                        <th rowspan="2">Frekuensi</th>
                        <th rowspan="2">Opening Stock</th>
                        <th colspan="4">Stock In</th>
                        <th colspan="4">Stock Out</th>
                        <th rowspan="2">Closing Stock</th>
                        <th rowspan="2">Note</th>
                    </tr>
                    <tr>
                        <th>GR Plant</th>
                        <th>DC</th>
                        <th>Vendor</th>
                        <th>Section</th>
                        <th>GI Plant</th>
                        <th>DC</th>
                        <th>Vendor</th>
                        <th>Section</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $t)
                    <tr>
                        <td>{{$t->product_name}}</td>
                        <td>{{$t->uom}}</td>
                        <td>{{$t->frekuensi}}</td>
                        <td align="right">{{$t->stock_opening}}</td>
                        <td align="right">{{$t->stock_in_gr_plant}}</td>
                        <td align="right">{{$t->stock_in_dc}}</td>
                        <td align="right">{{$t->stock_in_vendor}}</td>
                        <td align="right">{{$t->stock_in_section}}</td>
                        <td align="right">{{$t->stock_out_gi_plant}}</td>
                        <td align="right">{{$t->stock_out_dc}}</td>
                        <td align="right">{{$t->stock_out_vendor}}</td>
                        <td align="right">{{$t->stock_out_section}}</td>
                        <td align="right">{{$t->stock_closing}}</td>
                        <td>{{$t->note}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
	</body>
</html>
