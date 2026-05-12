<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoDocumento extends Model
{
    protected $table = 'estados_documento';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'estado_id', 'id');
    }
}