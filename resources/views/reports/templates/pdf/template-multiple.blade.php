<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
		<title>{{ config('app.name') }}</title>
		<style>
        @page {
            margin: 105px 25px 25px;
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

        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            background-color: #ffffff;
            height: 80px;
        }

        footer {
            position: fixed;
            bottom: -15px;
            left: 0px;
            right: 0px;
            background-color: #ffffff;
            height: 10px;
        }

        .report {
            page-break-after: always;
        }

        .report:last-child {
            page-break-after: never;
        }

        /* header */
        header table {
            width: 100%;
            border: 1px solid #ddd;
        }

        header table td.logo {
            width: 30%;
        }

        header table td.title {
            width: 70%;
            text-align: center;
        }

        header img{
            width:250px;
            height: 60px;
            background-position: center;
        }

        header h1 {
            font-size: 14px;
        }

        /* description content */
        main .desc {
            margin: 0;
            margin-bottom: 10px;
        }

        main .desc table {
            width: 100%;
        }

        main .desc table td {
            font-size: .80em;
        }

        /* table body content */
        main .body table {
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        main .body table thead tr {
            background-color: #ececec;
        }

        main .body table tr {
            padding: .35em;
        }

        main .body table th,
        main .body table td {
            padding: .125em .425em;
            border: 1px solid #666666;
        }

        main .body table th {
            font-size: .70em;
            letter-spacing: .1em;
            text-transform: uppercase;
            text-align: center;
        }

        main .body table td {
            font-size: .80em;
        }

        /* footer */
        footer .pagenum-container {
            text-align: center;
            font-size: .80em;
        }
        footer .pagenum:before {
            content: counter(page);
        }
        </style>
        @yield('style')
	</head>
	<body>
        <header>
            <table>
                <tr>
                    <td class="logo"><img src="{{ asset( 'images/logo/rki.png' ) }}" alt="Richeese Kuliner Indonesia"></td>
                    <td class="title"><h1>@yield('title')</h1></td>
                </tr>
            </table>
        </header>
        <main>
            @yield('content')
        </main>

	</body>
</html>
