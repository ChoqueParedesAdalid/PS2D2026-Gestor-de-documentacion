<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    protected $table = 'observaciones';
    
    protected $fillable = [
        'documento_id',
        'revisor_id',
        'comentario',
        'resuelta',
        'fecha_revision'
    ];
    
    protected $casts = [
        'resuelta' => 'boolean',
        'fecha_revision' => 'datetime',
    ];
    
    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }
    
    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
}