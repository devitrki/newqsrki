<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
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
			font-size: .70em;
			color: #000000;
			background-color: #ffffff;
		}

        .report {
            width: 100%;
            position: relative;
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
            max-height: 60px;
            min-height: 60px;
        }

        .report .header h1 {
            font-size: 14px;
        }

        .report .desc {
            margin: 0.6rem;
        }

        .report .desc table {
            width: 100%;
        }

        .report .desc table td {
            font-size: .80em;
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
            padding: .125em .425em;
            border: 1px solid #666666;
        }

        .report .body table th {
            font-size: .70em;
            letter-spacing: .1em;
            text-transform: uppercase;
            text-align: center;
        }

        .report .body table td {
            font-size: .80em;
        }

		</style>
	</head>
	<body>
        @yield('content')
	</body>
</html>
