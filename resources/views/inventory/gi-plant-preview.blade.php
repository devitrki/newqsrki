<!DOCTYPE html>
<html>
    <head>
        <title>{{ __('Delivery Orders Preview') }}</title>
        <base href="{#BASE_URL}/" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style type="text/css">
            body{
                font-family: Arial;
                font-size: 14px;
            }

            .wrapper{
                width: 96%;
                margin: 0 auto;
                position: relative;
            }

            .header h1, h4{
                text-align: center;
                margin: 0px;
            }

            .header{
                position: relative;
                margin-bottom: 40px;
            }

            .doc-number{
                position: absolute;
                top: 0px;
                right: 0px;
                text-align: right;
                width: 200px;
                font-size: 16px;
            }

            .footer{
                text-align: right !important;
                margin-top: 5px;
            }

            .item td{
                padding: 3px;
            }

            .sp-header td{
                text-align: left;
            }

            .center{
                text-align: center !important;
            }

            .right{
                text-align: right !important;
            }

            table .item td{
                border: 1px solid #666666;
                border-top: 0px;
                border-bottom: 0px;
                border-right: 0px;
                padding: 3px;
            }

            .sp-item tr:first-child td{
                border-top: 1px solid #666;
                border-bottom: 1px solid #666;
            }

            .keterangan td{
                border-top: 1px solid #666;
            }

            table .item td:last-child{
                border-right: 1px solid #666666;
                border-right: 1px solid #666666;
            }

            .error{
                margin: 15% auto;
                width: 500px;
                height: 90px;
                padding: 20px;
                background-color: #f6f6f6;
                border: 1px solid #8c909e;
                text-align: justify;
                font-size: 16px;
                position: relative;
                padding-left: 150px;
            }

            .error img{
                position: absolute;
                top: 0px;
                left: 10px;
            }

            .highlight {
                text-transform: uppercase;
            }

            #watermark {
                color: #d0d0d0;
                font-size: 100pt;
                position: absolute;
                width: 200px;
                height: 200px;
                margin: 0;
                z-index: -1;
                left:22%;
                top:40%;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="header">
                <h1>{{ __('Delivery Orders') }}</h1>
                <h4>351-TF to stck in trans</h4>
                <div class="doc-number">
                    {{ __('Document Number') }}<br/>
                    {{ $header->document_number }}
                </div>
            </div>
            <table class="sp-header"  style="width: 100%; margin: 0 auto;">
                <tr>
                    <td class="highlight">{{ __('Issuing') }} :</td>
                    <td class="highlight" width="12%">{{ __('Document Date') }}</td>
                    <td width="1%" class="center">:</td>
                    <td width="15%">{{ App\Library\Helper::DateConvertFormat($header->date, 'Y-m-d', 'd/m/Y') }}</td>
                    <td class="highlight" width="30%">{{ __('Receiving') }} :</td>
                </tr>
                <tr>
                    <td rowspan="4" valign="top" width="30%">
                        {{ $header->issuing_plant_code . ' ' . $header->issuing_plant_desc }}
                        <br/>
                        {{ $header->issuing_plant_address }}
                    </td>
                    <td>{{ __('Reservation Number') }}</td>
                    <td class="center">:</td>
                    <td></td>
                    <td rowspan="4" valign="top">
                        {{ $header->receiving_plant_code . ' ' . $header->receiving_plant_desc }}
                        <br/>
                        {{ $header->receiving_plant_address }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="center"></td>
                    <td></td>
                </tr>
            </table>
            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="5%">No.</td>
                    <td width="15%">{{ __('Item Code') }}</td>
                    <td width="50%">{{ __('Item Description') }} ({{ __('Name') }}, Spec) </td>
                    <td width="10%">{{ __('Quantity') }}</td>
                    <td width="20%">{{ __('Description') }}</td>
                </tr>
                @foreach($items as $index => $item)
                <tr class="item">
                    <td align="center">{{ $index + 1 }}</td>
                    <td align="center">{{ $item->material_code }}</td>
                    <td>{{ $item->material_desc }}</td>
                    <td  align="right">{{ App\Library\Helper::convertNumberToInd($item->qty, '', 3) }} {{ $item->uom }}</td>
                    <td>{{ $item->note }}</td>
                </tr>
                @endforeach
                @for($i = 0; $i < (7 - sizeof($items)); $i++)
                <tr class="item">
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
                <tr class="keterangan">
                    <td colspan="3" style="padding-top: 5px;">{{ __('Note') }} : WEB TRANSFER</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <table style="width: 60%; margin-top: 20px;">
                <tr style="text-align: center;">
                    <td width="30%">{{ __('Issuing') }}</td>
                    <td width="30%">Driver</td>
                    <td width="30%">{{ __('Receiving') }}</td>
                </tr>
                <tr style="height: 120px; text-align: center;">
                    <td>({{ $header->issuer }})</td>
                    <td>(............................)</td>
                    <td>({{ $header->requester }})</td>
                </tr>
            </table>
            <div class="footer">
                {{ date('d/m/Y H:i:m') }} / {{ $header->document_number }} Hal : 1 / 1
            </div>
        </div>
    </body>
</html>
