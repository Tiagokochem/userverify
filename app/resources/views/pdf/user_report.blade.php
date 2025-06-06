<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Risco – {{ $cpf }}</title>
    <style> /* estilos simples para PDF */ </style>
</head>
<body>
    <h1>Relatório de Processamento</h1>
    <p><strong>CPF:</strong> {{ $cpf }}</p>
    <p><strong>Risco:</strong> {{ $risk }}</p>

    <h2>Dados de Endereço</h2>
    <p>
        {{ $data['viacep']['logradouro'] }},
        {{ $data['viacep']['bairro'] }} –
        {{ $data['viacep']['localidade'] }}/{{ $data['viacep']['uf'] }}
    </p>

    <h2>Probabilidades de Nacionalidade</h2>
    <ul>
    @foreach ($data['nationalize']['country'] as $c)
        <li>{{ $c['country_id'] }}: {{ round($c['probability'] * 100, 2) }}%</li>
    @endforeach
    </ul>

    <h2>Status do CPF</h2>
    <p>{{ $data['cpf_status']['status'] }}</p>
</body>
</html>
