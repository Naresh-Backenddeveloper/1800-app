<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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

    public function verifyPassword(string $plainPassword): bool
    {
        return Hash::check($plainPassword, $this->password);
    }


    public function createPersonalAccessToken($name, $abilities = ['*'])
    {
        $token = Str::random(64);

        $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $token),
            'abilities' => json_encode($abilities),
        ]);

        return $token;
    }

    public function tokens()
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function createApiToken()
    {
        $token = $this->createToken('api_token', ['*']);
        return explode('|', $token->plainTextToken)[1];
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id')
            ->withTimestamps();
    }


    public function initiatedChats()
    {
        return $this->hasMany(Chat::class, 'user_id'); // as buyer
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id'); // my products
    }

    // Optional: all chats I'm involved in (buyer OR seller)
    public function allChats()
    {
        return Chat::where('user_id', $this->id)                     // I'm buyer
            ->orWhereIn('product_id', $this->products()->select('id')) // I'm seller
            ->with(['product.user', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->latest('updated_at');
    }
}
