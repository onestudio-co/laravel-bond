<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class NotificationToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    private int $id;

    protected int $user_id;

    protected string $token;

    protected string $device_id;

    protected string $device_type;

    protected Carbon $created_at;

    protected ?Carbon $updated_at;
}
