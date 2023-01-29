# LaraBooks

## Como iniciar a aplicação?

- Conecte uma instância do Redis ao projeto. Caso queira utilizar o docker: `docker run --name redis -p 6379:6379 -d redis`.
- Crie uma projeto no [Firebase](https://console.firebase.google.com/u/0/) e crie uma chave de conta de serviço. Essa chave será um arquivo `JSON`.
- Coloque essa chave no diretório raiz do projeto. Caso queira utilizar o mesmo nome já configurado na `.env` (`FIREBASE_CREDENTIALS="firebase-credentials.json"`), renomeie o arquivo. Caso contrário, edite o `.env`.
- Caso já não tenha instalado, instale a [extensão](https://cloud.google.com/php/grpc?hl=pt-br) `grpc` para o PHP.
- Inicie o projeto utilizando `php artisan serve`

## Como rodar os testes?

- Siga o passo a passo de como iniciar a aplicação, menos último ponto. Todas as configurações feitas no arquivo `.env` agora precisam ser feitas no `.env.test`.
- Rode todos os testes executando `php artisan test`.

## Como acessar a documentação?

Instale o Insomnia e importe o arquivo da raiz do projeto que inicia com `Insomnia-*.json`.
