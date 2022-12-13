<x-mail::message>
@if($asset->step_request == 1)
{{ __('Dear') }} {{$dear}},
<br/>
<br/>
{{ __('There is request asset transfer with the information below') }}
@elseif($asset->step_request == 3)
{{ __('Dear') }} Validator {{ $validator }},
<br/>
<br/>
{{ __('There is request asset transfer with the information below') }}
@elseif($asset->step_request == 4)
{{ __('Dear') }} {{ $request_by }},
<br/>
<br/>
{{ __('There is request asset transfer have not approved with the information below') }}
@elseif($asset->step_request == 5)
{{ __('Dear') }} {{ $plant_send->initital . ' ' . $plant_send->short_name }},
<br/>
<br/>
{{ __('There is request asset transfer have confirmation validator with the information below') }}
@elseif($asset->step_request == 6)
{{ __('Dear') }} {{ $request_by }},
<br/>
<br/>
{{ __('There is request asset transfer have rejected validator with the information below') }}
@elseif($asset->step_request == 7)
{{ __('Dear') }} {{ __('All') }},
<br/>
<br/>
{{ __('There is request asset transfer have send by DC with the information below') }}
@endif

<div class="table">
    <table>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Requester') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $request_by }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Note') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->note_request }}</td>
    </tr>
    @if(in_array($asset->step_request, [3,5,6,7]) )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Approval By') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $dear }}</td>
    </tr>
    @endif
    @if($asset->step_request == 3)
    @if(!is_null($asset->assign_asset_validator_id))
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Assign By') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $validator_assign }}</td>
    </tr>
    @endif
    @elseif( in_array($asset->step_request, [5,7]) )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Validator') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $validator }}</td>
    </tr>
    @endif
    @if( in_array($asset->step_request, [4,6]) )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Note Rejected') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->note_rejected }}</td>
    </tr>
    @endif
    </table>
</div>
@if( ($asset->req_number.$asset->req_number_sub) != ($asset->number.$asset->number_sub) || in_array($asset->step_request, [1,4,5,6,7]) )
<br/>
{{ __('With information on the request asset transfer as follows') }}
<br/>
<br/>
<div class="table">
    <table>
    <tr>
        <td align="left" style="width: 150px; !important;">{{ __('Number') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
    <td align="left" style="width: 400px !important">{{ $asset->req_number }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Sub Number') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->req_number_sub }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Description') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->req_description }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Qty') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->req_qty_mutation }}</td>
    </tr>
    </table>
</div>
@endif
@if( in_array($asset->step_request, [5,7]))
<br/>
@if($asset->step_request == 5)
{{ __('And these are confirmed assets as follows') }}
@else
{{ __('And this is the asset that DC has sent as follows') }}
@endif
<br/>
<br/>
<div class="table">
    <table>
    <tr>
        <td align="left" style="width: 150px; !important;">{{ __('Number') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
    <td align="left" style="width: 400px !important">{{ $asset->number }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Sub Number') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->number_sub }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Description') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->description }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Qty') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->qty_mutation }}</td>
    </tr>
    </table>
</div>
<br/>
@endif
<br/>
@if($asset->step_request == 1)
{{ __('Waiting for approval from you') }}!
<br/>
{{ __('Please check your web application and process it immediately') }}.
@elseif($asset->step_request == 3)
{{ __('Waiting for confirmation from you') }}!
<br/>
{{ __('Please check your web application and process it immediately') }}.
@elseif($asset->step_request == 5)
{{ __('Please confirm this asset transfer has been sent in web application') }}!
@elseif($asset->step_request == 7)
{{ __('Request Asset Transfer Done') }}!
@endif
<br/>
<br/>
<br/>
{{ __('Thank You') }}
<br/>
<br/>
<br/>

<x-mail::button :url="'qsr.richeesefactory.com'">
    {{ __('View Request Asset Transfer') }}
</x-mail::button>

</x-mail::message>
