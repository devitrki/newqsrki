@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('GR Plant Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>Plant :</strong> {{ $header['plant'] }}
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
                        <th scope="col">GI Number</th>
                        <th scope="col">PO Number</th>
                        <th scope="col">Issuing Plant</th>
                        <th scope="col">Receive Date</th>
                        <th scope="col">Material Code</th>
                        <th scope="col">Material Description</th>
                        <th scope="col">QTY PO</th>
                        <th scope="col">QTY Remaining PO</th>
                        <th scope="col">QTY GR</th>
                        <th scope="col">QTY Remaining</th>
                        <th scope="col">UOM</th>
                        <th scope="col">Recepient</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="GR Number">{{ $item->document_number }}</td>
                            <td data-label="GI Number">{{ $item->delivery_number }}</td>
                            <td data-label="PO Number">{{ $item->posto_number }}</td>
                            <td data-label="Issuing Plant">{{ $item->initital . ' ' .$item->short_name }}</td>
                            <td data-label="Receive Date">{{ App\Library\Helper::DateConvertFormat($item->receive_date, 'Y-m-d', 'd/m/Y') }}</td>
                            <td data-label="Material Code">{{ $item->material_code }}</td>
                            <td data-label="Material Description">{{ $item->material_desc }}</td>
                            <td data-label="QTY PO" align="right">{{ round($item->qty_po, 3) }}</td>
                            <td data-label="QTY Remaining" align="right">{{ round($item->qty_b4_gr, 3) }}</td>
                            <td data-label="QTY GR" align="right">{{ round($item->qty_gr, 3) }}</td>
                            <td data-label="Outstanding GR" align="right">{{ round($item->qty_remaining, 3) }}</td>
                            <td data-label="UOM">{{ $item->uom }}</td>
                            <td data-label="Recepient">{{ $item->recepient }}</td>
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