<?php


namespace App\Services;


use App\Mail\TransactionNotification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class TransactionService
{

    /** @var  Transaction */
    private $model;

    /**
     * TransactionService constructor.
     * @param Transaction $model
     */
    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return Transaction
     */
    public function createOne(array $data): Transaction
    {
        $transaction = $this->model->newInstance();
        $transaction->fill($data);
        $transaction->save();

        return $transaction;
    }

    public function notifyUser(Transaction $transaction, User $payer, User $payee)
    {
        Mail::to($payee)->send(new TransactionNotification($transaction, $payer, $payee));
    }


}
