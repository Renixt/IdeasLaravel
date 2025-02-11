<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;


class Idea extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'description','likes'];
  //  protected $casts = ['created_at' => 'dateTime'];
    //1 idea pertenece a un usuario
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    //relacion inversa, una idea le puede gustar a muchos usuarios(users liked)
    public function users(): BelongsToMany{
        return $this->belongsToMany(User::class);
    }

    //el qiery agrega al query principal
    public function scopeMyIdeas(Builder $query, $filter)
    {
        //si filter esta seteado y no esta vacio...
        if (!empty($filter) && $filter == 'mis-ideas'){
            //se regresa la query donde el user id, es igual al usuario autenticado(loggeado)
         return   $query->where('user_id', auth()->id());
        }
    }

    public function scopeTheBest(Builder $query, $filter)
    {
        if (!empty($filter) && $filter == 'las-mejores'){
            //se regresa la query donde el user id, es igual al usuario autenticado(loggeado)
         return   $query->orderBy('likes','desc');
        }
    }
    //change for git
}
