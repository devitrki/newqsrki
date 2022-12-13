@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0" style="margin-bottom: 5px !important;">{{ __('Log Transactions Vtec') }}</p>
        <p class="text-center m-0">{{ $header['date_from'] . ' - ' . $header['date_until'] }}</p>
        </div>
        <div class="col-12 p-0">
            @foreach ($items as $plant => $data)
            <table cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                <tbody>
                    <tr>
                        <td colspan="4" style="font-weight: bold;background-color:#f2f4f4;">{{ $plant }}</td>
                    </tr>
                    @foreach ($data as $date => $items)
                        <tr>
                            <td colspan="4" style="font-weight: bold;background-color:#f2f4f4;">Closing Date : {{ $date }}</td>
                        </tr>
                        @foreach ($items as $i => $item)
                        <tr>
                            <td width="30" style="text-align: center">
                                <div style="height: 1rem;">
                                    <i class="bx {{ $item['icon_status'] }}" style="color: {{ $item['icon_color'] }};font-weight: 900;"></i>
                                </div>
                            </td>
                            <td width="*">{{ $item['message'] }}</td>
                            <td width="140">{{ App\Library\Helper::DateConvertFormat($item['created_at'], 'Y-m-d H:i:s', 'd/m/Y H:i:s') }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            @endforeach
        </div>
    @else
        <div class="col-12">
            <h4 class="mt-2 text-center">{{ __('Data Not Found') }}</h4>
        </div>
    @endif
@endsection
