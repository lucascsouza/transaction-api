<?php


namespace App\Http\Controllers;


use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Services\UserService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{

    private const VALUE_FIELD = 'value';
    private const PAYER_ID_FIELD = 'payer_id';
    private const PAYEE_ID_FIELD = 'payee_id';

    private const USER_TYPE_ORDINARY = 'comum';

    /** @var UserService */
    private $userService;

    /** @var TransactionService */
    private $transactionService;

    /**
     * TransactionController constructor.
     * @param TransactionService $transactionService
     */
    public function __construct(UserService $userService, TransactionService $transactionService)
    {
        $this->userService = $userService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function new(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Validate required fields and if the user it's the same for payer and payee
            $this->validate($request, [
                'payer_id' => 'required',
                'payee_id' => 'required|different:payer_id',
                'value' => 'required|numeric|min:0'
            ]);

            // Check if payer and payee exists
            $value = $request->get(self::VALUE_FIELD);
            /** @var User $payer */
            $payer = $this->userService->findUserById($request->get(self::PAYER_ID_FIELD));
            /** @var User $payee */
            $payee =  $this->userService->findUserById($request->get(self::PAYEE_ID_FIELD));

            // Check if payer type equals to 'comum'
            if ($payer->type !== self::USER_TYPE_ORDINARY) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only ordinary users can transfer money to another user, the informed user is a shopkeeper'
                ]);
            }

            // Validate if the user has balance for the transaction
            if ($value > $payer->wallet_balance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The payer doens\'t have enough funds to this transaction'
                ]);
            }

            // Create the transaction
            $transaction = $this->transactionService->createOne([
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'value' => $value
            ]);

            // Recalculate payer's balance
            $this->userService->debitFromUserBalance($payer, $value);

            // Recalculate payee's balance
            $this->userService->creditToUserBalance($payee, $value);

            // Check authorization
            $client = new Client();
            $request = $client->request('GET', config('app.authorization_endpoint'));
            $authorization = json_decode($request->getBody()->getContents());
            if ($authorization->message !== 'Autorizado') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'The transaction wasn\'t authorized.'
                ]);
            }

            DB::commit();

            // Notify
            $this->transactionService->notifyUser($transaction, $payer, $payee);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction processed'
            ]);

        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => sprintf('Users not found: %s', implode(",", $exception->getIds()))
            ]);
        } catch (GuzzleException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while trying to authorize the transaction, please try again.'
            ]);
        }
    }

}
