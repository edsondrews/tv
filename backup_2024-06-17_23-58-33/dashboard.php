<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Funções para contagens e valores financeiros
$clientesAtivos = contarClientesAtivos($pdo, $_SESSION['user_id']);
$clientesVencidos = contarClientesVencidos($pdo, $_SESSION['user_id']);
$faturamentoMensal = calcularFaturamentoMensal($pdo, $_SESSION['user_id']);
$projecaoMensal = calcularProjecaoMensal($pdo, $_SESSION['user_id']);
$recebidosHoje = calcularRecebidosHoje($pdo, $_SESSION['user_id']);
$totalClientes = contarTotalClientes($pdo, $_SESSION['user_id']);

$clientes = listarClientes($pdo, $_SESSION['user_id']);
//$servidores = listarServidores($pdo, $_SESSION['user_id']);
$planos = listarPlanos($pdo, $_SESSION['user_id']);
$formasPagamento = listarFormasPagamento($pdo, $_SESSION['user_id']);
$dispositivos = listarDispositivos($pdo, $_SESSION['user_id']);
$formasCaptacao = listarFormasCaptacao($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/dashboard.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Bem-vindo, <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>!</h1>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="clientesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Clientes</a>
                        <div class="dropdown-menu" aria-labelledby="clientesDropdown">
                            <a class="dropdown-item" href="adicionar_cliente.php">Adicionar Cliente</a>
                            <a class="dropdown-item" href="listar_clientes.php">Listar Clientes</a>
                            <a class="dropdown-item" href="listar_pagamentos.php">Listar Pagamentos</a>
                            <a class="dropdown-item" href="revendedores.php">Cadastrar Revendedor</a>
                             <a class="dropdown-item" href="vendas_creditos.php">Venda de Crédito</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="servidoresDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Servidores</a>
                        <div class="dropdown-menu" aria-labelledby="servidoresDropdown">
                            <a class="dropdown-item" href="adicionar_servidor.php">Adicionar Servidor</a>
                            <a class="dropdown-item" href="listar_servidores.php">Listar Servidores</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="planosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Planos</a>
                        <div class="dropdown-menu" aria-labelledby="planosDropdown">
                            <a class="dropdown-item" href="adicionar_plano.php">Adicionar Plano</a>
                            <a class="dropdown-item" href="listar_planos.php">Listar Planos</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pagamentosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pagamentos</a>
                        <div class="dropdown-menu" aria-labelledby="pagamentosDropdown">
                            <a class="dropdown-item" href="adicionar_forma_pagamento.php">Adicionar Forma de Pagamento</a>
                            <a class="dropdown-item" href="listar_formas_pagamento.php">Listar Formas de Pagamento</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dispositivosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dispositivos</a>
                        <div class="dropdown-menu" aria-labelledby="dispositivosDropdown">
                            <a class="dropdown-item" href="adicionar_dispositivo.php">Adicionar Dispositivo</a>
                            <a class="dropdown-item" href="listar_dispositivos.php">Listar Dispositivos</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="aplicativosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aplicativos</a>
                        <div class="dropdown-menu" aria-labelledby="aplicativosDropdown">
                            <a class="dropdown-item" href="adicionar_aplicativo.php">Adicionar Aplicativo</a>
                            <a class="dropdown-item" href="listar_aplicativos.php">Listar Aplicativos</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="captacaoDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Captação</a>
                        <div class="dropdown-menu" aria-labelledby="captacaoDropdown">
                            <a class="dropdown-item" href="adicionar_forma_captacao.php">Adicionar Forma de Captação</a>
                            <a class="dropdown-item" href="listar_formas_captacao.php">Listar Formas de Captação</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>

        <div class="dashboard-section">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ativos</h5>
                    <p class="card-text"><?php echo $clientesAtivos; ?> Clientes</p>
                </div>
            </div>
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Vencidos</h5>
                    <p class="card-text"><?php echo $clientesVencidos; ?> Clientes</p>
                </div>
            </div>
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Faturamento Mensal</h5>
                    <p class="card-text">R$ <?php echo $faturamentoMensal; ?></p>
                </div>
            </div>
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Projeção Mensal</h5>
                    <p class="card-text">R$ <?php echo $projecaoMensal; ?></p>
                </div>
            </div>
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Recebidos Hoje</h5>
                    <p class="card-text">R$ <?php echo $recebidosHoje; ?></p>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h2>Clientes</h2>
            <ul class="list-group">
                <?php foreach ($clientes as $cliente): ?>
                    <li class="list-group-item"><?php echo htmlspecialchars($cliente['nome']); ?> (<?php echo htmlspecialchars($cliente['usuario']); ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
