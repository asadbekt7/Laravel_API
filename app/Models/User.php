<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Auth\MyUwedClaims;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'staff_id',
        'full_name',
    ];

    protected ?MyUwedClaims $ssoClaims = null;

    public static function syncFromSso(MyUwedClaims $claims): self
    {
        $user = static::firstOrNew(['staff_id' => $claims->staffId]);

        $user->full_name = $claims->fullName;
        $user->name = $claims->fullName;
        $user->email = $claims->email;

        $user->save();

        return $user->withSsoClaims($claims);
    }

    public function withSsoClaims(MyUwedClaims $claims): self
    {
        $this->ssoClaims = $claims;

        return $this;
    }

    public function ssoClaims(): ?MyUwedClaims
    {
        return $this->ssoClaims;
    }

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
}
