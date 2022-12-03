<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Customer</th>
            <th>Posting Date</th>
            <th>Period</th>
            <th>Company Code</th>
            <th>Currency</th>
            <th>Special G/L</th>
            <th>Doc Number / Assiment</th>
            <th>AR Value</th>
            <th>Reference</th>
            <th>Header Text</th>
            <th>Posting Key</th>
            <th>Special G/L Item</th>
            <th>Account</th>
            <th>Value</th>
            <th>Payment Date Bank</th>
            <th>Tax Code</th>
            <th>Include Tax</th>
            <th>Payment Term Customer</th>
            <th>Payment Date Customer</th>
            <th>Assigment</th>
            <th>Text</th>
            <th>Cost Center</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr>
            <td>{{ $item->no }}</td>
            <td>{{ $item->item }}</td>
            <td>{{ $item->customer_code }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($item->bank_in_date, 'Y-m-d', 'Ymd') }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($item->bank_in_date, 'Y-m-d', 'n') }}</td>
            <td>RKI</td>
            <td>IDR</td>
            <td>{{ $item->special_gl }}</td>
            <td>{{ $item->document_number }}</td>
            <td>{{ $item->ar_value }}</td>
            <td>{{ $item->reference }}</td>
            <td></td>
            <td>40</td>
            <td></td>
            <td>{{ $item->gl_account }}</td>
            <td>{{ $item->value }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($item->bank_in_date, 'Y-m-d', 'Ymd') }}</td>
            <td>{{ $item->tax_code }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $item->assigment }}</td>
            <td>{{ $item->text }}</td>
            <td>{{ $item->cost_center }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
