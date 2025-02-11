<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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

   //metodo para obtener todas las ideas del user, 1 usuario tiene muchas idea
    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::Class);
    }

    //el usuario tiene muchas ideas like
    public function ideasLiked(): BelongsToMany{
        return $this->belongsToMany(Idea::class);
    }

    public function iLikeIt($ideaId): bool{
        //this es este usuario, metodo ideas like, donde idea sea igual al ideid del paramatro, existe la relacion?
        return $this->ideasLiked()->where('idea_id',$ideaId)->exists();
    }
}
