<x-mail::message>
@if ($send_flag == 'am_approve')
Dear Mr. / Mrs. <b> {{ $am_plant->name }} </b>,
<br/>
<br/>
{{ __('There is a petty cash submission with information as below') }} :
@elseif ($send_flag == 'am_approved')
Dear Outlet <b> {{ $plant->initital . ' ' . $plant->short_name }} </b>,
<br/>
<br/>
{{ __('There is a petty cash have been approved by the area manager with information as below') }} :
@elseif ($send_flag == 'am_unapproved' || $send_flag == 'fa_rejected')
Dear Outlet <b> {{ $plant->initital . ' ' . $plant->short_name }} </b>,
<br/>
@if ($send_flag == 'fa_rejected')
{{ __('There is a petty cash have been rejected by the FA with information as below') }} :
@else
{{ __('There is a petty cash have been rejected by the area manager with information as below') }} :
@endif
<br/>
@endif

<div class="table">
    <table>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Plant') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $plant->initital . ' ' . $plant->short_name }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Type ID') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $pettycash->type_id }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Transaction Type') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $transaction_type }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Transaction ID') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            @if ($send_flag == 'am_approve')
            <td align="left" style="width: 400px !important">{{ $range_id_item }}</td>
            @else
            <td align="left" style="width: 400px !important">{{ $item_id }}</td>
            @endif
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Transaction Date') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ \App\Library\Helper::DateConvertFormat( $pettycash->transaction_date, 'Y-m-d', 'd-m-Y' ) }}</td>
        </tr>
        @if ($send_flag == 'am_unapproved' || $send_flag == 'fa_rejected')
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Reject By') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            @if ($send_flag == 'fa_rejected')
            <td align="left" style="width: 400px !important">{{ $fa_name }}</td>
            @else
            <td align="left" style="width: 400px !important">{{ $am_plant->name }}</td>
            @endif
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Description Reject') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $pettycash->description_reject }}</td>
        </tr>
        @endif
    </table>
</div>
<br/>
{{ __('So please check your web application and get to confirmation') }}
<br/>
<br/>
{{ __('Thank You') }}
<br/>
<br/>
<br/>

<x-mail::button :url="'qsr.richeesefactory.com'">
{{ __('View Petty Cash') }}
</x-mail::button>

</x-mail::message>
