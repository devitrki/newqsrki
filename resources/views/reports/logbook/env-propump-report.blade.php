@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Env Control (Pro Pump)') }}</p>
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
                    <th scope="col">{{ __('DGTT & SA') }}</th>
                    <th scope="col">{{ __('HC') }}</th>
                    <th scope="col">{{ __('PIC') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="{{ __('No') }}">{{ $i + 1 }}</td>
                        <td data-label="{{ __('Date') }}">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'j') }}</td>
                        <td data-label="{{ __('DGTT & SA') }}">{{ $item->dgtt }}</td>
                        <td data-label="{{ __('HC') }}">{{ $item->hc }}</td>
                        <td data-label="{{ __('PIC') }}">{{ $item->pic }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
