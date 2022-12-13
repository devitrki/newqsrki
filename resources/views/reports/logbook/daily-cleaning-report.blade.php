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
                <strong>Shift :</strong> {{ $header['shift'] }}
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Daily Task</th>
                    <th scope="col">Section</th>
                    <th scope="col">Frekuensi</th>
                    @foreach($header['shifts'] as $s)
                    <th scope="col">{{ $s }}</th>
                    @endforeach
                    <th scope="col">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Daily Task">{{ $item->task }}</td>
                        <td data-label="Section">{{ $item->section }}</td>
                        <td data-label="Frekuensi">{{ $item->frekuensi }}</td>
                        @foreach($header['shifts'] as $is => $s)
                        <td data-label="{{ $s }}">
                            @if($item->{'checklis_'.($is + 1)} == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        @endforeach
                        <td data-label="PIC">{{ $item->pic }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
