@extends('reports.templates.view.template1')
@section('content')
    @foreach($datas as $data)
        @if ( $data['count'] > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('History Saldo Vendor Used Oil Report') }}</p>
            </div>
            <div class="col-12">
                <div class="row head-item-row">
                    <div class="col-12 head-item">
                        <strong>Vendor :</strong> {{ $data['header']['vendor'] }}
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
                            <th scope="col">{{ __('Transaction Type') }}</th>
                            <th scope="col">{{ __('Nominal') }}</th>
                            <th scope="col">{{ __('Saldo') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['items'] as $i => $item)
                            <tr>
                                <td data-label="No">{{ $i+1 }}</td>
                                <td data-label="{{ __('Date') }}">{{ date("d-m-Y", strtotime($item->date)) }}</td>
                                <td data-label="{{ __('Transaction Type') }}">{{ $item->description }}</td>
                                <td data-label="{{ __('Nominal') }}" align="right">{{ App\Library\Helper::convertNumberToInd($item->nominal, '', 0) }}</td>
                                <td data-label="{{ __('Saldo') }}" align="right">{{ App\Library\Helper::convertNumberToInd($item->saldo, '', 0) }}</td>
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
