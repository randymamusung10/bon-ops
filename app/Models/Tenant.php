<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'subdomain',
        'status',
    ];

    /**
     * Booted function to auto-generate UUID.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get companies associated with this tenant.
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get branches associated with this tenant.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get users associated with this tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
