@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Saldo Vendor Used Oil Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                </div>
            </div>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">{{ __('Vendor') }}</th>
                        <th scope="col">{{ __('Saldo') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="{{ __('Vendor') }}">{{ $item->name }}</td>
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
@endsection
