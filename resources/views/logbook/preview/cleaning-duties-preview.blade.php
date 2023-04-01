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
                    <td width="33.3%"><strong>Section :</strong> {{$header['section']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <tbody>
                    {{-- daily --}}
                    <tr>
                        <th>No</th>
                        <th colspan="2">Daily Task</th>
                        <th>Opening</th>
                        <th>Closing</th>
                        <th colspan="2">Midnite</th>
                    </tr>
                    @foreach($data['daily'] as $i => $t)
                    <tr>
                        <td>{{$i + 1}}</td>
                        <td colspan="2">{{$t->task}}</td>
                        <td>
                            @if($t->opening == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>
                            @if($t->closing == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td colspan="2">
                            @if($t->midnite == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    {{-- weekly --}}
                    <tr>
                        <th>No</th>
                        <th>Weekly Task</th>
                        <th>Day</th>
                        <th>Opening</th>
                        <th>Closing</th>
                        <th>Midnite</th>
                        <th>pic</th>
                    </tr>
                    @foreach($data['weekly'] as $i => $t)
                    <tr>
                        <td>{{$i + 1}}</td>
                        <td>{{$t->task}}</td>
                        <td>{{$t->day}}</td>
                        <td>
                            @if($t->opening == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>
                            @if($t->closing == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>
                            @if($t->midnite == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>{{$t->pic}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="7">
                            <strong>Catatan:</strong>

                            @isset($data['weekly'][0]->note)
                            {{ $data['weekly'][0]->note }}
                            @endisset
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
	</body>
</html>
