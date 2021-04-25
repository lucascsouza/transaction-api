<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Queue;

class NotifyUserJob extends Job
{

    private const SUCCESS_MESSAGE = 'Enviado';

    /** @var Transaction */
    private $transaction;

    /** @var User */
    private $payer;

    /** @var User */
    private $payee;

    /** @var TransactionService */
    protected $transactionService;

    /**
     * NotifyUserJob constructor.
     * @param Transaction $transaction
     * @param User $payer
     * @param User $payee
     * @param TransactionService $transactionService
     */
    public function __construct(
        Transaction $transaction,
        User $payer,
        User $payee,
        TransactionService $transactionService
    ){
        $this->transaction = $transaction;
        $this->payer = $payer;
        $this->payee = $payee;
        $this->transactionService = $transactionService;
    }

    /**
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $endpoint = config('app.notification_endpoint');
        $client = new Client();
        $request = $client->get($endpoint);
        $result = json_decode($request->getBody()->getContents());

        if($result->message !== self::SUCCESS_MESSAGE) {
            Queue::later(
                Carbon::now()->addMinutes(10),
                new self($this->transaction, $this->payer, $this->payee, $this->transactionService)
            );
            return;
        }

        $this->transactionService->updateOne(
            [
                'notified' => true,
                'notified_at' => new Datetime()
            ],
            $this->transaction
        );

    }
}
