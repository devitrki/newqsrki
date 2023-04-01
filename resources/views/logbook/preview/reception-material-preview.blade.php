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
                        <th>No</th>
                        <th>Product</th>
                        <th>Transport Temperature</th>
                        <th>Transport Cleanliness</th>
                        <th>Product Temperature</th>
                        <th>Producer</th>
                        <th>Country</th>
                        <th>Supplier</th>
                        <th>Logo Halal</th>
                        <th>Product Condition</th>
                        <th>Production Code</th>
                        <th>Qty</th>
                        <th>UOM</th>
                        <th>Expired Date</th>
                        <th>Status</th>
                        <th>PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $i => $t)
                    <tr>
                        <td>{{$i + 1}}</td>
                        <td>{{$t->product}}</td>
                        <td>{{$t->transport_temperature}}</td>
                        <td>{{$t->transport_cleanliness}}</td>
                        <td>{{$t->product_temperature}}</td>
                        <td>{{$t->producer}}</td>
                        <td>{{$t->country}}</td>
                        <td>{{$t->supplier}}</td>
                        <td>{{$t->logo_halal}}</td>
                        <td>{{$t->product_condition}}</td>
                        <td>{{$t->production_code}}</td>
                        <td>{{$t->product_qty}}</td>
                        <td>{{$t->product_uom}}</td>
                        <td>{{$t->expired_date}}</td>
                        <td>{{$t->status}}</td>
                        <td>{{$t->pic}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
	</body>
</html>
