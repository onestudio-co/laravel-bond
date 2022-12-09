<?php

namespace App\Models;

use Based\Fluent\Relations\Relation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SocialLogin extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public int $id;

    public int $user_id;

    public string $provider;

    public string $provider_id;

    public string $email;

    public string $last_token;

    public Carbon $created_at;

    public ?Carbon $updated_at = null;

    #[Relation]
    public User $user;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
