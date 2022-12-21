<?php

namespace App\Mail\Financeacc\Assets;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Models\Plant;
use App\Models\User;
use App\Models\Financeacc\AssetValidator;

class NotificationRequestMutation extends Mailable
{
    use Queueable, SerializesModels;

    public $id;

    public $subjectMail;
    public $toMail = [];
    public $ccMail = [];
    public $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;

        $this->init();
    }

    public function init()
    {
        $requestMutation = DB::table('asset_request_mutations')
                                ->where('id', $this->id)
                                ->first();

        $this->subjectMail = '';
        $this->ccMail = [];
        $this->toMail = [];
        $this->data = [];

        $plant_send = Plant::getDataPlantById($requestMutation->from_plant_id);
        $plant_receiver = Plant::getDataPlantById($requestMutation->to_plant_id);
        $dearName = ($requestMutation->level_request == 'am') ? 'Regional Manager' : 'Head Of Department';
        $this->data = [
            'asset' => $requestMutation,
            'validator' => AssetValidator::getNameById($requestMutation->asset_validator_id),
            'validator_assign' => AssetValidator::getNameById($requestMutation->assign_asset_validator_id),
            'request_by' => User::getNameById($requestMutation->user_id) . ' (' . User::getDepartmentById($requestMutation->user_id) . ')',
            'plant_send' => $plant_send,
            'plant_receiver' => $plant_receiver,
            'dear' => User::getNameById($requestMutation->level_request_id) . ' (' . $dearName . ')'
        ];

        /*
            Step request asset mutation
            1 = submit from user request to hod / am
            2 = cancel by user
            3 = approve hod
            4 = unapprove hod
            5 = confirmation validator
            6 = reject by validator
            7 = confirmation send dc
            8 = reject by dc
        */

        switch ($requestMutation->step_request) {
            case '1':
                $this->subjectMail = Lang::get("Approval Asset Transfer Web");
                // send to RM / HOD
                $this->toMail = User::getEmailById($requestMutation->level_request_id);
                break;
            case '3':
                $this->subjectMail = Lang::get("Request Asset Transfer Web");
                $this->toMail = AssetValidator::getListEmailPicValidator($requestMutation->asset_validator_id, $requestMutation->from_plant_id);
                break;
            case '4':
                $this->subjectMail = Lang::get("UnApprove Request Asset Transfer Web");
                $this->toMail = User::getEmailById($requestMutation->user_id);
                break;
            case '5':
                $this->subjectMail = Lang::get("Confirmation Request Asset Transfer Web");
                $modPlantFrom = Plant::getMODIdPlantById($requestMutation->from_plant_id);
                $this->toMail = User::getEmailById($modPlantFrom);
                break;
            case '6':
                $this->subjectMail = Lang::get("Rejected Request Asset Transfer Web");
                $this->toMail = User::getEmailById($requestMutation->user_id);
                break;
            case '7':
                $this->subjectMail = Lang::get("Send Request Asset Transfer Web");
                $modPlantFrom = Plant::getMODIdPlantById($requestMutation->from_plant_id);
                $this->toMail = AssetValidator::getListEmailPicValidator($requestMutation->asset_validator_id, $requestMutation->from_plant_id);
                $this->ccMail[] = User::getEmailById($requestMutation->user_id);
                $this->ccMail[] = User::getEmailById($modPlantFrom);
                break;
        }
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subjectMail,
            // to: 'yudhapermana.dev@gmail.com',
            to: $this->toMail,
            cc: $this->ccMail
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.financeacc.assets.notification-request-mutation',
            with: $this->data
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
