<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public ?Carbon $updated_at = null;
}
