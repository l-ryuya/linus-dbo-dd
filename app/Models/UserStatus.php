<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    /** @use HasFactory<\Database\Factories\UserStatus> */
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'status';
    protected $keyType = 'string';
    public $timestamps = false;
}
