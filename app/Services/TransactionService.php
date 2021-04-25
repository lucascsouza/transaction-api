<?php


namespace App\Services;


use App\Jobs\NotifyUserJob;
use App\Mail\TransactionNotification;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

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
     *
     * @return Transaction
     */
    public function createOne(array $data): Transaction
    {
        $transaction = $this->model->newInstance();
        $transaction->fill($data);
        $transaction->save();

        return $transaction;
    }

    /**
     * @param array $data
     * @param Transaction $transaction
     *
     * @return Transaction
     */
    public function updateOne(array $data, Transaction $transaction): Transaction
    {
        $transaction
            ->fill($data)
            ->save();

        return $transaction;

    }

    /**
     * @param Transaction $transaction
     * @param User $payer
     * @param User $payee
     */
    public function notifyUser(Transaction $transaction, User $payer, User $payee): void
    {
        Queue::push(new NotifyUserJob($transaction, $payer, $payee, $this));
    }


}
