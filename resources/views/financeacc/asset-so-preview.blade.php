<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="Back Office & Outlet Richeese Factory">
        <meta name="keywords" content="back office, apps rf, apps.richeesefactory.com, richeese factory, apps, web apps, web apps richeese factory">
        <meta name="author" content="richeese factory">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>QSR RKI</title>
        <link rel="apple-touch-icon" href="{{ asset( 'images/ico/favicon.png' ) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset( 'images/ico/favicon.ico' ) }}">
        <link rel="stylesheet" type="text/css" href="{{ asset( 'css/bootstrap.css' ) }}">
        <style>
        body {
            font-family: "Rubik", sans-serif;
            line-height: 1.25;
            font-size: 14px;
            color: #000000;
            background-color: #ffffff;
            padding: 2rem;
        }

        .report .title p {
            font-size: 1.3rem;
            font-weight: bold;
        }

        .report .head-item-row {
            padding-top: 1.5rem;
            padding-bottom: 1rem;
        }

        .report .head-item {
            padding-bottom: 0.5rem;
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .report b,
        strong,
        th {
            font-weight: bold;
        }

        /* table data */
        .report table {
            border: 1px solid #ddd;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .report table caption {
            font-size: 1.5em;
            margin: .5em 0 .75em;
        }

        .report table thead tr {
            background-color: #ececec;
        }

        .report table tr {
            padding: .35em;
            border: 1px solid #ddd;
        }

        .report table th,
        .report table td {
            padding: .425em;
            border-right: 1px solid #ddd;
        }

        .report table th {
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
            text-align: center;
        }

        /* mobile */
        @media (max-width: 767.98px) {

            .report .title p {
                font-size: 1rem;
            }

            .report table {
                border: 0;
            }

            .report table caption {
                font-size: 1.3em;
            }

            .report table thead {
                border: none;
                clip: rect(0 0 0 0);
                height: 1px;
                margin: -1px;
                overflow: hidden;
                padding: 0;
                position: absolute;
                width: 1px;
            }

            .report table tr {
                background-color: #f8f8f8;
                border-bottom: 3px solid #ddd;
                display: block;
                margin-bottom: .625em;
            }

            .report table td {
                border-bottom: 1px solid #ddd;
                display: block;
                font-size: .8em;
                text-align: right;
            }

            .report table td::before {
                /*
                    * aria-label has no advantage, it won't be read inside a table
                    content: attr(aria-label);
                    */
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
            }

            .report table td:last-child {
                border-bottom: 0;
            }

            .report table th,
            .report table td {
                border-right: 0px solid #ddd;
            }
        }

        </style>
    </head>
    <body>
        <div class="row m-0 report">
            <div class="col-12 border py-1 title">
                <p class="text-center m-0">Preview Asset SO</p>
            </div>
            <div class="col-12">
                <div class="row head-item-row">
                    <div class="col-12 col-md-4 head-item">
                        <strong>{{ $plant['type'] }} :</strong> {{ $plant['code'] . '-' . $plant['name'] }}
                    </div>
                    <div class="col-12 col-md-4 head-item">
                        <strong>Cost Center  :</strong> {{ $assetSoPlant['head']->cost_center_code . '-' . $assetSoPlant['head']->cost_center }}
                    </div>
                    <div class="col-12 col-md-4 head-item">
                        <strong>Periode Asset SO :</strong> {{ $assetSoPlant['label'] . ' ' . $assetSoPlant['head']->year }}
                    </div>
                    <div class="col-12 mt-1 p-1 border">
                        <strong>Selisih Lebih :</strong> {{ $assetSoPlant['head']->note }}
                    </div>
                </div>
            </div>

            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Asset Number</th>
                            <th>Sub Number</th>
                            <th>Description</th>
                            <th>Spec / User</th>
                            <th>QTY SO</th>
                            <th>UOM</th>
                            <th>Remark</th>
                            <th>Remark SO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assetSoPlant['detail'] as $item)
                        <tr>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->number_sub }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->spec_user }}</td>
                            <td>{{ $item->qty_so }}</td>
                            <td>{{ $item->uom }}</td>
                            <td>{{ $item->remark }}</td>
                            <td>{{ $item->remark_so }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </body>
</html>
