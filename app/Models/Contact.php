<?php

namespace App\Models;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'profile_image_path',
        'other_doc',
        'status'
    ];

    public function getProfileImageUrlAttribute()
    {
        return CommonHelper::getFullPath($this->profile_image_path) ?? asset('images/no-image.jpg');
    }

    public function customFieldValues()
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    public static function fillableFields()
    {
        return (new static)->getFillable();
    }


    public function mergedContact()
    {
        return $this->hasOne(Contact::class, 'merged_into');
    }

}
