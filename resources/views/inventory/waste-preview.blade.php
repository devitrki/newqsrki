<!DOCTYPE html>
<html>
    <head>
        <title>Preview Waste / Scrap Data</title>
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
                <h1>Preview Scrap / Waste Data</h1>
                <h4>{{ __('Document Number') }}: {{ ($waste->document_number != '') ? $waste->document_number : '-' }}</h4>
            </div>
            <table class="sp-header" style="width: 100%; margin: 0 auto;">
                <tr>
                    <td width="25%">Outlet : {{ $plant }}</td>
                    <td width="25%">PIC : {{ $waste->pic }}</td>
                    <td width="25%">{{ __('Create Date') }}: {{ App\Library\Helper::DateConvertFormat($waste->date, 'Y-m-d', 'd-m-Y') }}</td>
                    <td width="25%">{{ __('Posting Date') }} : {{ ( $waste->submit != '0' ) ? App\Library\Helper::DateConvertFormat($waste->posting_date, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-'}}</td>
                </tr>
            </table>
            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="5%">No.</td>
                    <td width="10%">Material Code</td>
                    <td width="20%">Material Name</td>
                    <td width="10%">Qty</td>
                    <td width="10%">Uom</td>
                    <td width="30%">Note</td>
                </tr>
                @foreach($waste_items as $i => $item)
                <tr class="item">
                    <td align="left">{{ $i + 1 }}</td>
                    <td align="left">{{ $item->material_code }}</td>
                    <td align="left">{{ $item->material_name }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($item->qty, '', 3) }}</td>
                    <td align="left">{{ $item->uom }}</td>
                    <td align="left">{{ $item->note }}</td>
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
