<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfileImageHistory extends Model
{
    use SoftDeletes;
    protected $fillable = ['code', 'image_url', 'image_name', 'image_code'];

    public function userProfileImgHistoryListMap()
    {
        return [
            'image_history_code' => $this->image_code,
            'image_origin_name' => $this->image_name,
            'image_origin_url' => $this->image_url,
            'image_uploaded_at' => Carbon_HumanDateTime($this->created_at)
        ];
    }
}
