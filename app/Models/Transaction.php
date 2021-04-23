<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'payer_id',
        'payee_id',
        'value',
        'notified'
    ];
}
