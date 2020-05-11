<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    protected $fillable = ['code', 'log_code', 'ipaddr', 'info'];

    public function userLoginHistorySimpleMap()
    {
        $log = $this->logDeviceInfo();
        return [
            'log_code' => $this->log_code,
            'device_type' => $log["devType"],
            'device_name' => "{$log["osName"]} - {$log["osVersion"]}",
            'ip_address' => $this->ipaddr,
            'date_time' => Carbon_diffForHumans($this->created_at)
        ];
    }

    public function userLoginHistoryFullMap()
    {
        $log = $this->logDeviceInfo();
        return [
            'log_code' => $this->log_code,
            'device_info' => $log,
            'ip_address' => $this->ipaddr,
            'date_time' => Carbon_HumanFullDateTime($this->created_at) 
        ];
    }

    # Private function
    private function logDeviceInfo()
    {
        $deviceLog = unserialize($this->info);
        return $deviceLog;
    }
}
