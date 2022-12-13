@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Outstanding PO-STO Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>{{ __('Plant') }} :</strong> {{ $header['plant'] }}
                </div>
            </div>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">{{ __('Plant') }}</th>
                        <th scope="col">{{ __('Delivery Number') }}</th>
                        <th scope="col">{{ __('Date') }}</th>
                        <th scope="col">{{ __('Material Code') }}</th>
                        <th scope="col">{{ __('Material Name') }}</th>
                        <th scope="col">{{ __('Uom') }}</th>
                        <th scope="col">{{ __('Qty PO') }}</th>
                        <th scope="col">{{ __('Qty Outstanding') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="{{ __('Plant') }}">{{ $item['plant_from'] }}</td>
                            <td data-label="{{ __('Delivery Number') }}">{{ $item['sj'] }}</td>
                            <td data-label="{{ __('Date') }}">{{ $item['date'] }}</td>
                            <td data-label="{{ __('Material Code') }}">{{ $item['mat_code'] }}</td>
                            <td data-label="{{ __('Material Name') }}">{{ $item['mat_desc'] }}</td>
                            <td data-label="{{ __('Uom') }}">{{ $item['uom'] }}</td>
                            <td data-label="{{ __('Qty PO') }}" align="right">{{ $item['qty'] }}</td>
                            <td data-label="{{ __('Qty Outstanding') }}" align="right">{{ $item['qty_out'] }}</td>
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
