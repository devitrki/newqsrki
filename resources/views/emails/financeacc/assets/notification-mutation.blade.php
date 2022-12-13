<x-mail::message>
@if($asset->status_mutation == 1)
Dear Mr/Mrs. {{ $approve1_name }},
<br/>
<br/>
{{ __('There is an asset transfer request with the information below') }}
@elseif($asset->status_mutation == 2)
Dear Mr/Mrs. {{ $approve1_name }},
<br/>
<br/>
{{ __('There is an asset transfer request that has been canceled with the information below') }}
@elseif($asset->status_mutation == 3)
Dear Mr/Mrs. Validator {{ $validator }},
<br/>
<br/>
{{ __('There is request asset transfer with the information below') }}
@elseif($asset->status_mutation == 4)
Dear Mr/Mrs. {{ $requestor }},
<br/>
<br/>
{{ __('There is request asset transfer have rejected by approver 1 with the information below') }}
@elseif($asset->status_mutation == 5)
Dear Mr/Mrs. {{ $approve2_name }},
<br/>
<br/>
{{ __('There is request asset transfer have confirmation validator with the information below') }}
@elseif($asset->status_mutation == 6)
{{ __('Dear') }} {{ $requestor }},
<br/>
<br/>
{{ __('There is request asset transfer have rejected validator with the information below') }}
@elseif($asset->status_mutation == 7)
@if($asset->level_request_third_id != '0')
Dear Mr/Mrs. {{ $approve3_name }},
<br/>
<br/>
{{ __('There is asset transfer have confirmation validator with the information below') }}
@else
@if ($asset->sender_cost_center_id != '0')
Dear {{ $sender_costcenter_name }},
@else
Dear {{ $plant_send->initital . ' ' . $plant_send->short_name }},
@endif
<br/>
<br/>
{{ __('There is asset transfer have approved with the information below') }}
@endif
@elseif($asset->status_mutation == 8 )
{{ __('Dear') }} {{ $requestor }},
<br/>
<br/>
{{ __('There is request asset transfer have rejected by approver 2 with the information below') }}
@elseif($asset->status_mutation == 9)
Dear {{ $plant_send->initital . ' ' . $plant_send->short_name }},
<br/>
<br/>
{{ __('There is asset transfer have approved with the information below') }}
@elseif($asset->status_mutation == 10)
{{ __('Dear') }} {{ $requestor }},
<br/>
<br/>
{{ __('There is request asset transfer have rejected by approver 3 with the information below') }}
@elseif($asset->status_mutation == 11)
@if ($asset->receiver_cost_center_id != '0')
Dear {{ $receiver_costcenter_name }},
@else
Dear {{ $plant_receiver->initital . ' ' . $plant_receiver->short_name }},
@endif
<br/>
<br/>
{{ __('There is asset transfer have confirmed send with the information below') }}
@elseif($asset->status_mutation == 12)
{{ __('Dear') }} {{ $requestor }},
<br/>
<br/>
{{ __('There is request asset transfer have rejected by sender with the information below') }}
@elseif($asset->status_mutation == 13)
Dear All,
<br/>
<br/>
{{ __('There is asset transfer have accepted plant receiver with the information below') }}
@elseif($asset->status_mutation == 14)
{{ __('Dear') }} {{ $requestor }},
<br/>
<br/>
{{ __('There is request asset transfer have rejected by receiver with the information below') }}
@endif
<div class="table">
    <table>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Requestor') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $requestor }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Plant Sender') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $plant_send->initital . ' ' . $plant_send->short_name . ' (' . $asset->from_cost_center . ')' }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Plant Receiver') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $plant_receiver->initital . ' ' . $plant_receiver->short_name . ' (' . $asset->to_cost_center . ')' }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Est Transfer Date') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ App\Library\Helper::DateConvertFormat($asset->date_send_est, 'Y-m-d H:i:s', 'd-m-Y') }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Note Request') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->note_request }}</td>
    </tr>
    @if($asset->status_mutation >= 3 )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Approver 1') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $approve1_name . ' (' . $asset->level_request_first . ')' }}</td>
    </tr>
    @endif
    @if($asset->status_mutation == 3 && !is_null($asset->assign_asset_validator_id))
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Assign Validator By') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $validator_assign }}</td>
    </tr>
    @endif
    @if($asset->status_mutation >= 5 )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Validator') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $validator }}</td>
    </tr>
    @endif
    @if($asset->status_mutation >= 7)
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Approver 2') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $approve2_name . ' (' . $asset->level_request_second . ')' }}</td>
    </tr>
    @endif
    @if($asset->status_mutation >= 9 && $asset->level_request_third_id != '0' )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Approver 3') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $approve3_name . ' (' . $asset->level_request_third . ')' }}</td>
    </tr>
    @endif
    @if($asset->status_mutation >= 11 && $asset->status_mutation != 12 )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('PIC Sender') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->pic_sender }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Condition Asset Transfer') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->condition_send }}</td>
    </tr>
    @endif
    @if($asset->status_mutation >= 13 && $asset->status_mutation != 14 )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('PIC Receiver') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->pic_receiver }}</td>
    </tr>
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Condition Asset Receive') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->condition_receive }}</td>
        </tr>
    @endif
    @if( in_array($asset->status_mutation, [4, 6, 8, 10, 12, 14]) )
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Reason Rejected') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->reason_rejected }}</td>
    </tr>
    @endif
    </table>
</div>
@if( $asset->req_number.$asset->req_number_sub != $asset->number.$asset->number_sub || $asset->status_mutation < 5)
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
    <tr>
        <td align="left" style="width: 150px !important;">{{ __('Remark') }}</td>
        <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
        <td align="left" style="width: 400px !important">{{ $asset->req_remark }}</td>
    </tr>
    </table>
</div>
@endif
@if($asset->status_mutation >= 5)
<br/>
@if( $asset->req_number.$asset->req_number_sub != $asset->number.$asset->number_sub )
{{ __('And these are confirmed assets as follows') }}
@else
{{ __('with information on assets to be transferred as follows') }}
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
        @if($asset->status_mutation >= 13 )
        <tr>
            <td align="left" style="width: 150px !important;">{{ __('Remark') }}</td>
            <td align="left" style="width: 20px !important;">:&nbsp;&nbsp;</td>
            <td align="left" style="width: 400px !important">{{ $asset->remark }}</td>
        </tr>
        @endif
    </table>
</div>
@endif
<br/>
<br/>
@if( in_array($asset->status_mutation, [1, 3, 5, 7, 9, 11]) )
{{ __('Waiting for approval from you') }}!
<br/>
{{ __('Please check your web application and process it immediately') }}.
@else
{{ __('Asset Transfer Done') }}.
@endif
<br/>
<br/>
{{ __('Thank You') }}
<br/>
<br/>

<x-mail::button :url="'qsr.richeesefactory.com'">
{{ __('View Asset Transfer') }}
</x-mail::button>

</x-mail::message>
