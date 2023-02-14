<!DOCTYPE html>
<html>
    <head>
        <title>Preview Transaction Aloha : {{ $plant_name }} : {{ $customer_code }} : {{ $date }} </title>
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
            <h4>Preview Transaction Aloha : {{ $plant_name }} : {{ $customer_code }} : {{ $date }} </h4>
            @empty($pos)
            {{ __("No Data Transactions") }}
            @else
            <br><br>
            <h4>Payment</h4>
            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="6%">COL01</td>
                    <td width="6%">COL02</td>
                    <td width="6%">COL03</td>
                    <td width="6%">COL04</td>
                    <td width="6%">COL05</td>
                    <td width="6%">COL06</td>
                    <td width="6%">COL07</td>
                    <td width="6%">COL08</td>
                    <td width="6%">COL09</td>
                    <td width="6%">COL10</td>
                    <td width="6%">COL11</td>
                    <td width="6%">COL12</td>
                    <td width="6%">COL13</td>
                    <td width="6%">COL14</td>
                    <td width="6%">COL15</td>
                    <td width="6%">COL16</td>
                </tr>
                @foreach($pos['payment'] as $k => $v)
                <tr class="item">
                    <td align="left">{{$v['COL01']}}</td>
                    <td align="left">{{$v['COL02']}}</td>
                    <td align="left">{{$v['COL03']}}</td>
                    <td align="left">{{$v['COL04']}}</td>
                    <td align="left">{{$v['COL05']}}</td>
                    <td align="left">{{$v['COL06']}}</td>
                    <td align="left">{{$v['COL07']}}</td>
                    <td align="left">{{$v['COL08']}}</td>
                    <td align="left">{{$v['COL09']}}</td>
                    <td align="left">{{$v['COL10']}}</td>
                    <td align="left">{{$v['COL11']}}</td>
                    <td align="left">{{$v['COL12']}}</td>
                    <td align="left">{{$v['COL13']}}</td>
                    <td align="left">{{$v['COL14']}}</td>
                    <td align="left">{{$v['COL15']}}</td>
                    <td align="left">{{$v['COL16']}}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td colspan="16"></td>
                </tr>
            </table>

            <!-- sales -->
            <br><br>
            <h4>Sales</h4>
            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="6%">COL01</td>
                    <td width="6%">COL02</td>
                    <td width="6%">COL03</td>
                    <td width="6%">COL04</td>
                    <td width="6%">COL05</td>
                    <td width="6%">COL06</td>
                    <td width="6%">COL07</td>
                    <td width="6%">COL08</td>
                    <td width="6%">COL09</td>
                    <td width="6%">COL10</td>
                    <td width="6%">COL11</td>
                    <td width="6%">COL12</td>
                    <td width="6%">COL13</td>
                    <td width="6%">COL14</td>
                    <td width="6%">COL15</td>
                    <td width="6%">COL16</td>
                </tr>
                @foreach($pos['sales'] as $k => $v)
                <tr class="item">
                    <td align="left">{{$v['COL01']}}</td>
                    <td align="left">{{$v['COL02']}}</td>
                    <td align="left">{{$v['COL03']}}</td>
                    <td align="left">{{$v['COL04']}}</td>
                    <td align="left">{{$v['COL05']}}</td>
                    <td align="left">{{$v['COL06']}}</td>
                    <td align="left">{{$v['COL07']}}</td>
                    <td align="left">{{$v['COL08']}}</td>
                    <td align="left">{{$v['COL09']}}</td>
                    <td align="left">{{$v['COL10']}}</td>
                    <td align="left">{{$v['COL11']}}</td>
                    <td align="left">{{$v['COL12']}}</td>
                    <td align="left">{{$v['COL13']}}</td>
                    <td align="left">{{$v['COL14']}}</td>
                    <td align="left">{{$v['COL15']}}</td>
                    <td align="left">{{$v['COL16']}}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td colspan="16"></td>
                </tr>
            </table>

            <!-- Inventory -->
            <br><br>
            <h4>Inventory</h4>
            <table class="sp-item" style="width: 100%; margin: 0 auto; margin-top: 20px;" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; background-color: #ececec" class="item center">
                    <td width="6%">COL01</td>
                    <td width="6%">COL02</td>
                    <td width="6%">COL03</td>
                    <td width="6%">COL04</td>
                    <td width="6%">COL05</td>
                    <td width="6%">COL06</td>
                    <td width="6%">COL07</td>
                    <td width="6%">COL08</td>
                    <td width="6%">COL09</td>
                    <td width="6%">COL10</td>
                    <td width="6%">COL11</td>
                    <td width="6%">COL12</td>
                    <td width="6%">COL13</td>
                    <td width="6%">COL14</td>
                    <td width="6%">COL15</td>
                    <td width="6%">COL16</td>
                </tr>
                @foreach($pos['inventory'] as $k => $v)
                <tr class="item">
                    <td align="left">{{$v['COL01']}}</td>
                    <td align="left">{{$v['COL02']}}</td>
                    <td align="left">{{$v['COL03']}}</td>
                    <td align="left">{{$v['COL04']}}</td>
                    <td align="left">{{$v['COL05']}}</td>
                    <td align="left">{{$v['COL06']}}</td>
                    <td align="left">{{$v['COL07']}}</td>
                    <td align="left">{{$v['COL08']}}</td>
                    <td align="left">{{$v['COL09']}}</td>
                    <td align="left">{{$v['COL10']}}</td>
                    <td align="left">{{$v['COL11']}}</td>
                    <td align="left">{{$v['COL12']}}</td>
                    <td align="left">{{$v['COL13']}}</td>
                    <td align="left">{{$v['COL14']}}</td>
                    <td align="left">{{$v['COL15']}}</td>
                    <td align="left">{{$v['COL16']}}</td>
                </tr>
                @endforeach
                <tr class="item2">
                    <td colspan="16"></td>
                </tr>
            </table>
            @endempty

        </div>
    </body>
</html>
