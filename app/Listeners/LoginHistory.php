<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserLoginHistory;
use ApiHelper;

class LoginHistory
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  LoginEvent  $event
     * @return void
     */
    public function handle(LoginEvent $event)
    {
        $data = $event->data;

        // create login history
        $status = UserLoginHistory::create($data);
        
        // generate notification
        $msg = "User Login from".$data['location'].". Current ip : ".$data['user_ip'];
        $gendata = [ "user_id" => $data['user_id'], "type" => 1, "title" => "Login Activity", "msg" => $msg ];
        $res = ApiHelper::generate_notification($gendata);

        return $res;
    }
}
