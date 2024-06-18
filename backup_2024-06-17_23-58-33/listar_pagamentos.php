<?php
session_start();
require 'config/auth.php';
require 'config/db.php';
require 'config/functions.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Desfazer pagamento se a ação for post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagamento_id'])) {
    $pagamento_id = $_POST['pagamento_id'];
    $user_id = $_SESSION['user_id'];

    if (desfazerPagamento($pdo, $pagamento_id, $user_id)) {
        $message = "Pagamento desfeito com sucesso!";
    } else {
        $error = "Erro ao desfazer o pagamento.";
    }
}

// Configuração de paginação
$pagamentos_por_pagina = 20;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $pagamentos_por_pagina;

// Função para contar total de pagamentos
$total_pagamentos = contarPagamentos($pdo, $_SESSION['user_id']);

// Função para listar pagamentos com paginação
$pagamentos = listarPagamentosPaginados($pdo, $_SESSION['user_id'], $pagamentos_por_pagina, $offset);

// Calcular total de páginas
$total_paginas = ceil($total_pagamentos / $pagamentos_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Pagamentos</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Listar Pagamentos</h1>
        
        <?php if (isset($message)) { ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>
        
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Data de Pagamento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagamentos as $pagamento): ?>
                        <tr>
                            <td data-label="Cliente"><?php echo htmlspecialchars($pagamento['cliente_nome']); ?></td>
                            <td data-label="Valor">R$ <?php echo htmlspecialchars($pagamento['valor']); ?></td>
                            <td data-label="Data de Pagamento"><?php echo date('d/m/Y', strtotime($pagamento['data_pagamento'])); ?></td>
                            <td data-label="Ações">
                                <form action="listar_pagamentos.php" method="post" style="display:inline;">
                                    <input type="hidden" name="pagamento_id" value="<?php echo $pagamento['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Desfazer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Navegação de Paginação -->
        <nav aria-label="Navegação de página">
            <ul class="pagination justify-content-center">
                <?php if ($pagina_atual > 1): ?>
                    <li class="page-item"><a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?>">Anterior</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php if ($pagina_atual == $i) echo 'active'; ?>"><a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <li class="page-item"><a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?>">Próximo</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
