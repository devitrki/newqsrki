@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('GR PO Vendor Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>Plant :</strong> {{ $header['plant_code'] . ' - ' . $header['plant_name'] }}
                </div>
                <div class="col-12 head-item">
                    <strong>{{ __('Date') }} :</strong> {{ $header['date_from'] }} - {{ $header['date_until'] }}
                </div>
            </div>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">GR Number</th>
                        <th scope="col">PO Number</th>
                        <th scope="col">Ref Number</th>
                        <th scope="col">Posting Date</th>
                        <th scope="col">Vendor ID</th>
                        <th scope="col">Vendor Name</th>
                        <th scope="col">Material Code</th>
                        <th scope="col">Material Description</th>
                        <th scope="col">QTY PO</th>
                        <th scope="col">QTY Remaining PO</th>
                        <th scope="col">QTY GR</th>
                        <th scope="col">QTY Remaining</th>
                        <th scope="col">Batch</th>
                        <th scope="col">UOM</th>
                        <th scope="col">Recepient</th>
                        <th scope="col">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="GR Number">{{ $item->gr_number }}</td>
                            <td data-label="PO Number">{{ $item->po_number }}</td>
                            <td data-label="Ref Number">{{ $item->ref_number }}</td>
                            <td data-label="Posting Date">{{ App\Library\Helper::DateConvertFormat($item->posting_date, 'Y-m-d', 'd/m/Y') }}</td>
                            <td data-label="Vendor ID">{{ $item->vendor_id }}</td>
                            <td data-label="Vendor Name">{{ $item->vendor_name }}</td>
                            <td data-label="Material Code">{{ $item->material_code ?? '-' }}</td>
                            <td data-label="Material Description">{{ $item->material_desc }}</td>
                            <td data-label="QTY PO" align="right">{{ round($item->qty_po, 3) }}</td>
                            <td data-label="QTY Remaining PO" align="right">{{ round($item->qty_remaining_po, 3) }}</td>
                            <td data-label="QTY GR" align="right">{{ round($item->qty_gr, 3) }}</td>
                            <td data-label="QTY Remaining" align="right">{{ round($item->qty_remaining, 3) }}</td>
                            <td data-label="Batch" align="right">{{ $item->batch ?? '-' }}</td>
                            <td data-label="UOM">{{ $item->uom }}</td>
                            <td data-label="Recepient">{{ $item->recepient }}</td>
                            <td data-label="Timestamp">{{ App\Library\Helper::DateConvertFormat($item->created_at, 'Y-m-d H:i:s', 'd/m/Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="col-12">
            <h4 class="mt-2 text-center">{{ __('Data Not Found') }}</h4>
        </div>
    @endif
@endsection
