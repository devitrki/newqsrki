@extends('reports.templates.pdf.template1')
@section('content')
    <div class="report">
        <div class="header">
            <table>
                <tr>
                    <td class="logo"><img src="{{ public_path( 'images/logo/rki.png' ) }}" alt="Richeese Kuliner Indonesia"></td>
                    <td class="title"><h1>{{ $title }}</h1></td>
                </tr>
            </table>
        </div>
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Plant :</strong> {{ $data['header']['plant_code'] . ' - ' . $data['header']['plant_name'] }}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>GR Number</th>
                <th>PO Number</th>
                <th>Ref Number</th>
                <th>Posting Date</th>
                <th>Vendor ID</th>
                <th>Vendor Name</th>
                <th>Material Code</th>
                <th>Material Description</th>
                <th>QTY PO</th>
                <th>QTY Remaining PO</th>
                <th>QTY GR</th>
                <th>QTY Remaining</th>
                <th>Batch</th>
                <th>UOM</th>
                <th>Recepient</th>
                <th>Timestamp</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->gr_number }}</td>
                <td>{{ $item->po_number }}</td>
                <td>{{ $item->ref_number }}</td>
                <td>{{ App\Library\Helper::DateConvertFormat($item->posting_date, 'Y-m-d', 'd/m/Y') }}</td>
                <td>{{ $item->vendor_id }}</td>
                <td>{{ $item->vendor_name }}</td>
                <td>{{ $item->material_code }}</td>
                <td>{{ $item->material_desc }}</td>
                <td align="right">{{ round($item->qty_po, 3) }}</td>
                <td align="right">{{ round($item->qty_remaining_po, 3) }}</td>
                <td align="right">{{ round($item->qty_gr, 3) }}</td>
                <td align="right">{{ round($item->qty_remaining, 3) }}</td>
                <td align="right">{{ $item->batch }}</td>
                <td>{{ $item->uom }}</td>
                <td>{{ $item->recepient }}</td>
                <td>{{ App\Library\Helper::DateConvertFormat($item->created_at, 'Y-m-d H:i:s', 'd/m/Y H:i:s') }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endsection
