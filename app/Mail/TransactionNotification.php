<?php


namespace App\Mail;


use App\Models\Transaction;
use App\Models\User;
use Illuminate\Mail\Mailable;

class TransactionNotification extends Mailable
{
    /** @var Transaction */
    protected $transaction;

    /** @var User */
    protected $payer;

    /** @var User */
    protected $payee;

    /**
     * TransactionNotification constructor.
     * @param Transaction $transaction
     * @param User $payer
     * @param User $payee
     */
    public function __construct(Transaction $transaction, User $payer, User $payee)
    {
        $this->transaction = $transaction;
        $this->payer = $payer;
        $this->payee = $payee;
    }

    public function build()
    {
        $this->markdown('transaction-notification', [
            'transaction' => $this->transaction,
            'payer' => $this->payer,
            'payee' => $this->payee
        ]);
    }
}
