@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Cleaning Duties') }}</p>
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
                <strong>Section :</strong> {{ $header['section'] }}
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <tbody>
                {{-- daily --}}
                <tr>
                    <th scope="col">No</th>
                    <th scope="col" colspan="2">Daily Task</th>
                    <th scope="col">Opening</th>
                    <th scope="col">Closing</th>
                    <th scope="col" colspan="2">Midnite</th>
                </tr>
                @foreach ($items['daily'] as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Daily Task" colspan="2">{{ $item->task }}</td>
                        <td data-label="Opening">
                            @if($item->opening == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td data-label="Closing">
                            @if($item->closing == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td data-label="Midnite" colspan="2">
                            @if($item->midnite == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
                {{-- weekly --}}
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Weekly Task</th>
                    <th scope="col">Day</th>
                    <th scope="col">Opening</th>
                    <th scope="col">Closing</th>
                    <th scope="col">Midnite</th>
                    <th scope="col">Pic</th>
                </tr>
                @foreach ($items['weekly'] as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Weekly Task">{{ $item->task }}</td>
                        <td data-label="Day">{{ $item->day }}</td>
                        <td data-label="Opening">
                            @if($item->opening == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td data-label="Closing">
                            @if($item->closing == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td data-label="Midnite">
                            @if($item->midnite == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td data-label="Pic">{{ $item->pic }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7">
                        <strong>Catatan:</strong>

                        @isset($items['weekly'][0]->note)
                        {{ $items['weekly'][0]->note }}
                        @endisset
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
