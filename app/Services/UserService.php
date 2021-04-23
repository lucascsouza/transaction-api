<?php


namespace App\Services;


use App\Mail\TransactionNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserService
{

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
     * @return User|null
     */
    public function findUserById(int $id): ?User
    {
        $query = $this->model->newQuery();
        return $query->find($id);
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
