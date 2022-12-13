@extends('reports.templates.view.template1')
@section('content')
    @foreach($datas as $data)
        @if ( $data['count'] > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Waste / Scrap Report') }}</p>
            </div>
            <div class="col-12">
                <div class="row head-item-row">
                    <div class="col-12 head-item">
                        <strong>Plant :</strong> {{ $data['header']['plant_code'] . ' - ' . $data['header']['plant_name'] }}
                    </div>
                    <div class="col-12 head-item">
                        <strong>{{ __('Date') }} :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}
                    </div>
                </div>
            </div>
            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">{{ __('Material Code') }}</th>
                            <th scope="col">{{ __('Material Name') }}</th>
                            <th scope="col">{{ __('QTY') }}</th>
                            <th scope="col">{{ __('Uom') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($data['items'] as $i => $item)
                            <tr>
                                <td data-label="No">{{ $i+1 }}</td>
                                <td data-label="{{ __('Material Code') }}">{{ $item->material_code }}</td>
                                <td data-label="{{ __('Material Name') }}">{{ $item->material_name }}</td>
                                <td data-label="{{ __('QTY') }}" align="right">{{ App\Library\Helper::convertNumberToInd(abs($item->qty), '', 3) }}</td>
                                <td data-label="{{ __('Uom') }}">{{ $item->uom }}</td>
                            </tr>
                            @php
                                $total += $item->qty;
                            @endphp
                        @endforeach
                        <tr>
                            <td colspan="3" data-label="Total Qty" align="right"><b>{{ __('Total Qty') }}</b></td>
                            <td data-label="{{ __('QTY') }}" align="right">{{ App\Library\Helper::convertNumberToInd($total, '', 3) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="col-12">
                <h4 class="mt-2 text-center">{{ __('Data Not Found') }}</h4>
            </div>
        @endif
    @endforeach
@endsection
