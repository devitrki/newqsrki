@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Stock Card') }}</p>
    </div>
    <div class="col-12">
        <div class="row head-item-row">
            <div class="col-12 col-md-4 head-item">
                <strong>{{ __('Plant' )}} :</strong> {{ __($header['plant']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>{{ __('Year' )}} :</strong> {{ __($header['year']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>{{ __('Month' )}} :</strong> {{ __($header['month']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>{{ __('Item' )}} :</strong> {{ __($header['materialName']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>{{ __('UOM' )}} :</strong> {{ __($header['materialUom']) }}
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">{{ __('Date') }}</th>
                    <th scope="col">{{ __('No PO') }}</th>
                    <th scope="col">{{ __('Stock Initial') }}</th>
                    <th scope="col">{{ __('Stock In GR') }}</th>
                    <th scope="col">{{ __('Stock In TF') }}</th>
                    <th scope="col">{{ __('Stock Out Used') }}</th>
                    <th scope="col">{{ __('Stock Out Waste') }}</th>
                    <th scope="col">{{ __('Stock Out TF GI') }}</th>
                    <th scope="col">{{ __('Stock Last') }}</th>
                    <th scope="col">{{ __('Description') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="{{ __('Date') }}">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'j') }}</td>
                        <td data-label="{{ __('No PO') }}">{{ $item->no_po }}</td>
                        <td data-label="{{ __('Stock Initial') }}">{{ $item->stock_initial }}</td>
                        <td data-label="{{ __('Stock In GR') }}">{{ $item->stock_in_gr }}</td>
                        <td data-label="{{ __('Stock In TF') }}">{{ $item->stock_in_tf }}</td>
                        <td data-label="{{ __('Stock Out Used') }}">{{ $item->stock_out_used }}</td>
                        <td data-label="{{ __('Stock Out Waste') }}">{{ $item->stock_out_waste }}</td>
                        <td data-label="{{ __('Stock Out TF GI') }}">{{ $item->stock_out_tf }}</td>
                        <td data-label="{{ __('Stock Last') }}">{{ $item->stock_last }}</td>
                        <td data-label="{{ __('Description') }}">{{ $item->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
