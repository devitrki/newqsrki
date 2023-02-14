<!DOCTYPE html>
<html>
    <head>
        <title>Preview Transaction Vtec : {{ $plant_name }} : {{ $customer_code }} : {{ $date }} </title>
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
                padding: 3px 8px;
            }
            table .item2 td{
                border: 1px solid #666666;
                font-weight: 700;
                padding: 3px 8px;
                border-right: 0px;
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

            table .item2 td:last-child{
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
            <h4>Preview Transaction Vtec : {{ $plant_name }} : {{ $customer_code }} : {{ $date }} </h4>
            @empty($pos)
            {{ __("No Data Transactions") }}
            @else
            <br><br>
            <h4>PAYMENT</h4>

            <table class="sp-item" style="width: 500px; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="45%">PAYMENT</td>
                    <td width="15%">QTY</td>
                    <td width="35%">AMOUNT</td>
                </tr>
                @foreach($pos['payments'] as $k => $v)
                <tr class="item">
                    <td align="left">{{ $v['payment'] }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($v['count'], '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($v['value'], '', 0) }}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td align="right">{{ $pos['total_payments']['payment'] }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($pos['total_payments']['count'], '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($pos['total_payments']['value'], '', 0) }}</td>
                </tr>
            </table>

            {{-- promotion --}}
            <br><br>
            <h4>PROMOTION</h4>
            <table class="sp-item" style="width: 500px; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="45%">PROMOTION</td>
                    <td width="15%">QTY</td>
                    <td width="35%">AMOUNT</td>
                </tr>
                @foreach($pos['promotions'] as $k => $v)
                <tr class="item">
                    <td align="left">{{ $v['promotion'] }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($v['count'], '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($v['value'] * -1, '', 0) }}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td align="right">{{ $pos['total_promotions']['promotion'] }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($pos['total_promotions']['count'], '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($pos['total_promotions']['value'] * -1, '', 0) }}</td>
                </tr>
            </table>

            <!-- sales -->
            <br><br>
            <h4>SALES</h4>
            <table class="sp-item" style="width: 650px; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="18%">MENU CODE</td>
                    <td width="45%">MENU NAME</td>
                    <td width="13%">QTY</td>
                    <td width="24%">NET SALES</td>
                </tr>
                @foreach($pos['sales'] as $k => $v)
                <tr class="item">
                    <td align="left">{{ $v['menu_code'] }}</td>
                    <td align="left">{{ $v['menu_name'] }}</td>
                    <td align="right">{{ $v['qty'] }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($v['net_sales'], '', 0) }}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td align="right" colspan="2">{{ $pos['total_sales']['label'] }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($pos['total_sales']['qty'], '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($pos['total_sales']['net_sales'], '', 0) }}</td>
                </tr>
            </table>

            <!-- Inventory -->
            <br><br>
            <h4>INVENTORY</h4>
            <table class="sp-item" style="width: 500px; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="20">MENU CODE</td>
                    <td width="60">MENU NAME</td>
                    <td width="20">QTY</td>
                </tr>
                @foreach($pos['inventories'] as $k => $v)
                <tr class="item">
                    <td align="left">{{ $v['menu_code'] }}</td>
                    <td align="left">{{ $v['menu_name'] }}</td>
                    <td align="right">{{ $v['qty'] }}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td align="right" colspan="2">{{ $pos['total_inventories']['label'] }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($pos['total_inventories']['qty'], '', 0) }}</td>
                </tr>
            </table>

            @endempty

        </div>
    </body>
</html>
