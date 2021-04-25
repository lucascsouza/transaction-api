<?php


use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Services\UserService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TransactionTest extends TestCase
{

    private const VALID_PAYER_ID = 1;
    private const VALID_PAYEE_ID = 2;
    private const VALUE = 10.50;

    public function testTransaction()
    {
        $payer = User::factory()->make([
            'type' => 'comum',
            'wallet_balance' => 100,
        ]);
        $payee = User::factory()->make([
            'type' => 'lojista',
            'wallet_balance' => 50,
        ]);

        $userServiceMock = Mockery::mock(UserService::class);
        $userServiceMock
            ->shouldReceive('findUserById')
            ->twice()
            ->withArgs(function ($arg) {
                if ($arg === self::VALID_PAYER_ID || $arg === self::VALID_PAYEE_ID) {
                    return true;
                }
                return false;
            })
            ->andReturnUsing(function ($arg) use ($payer, $payee) {
                if ($arg === self::VALID_PAYER_ID) {
                    return $payer;
                }
                return $payee;
            });

        $userServiceMock
            ->shouldReceive('debitFromUserBalance')
            ->once();
        $userServiceMock
            ->shouldReceive('creditToUserBalance')
            ->once();

        $transaction = Transaction::factory()->make();
        $transactionServiceMock = Mockery::mock(TransactionService::class);
        $transactionServiceMock
            ->shouldReceive('createOne')
            ->andReturn($transaction);
        $transactionServiceMock
            ->shouldReceive('notifyUser')
            ->once();

        $this->app->instance(UserService::class, $userServiceMock);
        $this->app->instance(TransactionService::class, $transactionServiceMock);

        $this->post('/new-transaction', [
            'payer_id' => self::VALID_PAYER_ID,
            'payee_id' => self::VALID_PAYEE_ID,
            'value' => self::VALUE
        ]);

        $this->assertJson($this->response->getContent(), '{"status":"success","message":"Transaction processed"}');
    }

}
