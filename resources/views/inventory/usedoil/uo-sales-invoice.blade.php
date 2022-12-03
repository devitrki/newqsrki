<!DOCTYPE html>
<html>
    <head>
        <title>{{ __('Original Invoice') }}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body{
                font-family: Arial, Tahoma;
                font-size: 12px;
            }

            @page
            {
                size: landscape;
            }

            @media print{
                @page {
                    size: landscape
                }
            }

            #wrapper{
                margin: 0 auto;
                width: 80%;
                padding: 10px;
                border: 1px solid #999999;
                min-height: 300px;
                position: relative;
            }

            .header-title{
                position: relative;
                text-align: center;
                font-weight: bold;
                height: 70px;
            }

            .header-title span{
                font-weight: bold;
                font-size: 22px;
            }

            .header-data table:first-child{
                width: 100%;
                margin-top: 20px;
            }

            .header-data table td{
                padding: 2px;
            }

            .header-data .address{
                line-height: 18px;
            }

            .detail{
                width: 100%;
                margin-top: 20px;
            }

            .detail td{
                padding: 2px;
            }

            .detail tr:first-child td{
                border-top: 2px solid #333333 ;
                border-bottom: 1px solid #333333;
                padding: 3px 2px 3px 2px;
                font-weight: bold;
            }

            .center{
                text-align: center !important;
            }

            .right{
                text-align: right !important;
            }

            .total, .sub-total, .tax{
                font-weight: bold;
            }

            .sub-total td{
                border-top: 1px solid #333333;
            }

            .total td{
                border-bottom: 1px solid #333333;
            }

            .signature{
                width: 100%;
                margin-top: 20px;
                font-weight: bold;
            }

            .footer-page{
                width: 30%;
                font-size: smaller;
            }

            .watermark {
                position: absolute;
                opacity: 0.25;
                font-size: 4em;
                width: 600px;
                text-align: center;
                z-index: 1000;
                color: red;
                top: 40%;
                left: 25%;
                border: 4px solid red;
                -webkit-transform: rotate(-25deg);
                -moz-transform: rotate(-25deg);
                -o-transform: rotate(-25deg);
                -ms-transform: rotate(-25deg);
                transform: rotate(-25deg);
            }

            .watermark-consume {
                width: 70%;
                height: 25%;
                opacity: .45;
                overflow: auto;
                margin: 23% auto;
                position: absolute;
                top: 0; left: 0; bottom: 0; right: 0;
                font-size: 3em;
                text-align: center;
                z-index: 1000;
                color: red;
                border: 4px solid red;
            }
        </style>
        <style type="text/css" media="print">
            #wrapper{
                margin: 0 auto;
                width: 100%;
                padding: 0px;
                border: none;
                min-height: 300px;
                position: relative;
            }

            .watermark{
                left: 10%;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <div class="watermark-consume">
                BUKAN UNTUK KONSUMSI MANUSIA<br>
                ORIGINAL INVOICE
            </div>
            <div class="header-title">
                <span>{{ __('INVOICE') }}</span><br/>
                <div style="position: absolute; right: 5px; top: 0px; font-size: 14px;">
                    {{ __('Document Number') }}<br/>
                    {{ $movement->document_number }}
                </div>
            </div>
            <div class="header-data">
                <table>
                    <tr>
                        <td width="35%"><b>{{ __('SENDER') }} :</b></td>
                        <td width="15%"><b>{{ __('DATE') }}</b></td>
                        <td width="25%">: {{ App\Library\Helper::DateConvertFormat($movement->date, 'Y-m-d', 'd/m/Y') }}</td>
                        <td width="25%"><b>{{ __('RECEIVER') }} : </b></td>
                    </tr>
                    <tr style="vertical-align: top">
                        <td rowspan="2" class="address">
                            {{ $movement->plant_sender_code . ' - ' . $movement->plant_sender }}
                            <br/>
                            {{ $movement->plant_sender_address }}
                        </td>
                        <td></td>
                        <td></td>
                        <td rowspan="3" class="address">
                            {{ $movement->vendor_name }}
                            <br/>
                            {{ $movement->vendor_address }}
                        </td>
                    </tr>
                    <tr style="vertical-align: top">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <table class="detail" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="5%" class="center">No.</td>
                        <td width="15%">ITEM CODE</td>
                        <td width="*">ITEM DESCRIPTION</td>
                        <td width="10%" class="right">QUANTITY</td>
                        <td width="15%" class="right">PRICE</td>
                        <td width="15%" class="right">TOTAL</td>
                    </tr>
                    @foreach($movementItems as $i => $item)
                        <tr class="item">
                            <td align="center">{{ $i + 1 }}</td>
                            <td>{{ $item->material_code }}</td>
                            <td>{{ $item->material_name }}</td>
                            <td align="right">{{ \App\Library\Helper::convertNumberToInd(abs($item->qty), '', 2) . ' ' . $item->material_uom }}</td>
                            <td class="right">{{ \App\Library\Helper::convertNumberToInd($item->price, '', 2) }}</td>
                            <td class="right">{{ \App\Library\Helper::convertNumberToInd(round($item->price *  abs($item->qty), 0), '', 2) }}</td>
                        </tr>
                    @endforeach
                    @for($i = sizeof($movementItems) +1; $i < 10; $i++)
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    @endfor
                    <tr class="total sub-total">
                        <td colspan="3"></td>
                        <td colspan="2">TOTAL</td>
                        <td class="right">{{ \App\Library\Helper::convertNumberToInd($movement->subtotal, '', 2) }}</td>
                    </tr>
                </table>
                <table class="signature">
                    <tr class="center">
                        <td width="20%">Sender</td>
                        <td width="20%">Driver</td>
                        <td width="20%">Recipient</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="height: 40px;"></td>
                    </tr>
                    <tr class="center">
                        <td>____________________</td>
                        <td>____________________</td>
                        <td>____________________</td>
                    </tr>
                    <tr class="center" style="font-size: smaller; vertical-align: bottom;">
                        <td style="height: 20px;">{{ date('d/m/Y H:i:m') }}</td>
                        <td colspan="3" style="color: red;">{{ __('The invoice is considered paid when the payment has been made') }}</td>
                        <td> Hal : 1 / 1</td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
