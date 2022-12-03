<!DOCTYPE html>
<html>
    <head>
        <title>{{ __('Delivery Asset Preview') }}</title>
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
                <h1>{{ __('Delivery Asset') }}</h1>
            </div>
            <table class="sp-header"  style="width: 100%; margin: 0 auto;">
                <tr>
                    <td class="highlight" width="33.3%">{{ __('Sender') }} :</td>
                    <td class="highlight" width="33.3%">{{ __('Transfer Date') }} : </td>
                    <td class="highlight" width="33.3%">{{ __('Receiver') }} :</td>
                </tr>
                <tr>
                    <td rowspan="4" valign="top" width="30%">
                        {{ $assetMutation->plant_sender_code . ' - ' . $assetMutation->plant_sender }}
                        <br/>
                        {{ $assetMutation->plant_sender_address }}
                    </td>
                    <td>{{ App\Library\Helper::DateConvertFormat($assetMutation->date_confirmation_sender, 'Y-m-d H:i:s', 'd/m/Y') }}</td>
                    <td rowspan="4" valign="top">
                        {{ $assetMutation->plant_receiver_code . ' - ' . $assetMutation->plant_receiver }}
                        <br/>
                        {{ $assetMutation->plant_receiver_address }}
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
                    <td width="15%">Asset Number</td>
                    <td width="11%">Sub Number</td>
                    <td width="22%">Description</td>
                    <td width="15%">Spec / user</td>
                    <td width="5%">Qty</td>
                    <td width="5%">Uom</td>
                    <td width="22%">Remark</td>
                </tr>
                <tr class="item">
                    <td align="center">{{ $assetMutation->number }}</td>
                    <td align="center">{{ $assetMutation->number_sub }}</td>
                    <td align="center">{{ $assetMutation->description }}</td>
                    <td align="center">{{ $assetMutation->spec_user }}</td>
                    <td align="center">{{ $assetMutation->qty_mutation }}</td>
                    <td align="center">{{ $assetMutation->uom }}</td>
                    <td align="center">{{ $assetMutation->remark }}</td>
                </tr>
                <tr class="keterangan">
                    <td colspan="7" style="padding-top: 5px;">{{ __('Note: 1 print out for sender, 1 print out for driver / receiver') }}</td>
                </tr>
            </table>
            <table style="width: 60%; margin-top: 20px; margin-left: 20%;">
                <tr style="text-align: center;" class="trbold">
                    <td width="30%">{{ __('Sender') }}</td>
                    <td width="30%">{{ __('Driver') }}</td>
                    <td width="30%">{{ __('Receiver') }}</td>
                </tr>
                <tr style="height: 120px; text-align: center;">
                    <td>(............................)</td>
                    <td>(............................)</td>
                    <td>(............................)</td>
                </tr>
            </table>
            <div class="footer">
                {{ date('d/m/Y H:i:m') }}  Hal : 1 / 1
            </div>
        </div>
    </body>
</html>
