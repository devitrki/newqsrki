<!DOCTYPE html>
<html>
    <head>
        <title>{{ __('Acceptance of Order Goods Preview') }}</title>
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
                <h1>{{ __('ACCEPTANCE OF ORDER (GOODS)') }}</h1>
                <h4>{{ $header->receiving_plant_code . ' ' . $header->receiving_plant_desc }}</h4>
                <div class="doc-number">
                    {{ __('Document Number') }}<br/>
                    {{ $header->document_number }}
                </div>
            </div>
            <table class="sp-header"  style="width: 100%; margin: 0 auto;">
                <tr>
                    <td width="12%">{{ __('Document Date') }}</td>
                    <td width="1%" class="center">:</td>
                    <td width="15%">{{ App\Library\Helper::DateConvertFormat($header->date, 'Y-m-d', 'd/m/Y') }}</td>
                    <td width="30%">{{ __('Issuing Plant') }} : {{ $header->issuing_plant_desc }}</td>
                    <td width="30%">{{ __('Recepient') }} : {{ $header->recepient }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="center"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="5%">No.</td>
                    <td width="15%">{{ __('Item Code') }}</td>
                    <td width="50%">{{ __('Item Description') }} ({{ __('Name') }}, Spec) </td>
                    <td width="10%">{{ __('Qty PO') }}</td>
                    <td width="10%">{{ __('Qty Remaining PO') }}</td>
                    <td width="10%">{{ __('Qty GR') }}</td>
                    <td width="10%">{{ __('Qty Remaining') }}</td>
                    <td width="20%">{{ __('UOM') }}</td>
                </tr>
                @foreach($items as $index => $item)
                <tr class="item">
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $item->material_code }}</td>
                    <td>{{ $item->material_desc }}</td>
                    <td  align="right">{{ App\Library\Helper::convertNumberToInd($item->qty_po, '', 3) }}</td>
                    <td  align="right">{{ App\Library\Helper::convertNumberToInd($item->qty_b4_gr, '', 3) }}</td>
                    <td  align="right">{{ App\Library\Helper::convertNumberToInd($item->qty_gr, '', 3) }}</td>
                    <td  align="right">{{ App\Library\Helper::convertNumberToInd($item->qty_remaining, '', 3) }}</td>
                    <td>{{ $item->uom }}</td>
                </tr>
                @endforeach
                @for($i = 0; $i < (7 - sizeof($items)); $i++)
                <tr class="item">
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
                <tr class="keterangan">
                    <td colspan="4" style="padding-top: 10px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <div class="footer">
                {{ date('d/m/Y H:i:m') }} / {{ $header->document_number }} Page : 1 / 1
            </div>
        </div>
    </body>
</html>
