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
            'user_name' => User_getNameByCode($this->code),
            'user_img' => User_getImgProfileByCode($this->code),
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

    #relation
    public function user()
    {
        return $this->belongsTo(User::class, 'code', 'code');
    }
}
