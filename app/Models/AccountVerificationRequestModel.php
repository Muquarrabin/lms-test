<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AccountVerificationRequestModel extends Model
{
    use HasFactory;

    protected $table = "account_verification_requests";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_id",
        "email",
        "token",
        "status"
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
