<?php

namespace App\Models;

use App\Utilities\Overrider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail {
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'user_type', 'status', 'profile_picture',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeActive($query) {
        return $query->where('status', 1);
    }

    public function scopeStaff($query) {
        return $query->where('user_type', 'admin')->orWhere('user_type', 'user');
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id')->withDefault(['name' => _lang('Admin')]);
    }

    public function employee() {
        return $this->hasOne(Employee::class, 'user_id')->withDefault();
    }

    public function sentMessages() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages() {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    protected function createdAt(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

    public function sendEmailVerificationNotification() {
        if (get_option('email_verification') == 0) {
            return;
        }
        Overrider::load("Settings");
        $this->notify(new VerifyEmail);
    }

}
