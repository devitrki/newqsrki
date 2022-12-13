@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Toilet Checklist') }}</p>
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
                <strong>Shift :</strong>
                @if($header['shift'] == '1')
                    Opening
                @elseif($header['shift'] == '2')
                    Closing
                @else
                    Midnite
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Task</th>
                    @foreach($header['shifts'] as $s)
                    <th scope="col">{{ $s }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Task">{{ $item->task }}</td>
                        @foreach($header['shifts'] as $is => $s)
                        <td data-label="{{ $s }}">
                            @if($item->{'checklis_'.($is + 1)} == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
