@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Gas Meter Form') }}</p>
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
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">{{ __('No') }}</th>
                    <th scope="col">{{ __('Date') }}</th>
                    <th scope="col">{{ __('Initial Meter') }}</th>
                    <th scope="col">{{ __('Final Meter') }}</th>
                    <th scope="col">{{ __('Usage') }}</th>
                    <th scope="col">{{ __('Pic') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="{{ __('No') }}">{{ $i + 1 }}</td>
                        <td data-label="{{ __('Date') }}">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'j') }}</td>
                        <td data-label="{{ __('Initial Meter') }}">{{ $item->initial_meter }}</td>
                        <td data-label="{{ __('Final Meter') }}">{{ $item->final_meter }}</td>
                        <td data-label="{{ __('Usage') }}">{{ $item->usage }}</td>
                        <td data-label="{{ __('Pic') }}">{{ $item->pic }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
