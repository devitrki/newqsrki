@extends('reports.templates.view.template1')
@section('content')
    @foreach($datas as $data)
        @if ( $data['count'] > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Income Sales Detail Used Oil Report') }}</p>
            </div>
            <div class="col-12">
                <div class="row head-item-row">
                    <div class="col-12 head-item">
                        <strong>Plant :</strong> {{ $data['header']['plant'] }}
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
                            <th scope="col">{{ __('Date') }}</th>
                            <th scope="col">{{ __('Document Number') }}</th>
                            <th scope="col">{{ __('Vendor') }}</th>
                            <th scope="col">{{ __('Material') }}</th>
                            <th scope="col">{{ __('QTY') }}</th>
                            <th scope="col">{{ __('Price') }}</th>
                            <th scope="col">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['items'] as $i => $item)
                            <tr>
                                <td data-label="No">{{ $i+1 }}</td>
                                <td data-label="{{ __('Date') }}">{{ date("d-m-Y", strtotime($item->date)) }}</td>
                                <td data-label="{{ __('Document Number') }}">{{ $item->document_number }}</td>
                                <td data-label="{{ __('Vendor') }}">{{ $item->vendor_name }}</td>
                                <td data-label="{{ __('Material') }}">{{ $item->material_name }}</td>
                                <td data-label="{{ __('QTY') }}" align="right">{{ App\Library\Helper::convertNumberToInd(abs($item->qty), '', 2) }}</td>
                                <td data-label="{{ __('Price') }}" align="right">{{ App\Library\Helper::convertNumberToInd($item->price, '', 0) }}</td>
                                <td data-label="{{ __('Total') }}" align="right">{{ App\Library\Helper::convertNumberToInd(round(abs($item->qty) * $item->price, 0), '', 0) }}</td>
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
    @endforeach
@endsection
