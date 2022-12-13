@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Temperature Form') }}</p>
    </div>
    <div class="col-12">
        <div class="row head-item-row">
            <div class="col-12 head-item">
                <strong>Outlet :</strong> {{ __($header['plant']) }}
            </div>
            <div class="col-12 head-item">
                <strong>From Date :</strong> {{ __($header['date_from']) }}
            </div>
            <div class="col-12 head-item">
                <strong>Until Date :</strong> {{ __($header['date_until']) }}
            </div>
            <div class="col-12 head-item">
                <strong>Storage :</strong> {{ __($header['storage']) }}
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">{{ __('Date')}}</th>
                    @foreach($header['range_check_temp'] as $is => $temp)
                    <th scope="col">{{ $temp }}</th>
                    @endforeach
                    <th scope="col">{{ __('Note') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="{{ __('Date') }}">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd-m-Y') }}</td>
                        @foreach($header['range_check_temp'] as $is => $temp)
                        <td data-label="{{ $temp }}">{{ $item->{'temp_'.($is+1)} }}</td>
                        @endforeach
                        <td data-label="{{ __('Note') }}">{{ $item->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
