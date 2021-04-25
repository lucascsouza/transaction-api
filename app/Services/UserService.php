<?php


namespace App\Services;


use App\Mail\TransactionNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class UserService
{

    private const ID_FIELD = 'id';

    /** @var User */
    private $model;

    /**
     * UserService constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findUserById(int $id)
    {
        $query = $this->model->newQuery();
        return $query
            ->where(self::ID_FIELD, $id)
            ->first();
    }

    /**
     * @param User $payer
     * @param float $value
     */
    public function creditToUserBalance(User $payer, float $value): void
    {
        $payer->wallet_balance += $value;
        $payer->save();
    }

    /**
     * @param User $payee
     * @param float $value
     */
    public function debitFromUserBalance(User $payee, float $value): void
    {
        $payee->wallet_balance -= $value;
        $payee->save();
    }

}
