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
                    <td><strong>Outlet :</strong> {{$header['outlet']}}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{$header['date']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            @foreach($data as $d)
                <table>
                    <tr>
                        <th width="40%" colspan="2"><strong>{{ $d['shift'] }} Briefing</strong></th>
                        <th width="60%" align="center" colspan="5"><strong>Duty Roster</strong></th>
                    </tr>
                    @foreach($d['rows'] as $row)
                    <tr>
                        <td width="15%"><strong>{{ $row['col1'] }}</strong></td>
                        <td width="15%">{{ $row['col2'] }}</td>
                        <td width="12%"><strong>{{ $row['col3'] }}</strong></td>
                        <td width="12%">{{ $row['col4'] }}</td>
                        <td width="12%">{{ $row['col5'] }}</td>
                        <td width="12%">{{ $row['col6'] }}</td>
                        <td width="12%">{{ $row['col7'] }}</td>
                    </tr>
                    @endforeach

                    {{-- <tr>
                        <td>MTD Sales</td>
                        <td align="right">{{ App\Library\Helper::convertNumberToInd($d['briefings']->mtd_sales, '', 0) }}</td>
                    </tr>
                    <tr>
                        <td>Today's Highlight</td>
                        <td>{{ $d['briefings']->highlight }}</td>
                    </tr>
                    <tr>
                        <td>RF Updates</td>
                        <td>{{ $d['briefings']->rf_updates }}</td>
                    </tr> --}}
                </table>
            @endforeach
        </div>
    </div>
	</body>
</html>
