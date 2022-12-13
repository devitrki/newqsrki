@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Daily Wasted') }}</p>
    </div>
    <div class="col-12">
        <div class="row head-item-row">
            <div class="col-12 col-md-4 head-item">
                <strong>Outlet :</strong> {{ __($header['plant']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>Date :</strong> {{ __($header['date']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>MOD :</strong>
                @isset($header['appReview']->mod_pic)
                    {{ $header['appReview']->mod_pic }}
                @else
                    -
                @endisset
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>Crew Opening :</strong>
                @isset($header['appReview']->crew_opening)
                    {{ $header['appReview']->crew_opening }}
                @else
                    -
                @endisset
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>Crew Midnight :</strong>
                @isset($header['appReview']->crew_midnight)
                    {{ $header['appReview']->crew_midnight }}
                @else
                    -
                @endisset
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>Crew Closing :</strong>
                @isset($header['appReview']->crew_closing)
                    {{ $header['appReview']->crew_closing }}
                @else
                    -
                @endisset
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Item</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Uom</th>
                    <th scope="col">Time</th>
                    <th scope="col">Remark</th>
                    <th scope="col">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Item">{{ $item->name }}</td>
                        <td data-label="Qty">{{ $item->qty }}</td>
                        <td data-label="Uom">{{ $item->uom }}</td>
                        <td data-label="Time">{{ $item->time }}</td>
                        <td data-label="Remark">{{ $item->remark }}</td>
                        <td data-label="PIC">{{ $item->last_update }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
