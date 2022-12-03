<!DOCTYPE html>
<html>
    <head>
        <title>{{ __('RECEIPT OF GOODS') }}</title>
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
                <h1>{{ __('RECEIPT OF GOODS') }}</h1>
                <h4>{{ __('Document Number') }} {{ $grvendor->gr_number }}</h4>
                <h4>{{ __('Reference Number') }} {{ $grvendor->ref_number }}</h4>
                <div class="doc-number">
                    {{ __('PO Number') }}<br/>
                    {{ $grvendor->po_number }}
                </div>
            </div>
            <table class="sp-header" style="width: 100%; margin: 0 auto;">
                <tr>
                    <td>{{ __('Shipper') }} :</td>
                    <td width="12%">{{ __('Document Date') }}</td>
                    <td width="1%" class="center">:</td>
                    <td width="15%">{{ App\Library\Helper::DateConvertFormat($grvendor->posting_date, 'Y-m-d', 'd/m/Y') }}</td>
                    <td width="30%">{{ __('Receiver') }} :</td>
                </tr>
                <tr>
                    <td rowspan="4" valign="top">{{ $grvendor->vendor_name }}<br/></td>
                    <td></td>
                    <td class="center"></td>
                    <td></td>
                    <td rowspan="4" valign="top" width="30%"> {{ $plant }}<br/>{{ $plant_address }}</td>

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
                    <td width="10%">GR Qty</td>
                    <td width="10%">UOM</td>
                </tr>
                <tr class="item">
                    <td align="center">1</td>
                    <td align="center">{{ $grvendor->material_code }}</td>
                    <td>{{ $grvendor->material_desc }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($grvendor->qty_gr, '', 3) }}</td>
                    <td>{{ $grvendor->uom }}</td>
                </tr>
                <tr class="keterangan">
                    <td colspan="3" style="padding-top: 10px;">Note : -</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

            <table style="width: 60%; margin-top: 20px;">
                <tr style="text-align: center;">
                    <td width="30%">{{ __('Sender') }}</td>
                    <td width="30%">{{ __('Recepient') }}</td>
                </tr>
                <tr style="height: 120px; text-align: center;">
                    <td>(............................)</td>
                    <td>(............................)</td>
                </tr>
            </table>

            <div class="footer">
               {{ $grvendor->po_number }} Hal : 1 / 1
            </div>
        </div>
    </body>
    <script type="text/javascript">
        //window.print();
    </script>
</html>
