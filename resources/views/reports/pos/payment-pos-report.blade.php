@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Payment POS Report') }}</p>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ __('DATE') }}</th>
                        <th>{{ __('STORE CODE') }}</th>
                        <th>{{ __('STORE NAME') }}</th>
                        <th>{{ __('POS') }}</th>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                            <th>{{ __('QTY') }}</th>
                        @endforeach
                        <th>{{ __('TOTAL PAYMENT') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td>{{ $item['date'] }}</td>
                            <td>{{ $item['store_code'] }}</td>
                            <td>{{ $item['store_name'] }}</td>
                            <td>{{ $item['pos'] }}</td>

                            @foreach($headers as $header)
                            <td align="right">{{ $item[$header] }}</td>
                            <td align="right">{{ $item['qty'.$header] }}</td>
                            @endforeach

                            <td align="right">{{ $item['total_payment'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="col-12">
            <h4 class="mt-2 text-center">{{ __($message) }}</h4>
        </div>
    @endif
@endsection
