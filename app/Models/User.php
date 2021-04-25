<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    private $id;
    private $fullname;
    private $cpf_cnpj;
    private $type;
    private $email;
    private $wallet_balance;

    protected $fillable = [
        'fullname',
        'email',
        'cpf_cnpj',
        'type',
        'wallet_balance'
    ];
    protected $hidden = [
        'password',
    ];

}
