<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Llave extends Model
{
    use HasFactory;


    // Estados de entrega de la llave
    const ESTADO_NO_ENTREGADA = 0;
    const ESTADO_ENTREGADA = 1;


    protected $fillable = [
        'nombre',
        'estado',
        'condicion'
    ];


    public function recinto()
    {
        return $this->hasMany(Recinto::class, 'llave_id');
    }


    protected $table = 'llave';


    /**
     * Obtener el estado de entrega en formato legible
     */
    public function getEstadoEntregaTextAttribute()
    {
        switch ($this->estado) {
            case self::ESTADO_NO_ENTREGADA: // 0
                return 'Solicitar';
            case self::ESTADO_ENTREGADA: // 1
                return 'No Entregada';
            default:
                return 'Desconocido';
        }
    }


    /**
     * Obtener la clase CSS para el badge del estado
     */
    public function getEstadoBadgeClassAttribute()
    {
        switch ($this->estado) {
            case self::ESTADO_NO_ENTREGADA:
                return 'bg-success';
            case self::ESTADO_ENTREGADA:
                return 'bg-warning';
            default:
                return 'bg-secondary';
        }
    }


    /**
     * Verificar si la llave está disponible para entregar
     */
    public function estaDisponible()
    {
        return $this->estado === self::ESTADO_NO_ENTREGADA && $this->condicion == 1;
    }


    /**
     * Verificar si la llave está entregada
     */
    public function estaEntregada()
    {
        return $this->estado === self::ESTADO_ENTREGADA;
    }


    /**
     * Marcar llave como entregada
     */
    public function marcarComoEntregada()
    {
        $this->estado = self::ESTADO_ENTREGADA;
        $this->save();
    }


    /**
     * Marcar llave como devuelta (no entregada)
     */
    public function marcarComoDevuelta()
    {
        $this->estado = self::ESTADO_NO_ENTREGADA;
        $this->save();
    }


}




