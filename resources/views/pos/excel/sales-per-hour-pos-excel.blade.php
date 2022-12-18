<table>
    <tr>
        <td colspan="27"><strong>Store :</strong> {{ $data['header']['store'] }}</td>
    </tr>
    <tr>
        <td colspan="27"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>{{ __('Hours') }}</th>
        <th>{{ __('Bill') }}</th>
        <th>{{ __('Customer') }}</th>
        <th>{{ __('Total Price') }}</th>
        <th>{{ __('Disc') }}</th>
        <th>{{ __('Sub Total') }}</th>
        <th>{{ __('SC') }}</th>
        <th>{{ __('Total Sale') }}</th>
        <th>{{ __('Tax') }}</th>
        <th>{{ __('Rounding') }}</th>
        <th>{{ __('Net Sales') }}</th>
        <th>{{ __('Total Payment') }}</th>
    </tr>
    </thead>
    <tbody>
        @for($time = 0; $time <= 23; $time++)
        <tr>
        <td>{{ ($time >= 23) ? $time . '-0'  : $time . '-' . ($time + 1) }}</td>
        @isset( $data['items']['h'.$time] )
        <td>{{ $data['items']['h'.$time]['bill'] }}</td>
        <td>{{ $data['items']['h'.$time]['bill'] }}
        <td>{{ round($data['items']['h'.$time]['totalPrice']) }}
        <td>{{ round($data['items']['h'.$time]['disc']) }}
        <td>{{ round($data['items']['h'.$time]['subTotal']) }}
        <td>0</td>
        <td>{{ round($data['items']['h'.$time]['subTotal']) }}
        <td>{{ round($data['items']['h'.$time]['tax']) }}
        <td>{{ round(abs($data['items']['h'.$time]['totalPayment'] - ($data['items']['h'.$time]['subTotal'] + $data['items']['h'.$time]['tax']))) }}
        <td>{{ round($data['items']['h'.$time]['netSales']) }}
        <td>{{ round($data['items']['h'.$time]['totalPayment']) }}
        @else
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        @endisset
        </tr>
        @endfor
    </tbody>
</table>
