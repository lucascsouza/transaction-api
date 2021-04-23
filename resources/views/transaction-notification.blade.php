

@component('mail::message')
    <h1>Olá, {{ $payee->fullname }}!</h1>
    <p>Você recebeu uma transação de {{ $payer->fullname }} no valor de R$ {{ $transaction->value }}.</p>

    Obrigado, {{ config('app.name') }}
@endcomponent
