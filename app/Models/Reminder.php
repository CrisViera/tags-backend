<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use DateTimeInterface;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','description','remind_at','notified'];

    protected $casts = ['remind_at' => 'datetime'];

    // Forzar formato ISO 8601 al serializar JSON
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(DATE_RFC3339_EXTENDED);
    }
}
