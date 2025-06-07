# User Processor API

API RESTful para processamento de dados de usuários com validação, cache, integração com serviços externos e processamento assíncrono.

## 🚀 Tecnologias Utilizadas

- Laravel 12.x
- PHP 8.4
- MySQL
- Redis
- Laravel Sail (Docker)
- Laravel Horizon
- Laravel DomPDF
- L5-Swagger (OpenAPI)

## ✅ Funcionalidades Implementadas

### Endpoints RESTful
- `POST /api/v1/users/process` - Processa dados de usuário (CPF, CEP, e-mail)
- `GET /api/v1/users/{cpf}` - Consulta dados do usuário

### Validação e Cache
- Validação robusta via Form Request
- Cache Redis com tags e TTL de 24h
- Tratamento estruturado de erros (HTTP 422)

### Integração com APIs Externas
- ViaCEP (endereços)
- Nationalize (dados demográficos)
- Mock de status de CPF
- Retry automático (3 tentativas com backoff)
- Timeouts configuráveis

### Observabilidade
- Middleware de logging com correlation_id
- Logs estruturados em JSON
- Monitoramento de chamadas externas e cache

### Persistência e Processamento
- Repository Pattern + Eloquent
- Migrations com constraints
- Processamento assíncrono via filas
- Análise de risco e geração de relatórios PDF
- Simulação de envio de e-mails

### Monitoramento
- Laravel Horizon para filas
- Métricas e balanceamento
- Supervisores configurados

### Documentação
- OpenAPI/Swagger
- UI disponível em `/api/documentation`

## 🔧 Como Rodar o Projeto

1. Clone o repositório
```bash
git clone [URL_DO_REPOSITÓRIO]
cd [NOME_DO_PROJETO]
```

2. Instale as dependências com Composer (usando Docker)
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

3. Configure o ambiente
```bash
cp .env.example .env
```

4. Inicie os containers
```bash
./vendor/bin/sail up -d
```

5. Execute as migrations e seeds
```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

6. Inicie o Horizon para monitoramento de filas
```bash
./vendor/bin/sail artisan horizon
```

## 🧪 Testes

O projeto inclui testes unitários e de feature:

```bash
./vendor/bin/sail artisan test
```

Testes implementados:
- ExternalApiServiceTest (Unit)
- UserProcessTest (Feature)
- Cobertura de validação, cache e fluxo completo

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── UserProcessController.php
│   │   └── Requests/
│   │       └── ProcessUserRequest.php
│   ├── Jobs/
│   │   └── AnalyzeUserJob.php
│   ├── Models/
│   │   └── UserRecord.php
│   ├── Repositories/
│   │   └── UserRepository.php
│   └── Services/
│       └── ExternalApiService.php
├── config/
│   ├── horizon.php
│   └── cache.php
├── database/
│   └── migrations/
├── storage/
│   └── app/
│       └── reports/
└── tests/
    ├── Unit/
    │   └── ExternalApiServiceTest.php
    └── Feature/
        └── UserProcessTest.php
```

## 📝 Observações Importantes

- Cache Redis configurado com TTL de 24h
- Relatórios PDF são armazenados em `storage/app/reports`
- Logs estruturados em JSON para melhor observabilidade
- Timeouts e retries configurados para chamadas externas
- Documentação OpenAPI disponível em `/api/documentation`

## 🔍 Monitoramento

Para monitorar as filas e jobs:

```bash
./vendor/bin/sail artisan horizon
```

Acesse o dashboard do Horizon em: `http://localhost/horizon`

## 📚 Documentação da API

A documentação completa da API está disponível em:
`http://localhost/api/documentation`
![relatorio pdf](https://github.com/user-attachments/assets/a2e0c26c-871e-4af1-b321-458758825559)
![docuser](https://github.com/user-attachments/assets/c57687f0-830b-45e4-8eda-073fa980f3ad)
![docuser2](https://github.com/user-attachments/assets/715f3b55-3b7e-44be-9aec-3c570cbd0e5e)
![docuser3](https://github.com/user-attachments/assets/5ce29bbd-9dc1-491d-9924-3349a980a587)
![testes1](https://github.com/user-attachments/assets/02ebc7d5-1bf5-4562-8c71-44eefcc2bc07)
![horizon2](https://github.com/user-attachments/assets/16d9762a-5fd4-4096-9ce6-698c2112949e)
![horizon](https://github.com/user-attachments/assets/6e3acdbd-870e-44b4-b6c2-6b56a35bef18)



## 👨‍💻 Autor![Uploading testes1.png…]()


[Tiago Kochem]
- 📧 [tiagok989@gmail.com]
- 🔗 [LinkedIn](https://linkedin.com/in/tiagokochem) | [GitHub](https://github.com/tiagokochem) 
