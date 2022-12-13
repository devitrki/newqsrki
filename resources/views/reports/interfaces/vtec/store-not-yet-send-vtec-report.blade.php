@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
    <p class="text-center m-0">{{ __('Store Not Yet Send Report') }}</p>
    <p class="text-center m-0">{{ $header['date'] }}</p>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">AM</th>
                    <th scope="col">Store Code</th>
                    <th scope="col">Store Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="AM">{{ $item['am'] }}</td>
                        <td data-label="Store Code">{{ $item['store_code'] }}</td>
                        <td data-label="Store Name">{{ $item['store_name'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
