<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');
include('config/funcoes_revendedores.php');

// Configuração de paginação
$vendas_por_pagina = 25;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $vendas_por_pagina;

// Obter mês e ano atuais
$mes_atual = date('m');
$ano_atual = date('Y');

// Função para contar total de vendas
$total_vendas = contarVendas($pdo, $_SESSION['user_id']);

// Função para listar vendas por mês com nomes e lucro
$vendas = listarVendasPorMes($pdo, $mes_atual, $ano_atual);

// Função para agrupar dados para os gráficos
$dadosAgrupados = agruparVendasPorRevendedorEServidor($pdo);

// Calcular total de páginas
$total_paginas = ceil($total_vendas / $vendas_por_pagina);

// Total de receitas, despesas e saldo
$total_receitas = totalReceitas($pdo, $_SESSION['user_id'], $mes_atual, $ano_atual);
$total_despesas = totalDespesas($pdo, $_SESSION['user_id'], $mes_atual, $ano_atual);
$saldo = $total_receitas - $total_despesas;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Vendas de Créditos</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <h1 class="my-4">Resumo Financeiro</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Receitas</div>
                <div class="card-body">
                    <h5 class="card-title">R$ <?= number_format($total_receitas, 2, ',', '.') ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Despesas</div>
                <div class="card-body">
                    <h5 class="card-title">R$ <?= number_format($total_despesas, 2, ',', '.') ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Saldo</div>
                <div class="card-body">
                    <h5 class="card-title">R$ <?= number_format($saldo, 2, ',', '.') ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <h2 class="my-4">Gráficos</h2>
    <div class="row">
        <div class="col-md-6">
            <canvas id="graficoRevendedores"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="graficoServidores"></canvas>
        </div>
    </div>

    <!-- Lista de Vendas -->
    <h2 class="my-4">Lista de Vendas</h2>
    <table class="table table-striped table-custom">
        <thead>
            <tr>
                <th>Data</th>
                <th>Revendedor</th>
                <th>Servidor</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Valor Total</th>
                <th>Lucro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($vendas)): ?>
            <?php foreach ($vendas as $venda): ?>
                <tr>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($venda['data_venda']))) ?></td>
                    <td><?= htmlspecialchars($venda['revendedor_nome']) ?></td>
                    <td><?= htmlspecialchars($venda['servidor_nome']) ?></td>
                    <td><?= htmlspecialchars($venda['quantidade']) ?></td>
                    <td>R$ <?= number_format($venda['preco_credito'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($venda['lucro'], 2, ',', '.') ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="desfazerVenda(<?= $venda['id'] ?>)">Desfazer</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Nenhuma venda encontrada.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginação -->
    <nav aria-label="Navegação de página">
        <ul class="pagination justify-content-center">
            <?php if ($pagina_atual > 1): ?>
                <li class="page-item"><a class="page-link" href="?pagina=<?= $pagina_atual - 1 ?>">Anterior</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= $pagina_atual == $i ? 'active' : '' ?>"><a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>

            <?php if ($pagina_atual < $total_paginas): ?>
                <li class="page-item"><a class="page-link" href="?pagina=<?= $pagina_atual + 1 ?>">Próximo</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Scripts para os gráficos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctxRevendedores = document.getElementById('graficoRevendedores').getContext('2d');
    var ctxServidores = document.getElementById('graficoServidores').getContext('2d');

    var nomesRevendedores = [];
    var valoresVendasRevendedores = [];
    var nomesServidores = [];
    var valoresVendasServidores = [];

    <?php foreach ($dadosAgrupados as $dado): ?>
        nomesRevendedores.push("<?= $dado['revendedor_nome'] ?>");
        valoresVendasRevendedores.push(<?= $dado['total_vendas'] ?>);
        nomesServidores.push("<?= $dado['servidor_nome'] ?>");
        valoresVendasServidores.push(<?= $dado['total_vendas'] ?>);
    <?php endforeach; ?>

    var dataRevendedores = {
        labels: nomesRevendedores,
        datasets: [{
            label: 'Vendas por Revendedor',
            data: valoresVendasRevendedores,
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
        }]
    };

    var dataServidores = {
        labels: nomesServidores,
        datasets: [{
            label: 'Vendas por Servidor',
            data: valoresVendasServidores,
            backgroundColor: 'rgba(255, 99, 132, 0.6)'
        }]
    };

    var graficoRevendedores = new Chart(ctxRevendedores, {
        type: 'bar',
        data: dataRevendedores,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': R$ ' + tooltipItem.formattedValue;
                        }
                    }
                }
            }
        }
    });

    var graficoServidores = new Chart(ctxServidores, {
        type: 'bar',
        data: dataServidores,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': R$ ' + tooltipItem.formattedValue;
                        }
                    }
                }
            }
        }
    });
});

function desfazerVenda(id) {
    if (confirm('Tem certeza que deseja desfazer esta venda?')) {
        window.location.href = 'listar_vendas_credito.php?acao=desfazer&id=' + id;
    }
}
</script>
</body>
</html>

<?php
// Tratamento de ações
if (isset($_GET['acao']) && $_GET['acao'] == 'desfazer' && isset($_GET['id'])) {
    $id = $_GET['id'];
    desfazerVenda($pdo, $id);
    header('Location: listar_vendas_credito.php');
    exit;
}
?>