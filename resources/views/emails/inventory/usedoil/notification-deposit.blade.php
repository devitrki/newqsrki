<x-mail::message>

@if( $type == 'submit' )
{{ __('Dear') }} {{ __('Department') }} FA,

{{ __('There is a deposit vendor used oil submission with the information below') }}
@elseif( $type == 'confirm' )
{{ __('Dear') }} {{ $uo_deposit->created_by }},

{{ __('There are vendor deposits that are approved, with the information below') }}
@elseif( $type == 'reject' )
{{ __('Dear') }} {{ $uo_deposit->created_by }},

{{ __('There are vendor deposits that are rejected, with the information below') }}
@endif

<div class="table">
    <table>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Company') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->company_name }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Document Number') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->document_number }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Vendor') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->vendor_name }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Date') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->deposit_date_desc }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Richeese Bank') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->richeese_bank }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Nominal') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->deposit_nominal_desc }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Deposit Type') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->type_deposit_desc }}</td>
        </tr>
        @if( $uo_deposit->type_deposit != '1' )
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Bank') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->transfer_bank }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Bank Account') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->transfer_bank_account }}</td>
        </tr>
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Bank Account Name') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $uo_deposit->transfer_bank_account_name }}</td>
        </tr>
        @endif
        @if( $type == 'reject' )
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Reject Description') }}</td>
            <td colspan="2" align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3" align="left">{{ $uo_deposit->reject_description }}</td>
        </tr>
        @endif
    </table>
</div>
<br/>
<br/>

@if( $type == 'submit' )
{{ __('Waiting for approval from you') }}!
@endif

<br/>
<br/>
{{ __('Thank You') }}
<br/>
<br/>
<br/>

<x-mail::button :url="'qsr.richeesefactory.com'">
    {{ __('View Deposit Vendor Used Oil') }}
</x-mail::button>

</x-mail::message>
