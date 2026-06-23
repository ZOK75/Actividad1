<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'google2fa_secret' => 'encrypted',
    ];

    protected static function booted()
{
    // 🟢 Rastrear cuando se CREA un registro (Alteración por inserción)
    static::created(function ($model) {
        \Illuminate\Support\Facades\Log::channel('desarrollo')->info('[DATABASE_INSERT] Nuevo registro creado en la tabla: ' . $model->getTable(), [
            'id_afectado' => $model->id,
            'datos_nuevos' => $model->getAttributes(), // Guarda todos los campos creados
            'url_actividad' => request()->fullUrl(),
            'ip' => request()->ip()
        ]);
    });

    // 🟡 Rastrear cuando se ACTUALIZA un registro (Alteración por edición)
    static::updated(function ($model) {
        \Illuminate\Support\Facades\Log::channel('desarrollo')->info('[DATABASE_UPDATE] Registro modificado en la tabla: ' . $model->getTable(), [
            'id_afectado' => $model->id,
            'antes' => $model->getOriginal(), // Cómo estaban los datos antes
            'despues' => $model->getChanges(),  // Qué campos cambiaron exactamente
            'ip' => request()->ip()
        ]);
    });

    // 🔴 Rastrear cuando se ELIMINA un registro (Alteración por borrado)
    static::deleted(function ($model) {
        \Illuminate\Support\Facades\Log::channel('desarrollo')->warning('[DATABASE_DELETE] Registro eliminado en la tabla: ' . $model->getTable(), [
            'id_afectado' => $model->id,
            'datos_viejos' => $model->getOriginal(),
            'ip' => request()->ip()
        ]);
    });
    }

    public $incrementing = false;
    protected $keyType = 'string';
}
