<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

// Configuração de paginação
$clientes_por_pagina = 25;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $clientes_por_pagina;

// Função para contar total de clientes
$total_clientes = contarClientes($pdo, $_SESSION['user_id']);

// Função para listar clientes com paginação
$clientes = listarClientesPaginados($pdo, $_SESSION['user_id'], $clientes_por_pagina, $offset);

// Calcular total de páginas
$total_paginas = ceil($total_clientes / $clientes_por_pagina);

// Verificar se um cliente foi renovado ou excluído
$cliente_renovado = isset($_GET['renovado']);
$cliente_excluido = isset($_GET['excluido']);
$erro = '';

// Processar ações de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['renovar'])) {
        $cliente_id = $_POST['cliente_id'];
        $periodo = $_POST['periodo'];
        if (renovarCliente($pdo, $cliente_id, $_SESSION['user_id'], $periodo)) {
            header('Location: listar_clientes.php?renovado=1');
        } else {
            $erro = 'Erro ao renovar cliente.';
        }
        exit;
    } elseif (isset($_POST['excluir'])) {
        $cliente_id = $_POST['cliente_id'];
        if (excluirCliente($pdo, $cliente_id)) {
            header('Location: listar_clientes.php?excluido=1');
        } else {
            $erro = 'Erro ao excluir cliente.';
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Listar Clientes</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
<div class="container listar-clientes">
    <h1 class="my-4">Listar Clientes</h1>
    <?php if ($cliente_renovado): ?>
        <div class="alert alert-success" role="alert">
            Cliente renovado com sucesso!
        </div>
    <?php elseif ($cliente_excluido): ?>
        <div class="alert alert-success" role="alert">
            Cliente excluído com sucesso!
        </div>
    <?php elseif ($erro): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <!-- Barra de Busca -->
    <div class="mb-3">
        <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar cliente...">
    </div>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAdicionar">Adicionar Novo Cliente</button>

    <table class="table table-striped table-custom">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Vencimento</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaClientes">
        <?php if (!empty($clientes)): ?>
            <?php foreach ($clientes as $cliente): ?>
                <tr class="<?php echo $cliente['status'] == 'vencido' ? 'vencido' : ($cliente['vencimento'] == date('Y-m-d') ? 'vence-hoje' : 'ativo'); ?>">
                    <td><?php echo htmlspecialchars($cliente['nome'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['usuario'] ?? ''); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($cliente['vencimento'])); ?></td>
                    <td>
                        <?php if ($cliente['status'] == 'vencido'): ?>
                            <span class="badge badge-danger">Vencido</span>
                        <?php else: ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info avisar-cliente" data-id="<?php echo $cliente['id']; ?>"><i class="fas fa-paper-plane"></i></button>
                        <button class="btn btn-sm btn-primary renovar-cliente" data-id="<?php echo $cliente['id']; ?>"><i class="fas fa-sync-alt"></i></button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                            <button type="submit" name="excluir" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                        <button class="btn btn-sm btn-info visualizar-cliente" data-id="<?php echo $cliente['id']; ?>"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-warning editar-cliente" data-id="<?php echo $cliente['id']; ?>"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Nenhum cliente encontrado.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

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

    <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
</div>

<!-- Modal para Adicionar Cliente -->
<div class="modal fade" id="modalAdicionar" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAdicionarLabel">Adicionar Novo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="adicionar_cliente.php">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuário:</label>
                        <input type="text" id="usuario" name="usuario" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="vencimento">Vencimento:</label>
                        <input type="date" id="vencimento" name="vencimento" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="ativo">Ativo</option>
                            <option value="vencido">Vencido</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Renovação -->
<div class="modal fade" id="renovarModal" tabindex="-1" aria-labelledby="renovarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renovarModalLabel">Renovar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="renovarForm" method="post">
                    <input type="hidden" name="cliente_id" id="cliente_id">
                    <div class="form-group">
                        <label for="periodo">Selecione o período de renovação:</label>
                        <select name="periodo" id="periodo" class="form-control" required>
                            <option value="+1 month">1 mês</option>
                            <option value="+2 months">2 meses</option>
                            <option value="+3 months">3 meses</option>
                            <option value="+6 months">6 meses</option>
                            <option value="+1 year">1 ano</option>
                        </select>
                    </div>
                    <button type="submit" name="renovar" class="btn btn-primary">Renovar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Enviar Aviso -->
<div class="modal fade" id="avisoModal" tabindex="-1" aria-labelledby="avisoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avisoModalLabel">Enviar Aviso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="avisoForm" method="post" action="enviar_aviso.php">
                    <input type="hidden" name="cliente_id" id="aviso_cliente_id">
                    <div class="form-group">
                        <label for="mensagem_id">Selecione o tipo de aviso:</label>
                        <select name="mensagem_id" id="mensagem_id" class="form-control" required>
                            <option value="" disabled selected>Selecione o aviso</option>
                            <?php foreach ($mensagens as $mensagem): ?>
                                <option value="<?php echo $mensagem['id']; ?>"><?php echo htmlspecialchars($mensagem['nome'] ?? ''); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Aviso</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Visualizar Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clienteModalLabel">Detalhes do Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Conteúdo do cliente será carregado aqui -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.getElementById('buscarCliente').addEventListener('input', function() {
    var searchValue = this.value.toLowerCase();
    var tableRows = document.querySelectorAll('#tabelaClientes tr');

    tableRows.forEach(function(row) {
        var rowText = row.innerText.toLowerCase();
        row.style.display = rowText.includes(searchValue) ? '' : 'none';
    });
});

$(document).ready(function() {
    $('.visualizar-cliente').on('click', function() {
        var clienteId = $(this).data('id');
        $.ajax({
            url: 'visualizar_cliente.php',
            type: 'GET',
            data: { id: clienteId },
            success: function(response) {
                $('#clienteModal .modal-body').html(response);
                $('#clienteModal').modal('show');
            }
        });
    });

    $('.renovar-cliente').on('click', function() {
        var clienteId = $(this).data('id');
        $('#cliente_id').val(clienteId);
        $('#renovarModal').modal('show');
    });

    $('.editar-cliente').on('click', function() {
        var clienteId = $(this).data('id');
        window.location.href = 'editar_cliente.php?id=' + clienteId;
    });

    $('.avisar-cliente').on('click', function() {
        var clienteId = $(this).data('id');
        $('#aviso_cliente_id').val(clienteId);
        $('#avisoModal').modal('show');
    });
});
</script>
</body>
</html>
