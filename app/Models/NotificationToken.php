<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class NotificationToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    public int $id;

    public int $user_id;

    public string $token;

    public string $device_id;

    public string $device_type;

    public Carbon $created_at;

    protected ?Carbon $updated_at = null;
}
