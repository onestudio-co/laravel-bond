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

    private int $id;

    protected int $user_id;

    protected string $provider;

    protected string $provider_id;

    protected string $email;

    protected string $last_token;

    protected Carbon $created_at;

    protected ?Carbon $updated_at;

    #[Relation]
    public User $user;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
