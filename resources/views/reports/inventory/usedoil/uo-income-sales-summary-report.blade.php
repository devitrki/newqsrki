@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Income Sales Summary Used Oil Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
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
                        <th scope="col">{{ __('Plant Code') }}</th>
                        <th scope="col">{{ __('Plant Name') }}</th>
                        <th scope="col">{{ __('Total Sales') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="{{ __('Plant Code') }}">{{ App\Models\Plant::getCodeById($item->plant_id_sender) }}</td>
                            <td data-label="{{ __('Plant Name') }}">{{ App\Models\Plant::getShortNameById($item->plant_id_sender) }}</td>
                            <td data-label="{{ __('Total Sales') }}" align="right">{{ App\Library\Helper::convertNumberToInd($item->total_sales, '', 0) }}</td>
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
