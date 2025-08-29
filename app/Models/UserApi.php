<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class UserApi extends SanctumToken
{
    protected $table = 'user_apis'; 

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set user_code automatically from tokenable user
            if (empty($model->user_code) && isset($model->tokenable)) { 
                $model->user_code = $model->tokenable->user_code; // getting the user_code with the help of tokenable column
            }
        });
    }
}

