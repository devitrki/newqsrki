<table>
    <thead>
        <tr>
            <th>Bank In GL</th>
            <th>Bank In Date</th>
            <th>Bank In Description</th>
            <th>Sales Date</th>
            <th>Sales Month</th>
            <th>Sales Year</th>
            <th>Special GL</th>
            <th>Outlet Code</th>
            <th>Total Nominal Bank</th>
            <th>Bank Charge / Commision</th>
            <th>Total Nominal Sales</th>
            <th>Selisih</th>
            <th>Selisih (%)</th>
            <th>Status Generate</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr>
            <td>{{ $item->bank_in_bank_gl }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($item->bank_in_date, 'Y-m-d', 'd-m-Y') }}</td>
            <td>{{ $item->bank_in_description }}</td>
            <td>{{ $item->sales_date }}</td>
            <td>{{ $item->sales_month }}</td>
            <td>{{ $item->sales_year }}</td>
            <td>{{ $item->special_gl }}</td>
            <td>{{ App\Models\Plant::getCustomerCodeById($item->plant_id) }}</td>
            <td>{{ $item->bank_in_nominal }}</td>
            <td>{{ $item->bank_in_charge }}</td>
            <td>{{ $item->nominal_sales }}</td>
            <td>{{ $item->selisih }}</td>
            <td>{{ $item->selisih_percent }}</td>
            <td>
            @if($item->status_generate == 0)
            -
            @elseif($item->status_generate == 1)
            yes
            @else
            no
            @endif
            </td>
            <td>{{ $item->description }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
