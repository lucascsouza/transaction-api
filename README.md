# Transaction API

## Como instalar

Você pode utilizar este projeto com Container Docker montado no seguinte repositório: https://github.com/lucascsouza/docker-container

1. Clone o projeto seguindo as instruções do repositório Docker
2. Acesse o container da aplicação `docker exec -it application bash` e navegue até a pasta do projeto `cd transaction-api/`  
3. Instale o projeto executando `composer install`
4. Execute as migrations com `php artisan migrate`
5. Execute o seeder para criação automática de usuários ```php artisan db:seed``` 

## API
### POST - /new-transaction
Exemplo de Request:
```
{
    "payer_id": 1,
    "payee_id": 2,
    "value": 7.35
}
```

Exemplo de Response: 
```
{
    "status": "success",
    "message": "Transaction processed"
}
```

