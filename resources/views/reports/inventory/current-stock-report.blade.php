@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Current Stock Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>{{ __('Plant') }} :</strong> {{ $header['plant'] }}
                </div>
                <div class="col-12 head-item">
                    <strong>{{ __('Material Type') }} :</strong> {{ $header['material_type'] }}
                </div>
                <div class="col-12 head-item">
                    <strong>{{ __('Stock Date') }} :</strong> {{ date('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">{{ __('Plant') }}</th>
                        <th scope="col">{{ __('Material Type') }}</th>
                        <th scope="col">{{ __('Material Code') }}</th>
                        <th scope="col">{{ __('Material Name') }}</th>
                        <th scope="col">{{ __('Qty') }}</th>
                        <th scope="col">{{ __('Uom') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="{{ __('Plant') }}">{{ $item['plant'] }}</td>
                            <td data-label="{{ __('Material Type') }}">{{ $item['material_type'] }}</td>
                            <td data-label="{{ __('Material Code') }}">{{ $item['material_code'] }}</td>
                            <td data-label="{{ __('Material Name') }}">{{ $item['material_desc'] }}</td>
                            <td data-label="{{ __('Qty') }}" align="right">{{ App\Library\Helper::convertNumberToInd($item['qty'], '', 3) }}</td>
                            <td data-label="{{ __('Uom') }}">{{ $item['uom'] }}</td>
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
