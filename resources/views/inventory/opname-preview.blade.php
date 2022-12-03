<!DOCTYPE html>
<html>
    <head>
        <title>Preview Stock Opname Data</title>
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
                margin-bottom: 10px;
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
                margin-top: -14px;
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
            table .item2 td{
                border-top: 2px solid #666666;
            }
            .grand{
                border-bottom: 1px solid #666666;
                border-top: 1px solid #666666;
                border-left: 1px solid #666666;
                border-right: 1px solid #666666;
                padding-top: 5px;
                padding-bottom: 5px;
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
                <h1>Preview Stock Opname Data</h1>
                <h4>{{ __('Document Number') }}: {{ ($opname->document_number != '') ? $opname->document_number : '-' }}</h4>
            </div>
            <table class="sp-header" style="width: 100%; margin: 0 auto;">
                <tr>
                    <td width="33.3%">Outlet : {{ $plant }}</td>
                    <td width="33.3%">PIC : {{ $opname->pic }}</td>
                    <td width="33.3%">PIC Update : {{ ($opname->update != '0') ? $opname->pic_update : '-' }}</td>
                </tr>
                <tr>
                    <td>{{ __('Create Date') }}: {{ App\Library\Helper::DateConvertFormat($opname->date, 'Y-m-d', 'd-m-Y') }}</td>
                    <td>{{ __('Posting Date') }} : {{ ( $opname->submit != '0' ) ? App\Library\Helper::DateConvertFormat($opname->posting_date, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-'}}</td>
                    <td>{{ __('Update Date') }} : {{ ( $opname->update != '0' ) ? App\Library\Helper::DateConvertFormat($opname->update_date, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                </tr>
            </table>
            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="*">Notes</td>
                </tr>
                <tr class="item">
                    <td>{{ ( $opname->note != '' ) ? $opname->note : '- ' . __('No Additional Material') . ' -' }}</td>
                </tr>
                <tr class="item2">
                    <td colspan="0"></td>
                </tr>
            </table>

            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="3%">No.</td>
                    <td width="5%">Material Code</td>
                    <td width="20%">Material Desc</td>
                    <td width="7%">Qty 1st Input</td>
                    <td width="3%">Uom</td>
                    <td width="7%">Qty 2nd Input</td>
                    <td width="3%">Uom</td>
                    <td width="7%">Qty Final Input</td>
                    <td width="3%">Uom</td>
                    <td width="7%">Qty SAP</td>
                    <td width="3%">Uom</td>
                    <td width="3%">Selisih</td>
                </tr>
                @foreach($opname_items as $i => $item)
                <tr class="item">
                    <td align="left">{{ $i + 1 }}</td>
                    <td align="left">{{ $item->material_code }}</td>
                    <td align="left">{{ $item->material_name }}</td>
                    <td align="right">{{ $item->qty_first + 0 }}</td>
                    <td align="left">{{ $item->uom_first }}</td>
                    <td align="right">{{ ( $item->qty_update <> '0' ) ? App\Library\Helper::convertNumberToInd($item->qty_update, '', 3) : '-' }}</td>
                    <td align="left">{{ ( $item->qty_update <> '0' ) ? $item->uom_update : '-'}}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($item->qty_final, '', 3) }}</td>
                    <td align="left">{{ $item->uom_final }}</td>
                    <td align="right">{{ $item->qty_sap }}</td>
                    <td align="left">{{ $item->uom_sap }}</td>
                    <td align="right">{{ $item->selisih }}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td colspan="12"></td>
                </tr>
            </table>
            <div class="footer">
            </div>
        </div>
    </body>
</html>
