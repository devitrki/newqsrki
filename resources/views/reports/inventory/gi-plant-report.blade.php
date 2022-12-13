@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('GI Plant Report') }}</p>
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
                        <th scope="col">GI Number</th>
                        <th scope="col">POSTO Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Material Code</th>
                        <th scope="col">Material Description</th>
                        <th scope="col">QTY GI</th>
                        <th scope="col">QTY GR</th>
                        <th scope="col">Outstanding GR</th>
                        <th scope="col">UOM</th>
                        <th scope="col">Receiving Plant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="GI Number">{{ $item->document_number }}</td>
                            <td data-label="POSTO Number">{{ $item->document_posto }}</td>
                            <td data-label="Date">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
                            <td data-label="Material Code">{{ $item->material_code }}</td>
                            <td data-label="Material Description">{{ $item->material_desc }}</td>
                            <td data-label="QTY GI" align="right">{{ $item->gi_qty }}</td>
                            <td data-label="QTY GR" align="right">{{ $item->gr_qty }}</td>
                            <td data-label="Outstanding GR" align="right">{{ $item->gr_outstanding }}</td>
                            <td data-label="UOM">{{ $item->uom }}</td>
                            <td data-label="Receiving Plant">{{ $item->initital . ' ' .$item->short_name }}</td>
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
