<?php

namespace App\Mail\Financeacc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

use App\Models\Plant;

class NotificationPettycash extends Mailable
{
    use Queueable, SerializesModels;

    public $sendFlag;
    public $itemId;
    public $faName;

    public $subjectMail;
    public $toMail = [];
    public $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sendFlag, $itemId, $faName = null)
    {
        if ($faName == null) {
            $faName = '';
        }

        $this->sendFlag = $sendFlag;
        $this->itemId = $itemId;
        $this->faName = $faName;

        $this->init();
    }

    public function init()
    {
        $pettycash = DB::table('pettycashes')
                        ->where('id', $this->itemId)
                        ->first();

        $countItem = DB::table('pettycashes')
                        ->where('transaction_id', $pettycash->transaction_id)
                        ->count('id');

        $minItemId = DB::table('pettycashes')
                        ->where('transaction_id', $pettycash->transaction_id)
                        ->min('id');

        if( $countItem > 1 ){

            $maxItemId = DB::table('pettycashes')
                            ->where('transaction_id', $pettycash->transaction_id)
                            ->max('id');

            $rangeIdItem = $minItemId . ' - ' . $maxItemId;

        } else {

            $rangeIdItem = $minItemId;

        }

        // transaction type
        if( $pettycash->type == '1' ){
            $transactionType = 'Debit';
        } else if( $pettycash->type == '0' ) {
            $transactionType = 'Credit';
        } else{
            $transactionType = 'Credit By PO';
        }

        $plant = Plant::getDataPlantById($pettycash->plant_id);
        $am_plant = Plant::getDataAMPlantById($pettycash->plant_id);

        $this->data = [
            'pettycash' => $pettycash,
            'plant' => $plant,
            'am_plant' => $am_plant,
            'range_id_item' => $rangeIdItem,
            'transaction_type' => $transactionType,
            'send_flag' => $this->sendFlag,
            'item_id' => $this->itemId,
            'fa_name' => $this->faName,
        ];
        switch ($this->sendFlag) {
            case 'am_approve':
                $this->subjectMail = 'Submission Petty cash on outlet ' . $plant->initital . ' ' . $plant->short_name;
                $this->toMail[] = $am_plant->email;
                break;
            case 'am_approved':
                $this->subjectMail = 'Petty cash on outlet ' . $plant->initital . ' ' . $plant->short_name . ' has been approved';
                $this->toMail[] = $plant->email;
                break;
            case 'am_unapproved':
                $this->subjectMail = 'Petty cash on outlet ' . $plant->initital . ' ' . $plant->short_name . ' has been rejected';
                $this->toMail[] = $plant->email;
                break;
            case 'fa_rejected':
                $this->subjectMail = 'Petty cash on outlet ' . $plant->initital . ' ' . $plant->short_name . ' has been rejected';
                $this->toMail[] = $plant->email;
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
            to: 'yudhapermana.dev@gmail.com',
            // to: $this->toMail
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
            markdown: 'emails.financeacc.notification-pettycash',
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
