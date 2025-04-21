<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * U-000001 形式の新しいユーザーIDを生成
     *
     * @return string
     */
    public static function generateNewUserId(): string
    {
        $lastUser = self::select('user_code')
            ->where('user_code', 'like', 'U-%')
            ->orderBy('user_code', 'desc')
            ->withTrashed()
            ->first();
        if (empty($lastUser)) {
            $lastNumber = 0;
        } else {
            $lastNumber = (int) substr($lastUser->user_code, 2); // "U-" を除く
        }

        $newNumber = $lastNumber + 1;

        return 'U-' . str_pad((string) $newNumber, 6, '0', STR_PAD_LEFT);
    }
}
