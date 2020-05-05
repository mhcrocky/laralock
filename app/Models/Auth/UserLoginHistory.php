<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    protected $fillable = ['code', 'ipaddr', 'info'];

    public function userLoginHistorySimpleMap()
    {
        $log = $this->logDeviceInfo();
        return [
            'device' => "{$log["osName"]} - {$log["osVersion"]}",
            'ip-address' => $this->ipaddr,
            'date-time' => Carbon_HumanFullDateTime($this->created_at)
        ];
    }

    # Private function
    private function logDeviceInfo()
    {
        $deviceLog = unserialize($this->info);
        return $deviceLog;
    }
}
