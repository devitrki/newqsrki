@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Stock Material Plant Used Oil Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>Plant :</strong> {{ $header['plant'] }}
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
                        <th scope="col">{{ __('Stock') }}</th>
                        <th scope="col">{{ __('Material Uom') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="{{ __('Material Code') }}">{{ $item->code }}</td>
                            <td data-label="{{ __('Material Name') }}">{{ $item->name }}</td>
                            <td data-label="{{ __('Stock') }}" align="right">{{ App\Library\Helper::convertNumberToInd($item->stock, '', 2) }}</td>
                            <td data-label="{{ __('Material Uom') }}">{{ $item->uom }}</td>
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
