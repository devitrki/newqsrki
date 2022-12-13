<table>
    <thead>
        <tr>
            <th>Receipt Number</th>
            <th>Transaction Time</th>
            <th>Transaction Value (Not subject to VAT)</th>
            <th>Discount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $trans)
        <tr>
            <td>{{ $trans->CheckNumber }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($trans->SystemDate, 'Y-m-d H:i:s.v', 'd/m/Y H:i:s') }}</td>
            <td>{{ (int)$trans->Total }}</td>
            <td>{{ (int)$trans->TotalDiscount }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
