<?php

namespace App\Jobs;

use App\Events\AnnouncementSMSSendProgress;
use App\Models\Announcement;
use App\Models\AnnouncementLog;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;
use App\Traits\MarkupTrait;
use App\Traits\SMSTrait;
use DB;
use Illuminate\Support\Facades\Auth;

class AnnouncementSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MarkupTrait, SMSTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $customer_id;
    private $announcement_id;
    private $auth_id;
    private $total_announcement;
    public function __construct($customer_id, $announcement_id, $total_announcement, $auth_id)
    {
        $this->customer_id = $customer_id;
        $this->announcement_id = $announcement_id;
        $this->total_announcement = $total_announcement;
        $this->auth_id = $auth_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $log_check = DB::table('announcement_log')->where('announcement_id', '=', $this->announcement_id)
            ->where('customer_id', '=', $this->customer_id)
            ->where('status', '=', 'sent')
            ->first();
        $customer = Customer::find($this->customer_id);
        $announcement = Announcement::find($this->announcement_id);
        if (!$log_check && $customer && $announcement) {

            $sms_template = EmailTemplate::where('id', '=', $announcement->template_id)
                ->where('type', '=', 'sms')
                ->first();
            if ($sms_template) {
                $sms_message = $sms_template->body;
                $sms_response = null;
                $success = false;
                $phone = (trim(empty($customer->phone_2))) ? $customer->phone_1 : $customer->phone_1 . ',' . $customer->phone_2;

                $message = $this->replaceAnnouncementMarkup($this->customer_id, $sms_message);

                $success = $this->deliverSMS($phone, $message);

                $log = new AnnouncementLog();
                $log->customer_id = $customer->id;
                $log->sender_id = $this->auth_id;
                $log->template_id = $announcement->template_id;
                $log->announcement_id = $announcement->id;
                $log->title = 'SMS';
                $log->detail =  $message;
                $log->status =  $success;
                $log->message_id =  '';
                $log->date = date("Y-m-j h:m:s");
                $log->type = $announcement->announcement_type;
                if ($log->save()) {
                    $sent_log = DB::table('announcement_log')->where('announcement_id', '=', $this->announcement_id)
                        ->where('status', '=', 'sent')
                        ->count();
                    $progress = round(($sent_log / $this->total_announcement) * 100);
                    event(new AnnouncementSMSSendProgress($progress));
                }
            }
        }
    }
}
