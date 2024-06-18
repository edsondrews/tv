<?php
include('config/auth.php');
include('config/db.php');
include('config/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $telefone = $_POST['telefone'];
    $vencimento = $_POST['vencimento'];
    $hora_vencimento = $_POST['hora_vencimento'];
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $observacao = $_POST['observacao'];
    $plano = $_POST['plano'];
    $valor = $_POST['valor'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $telas = $_POST['telas'];
    $captacao = $_POST['captacao'];
    $indicado_por = isset($_POST['indicado_por']) ? $_POST['indicado_por'] : null;
    $servidor = $_POST['servidor'];
    $dispositivo = $_POST['dispositivo'];
    $aplicativo = $_POST['aplicativo'];
    $mac = $_POST['mac'];
    $device_key = $_POST['device_key'];
    $vencimento_aplicativo = $_POST['vencimento_aplicativo'];
    $receber_mensagem = isset($_POST['receber_mensagem']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    adicionarCliente($pdo, $nome, $usuario, $senha, $telefone, $vencimento, $hora_vencimento, $email, $observacao, $plano, $valor, $forma_pagamento, $telas, $captacao, $indicado_por, $servidor, $dispositivo, $aplicativo, $mac, $device_key, $vencimento_aplicativo, $receber_mensagem, $user_id);

    header('Location: dashboard.php');
    exit;
}

$planos = listarPlanos($pdo, $_SESSION['user_id']);
$formasPagamento = listarFormasPagamento($pdo, $_SESSION['user_id']);
$servidores = listarServidores($pdo, $_SESSION['user_id']);
$dispositivos = listarDispositivos($pdo, $_SESSION['user_id']);
$formasCaptacao = listarFormasCaptacao($pdo, $_SESSION['user_id']);
$aplicativos = listarAplicativos($pdo, $_SESSION['user_id']);
$clientes = listarClientes($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Cliente</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .form-group {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Adicionar Cliente</h1>
        <form method="post">
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Usuário:</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" class="form-control">
            </div>
            <div class="form-group">
                <label>Telefone:</label>
                <input type="text" name="telefone" class="form-control">
            </div>
            <div class="form-group">
                <label>Vencimento:</label>
                <input type="date" name="vencimento" class="form-control">
            </div>
            <div class="form-group">
                <label>Hora Vencimento:</label>
                <input type="time" name="hora_vencimento" class="form-control">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Plano:</label>
                <select name="plano" class="form-control" required>
                    <option value="">----</option>
                    <?php foreach ($planos as $plano) { ?>
                        <option value="<?php echo $plano['id']; ?>"><?php echo $plano['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Valor:</label>
                <input type="number" name="valor" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Forma de Pagamento:</label>
                <select name="forma_pagamento" class="form-control" required>
                    <option value="">----</option>
                    <?php foreach ($formasPagamento as $forma) { ?>
                        <option value="<?php echo $forma['id']; ?>"><?php echo $forma['nome']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Telas:</label>
                <input type="number" name="telas" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Captação:</label>
                <select name="captacao" class="form-control">
                    <option value="">----</option>
                    <?php foreach ($formasCaptacao as $forma) { ?>
                        <option value="<?php echo $forma['id']; ?>"><?php echo $forma['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Indicado por:</label>
                <input type="text" id="search_indicado" class="form-control" placeholder="Buscar cliente">
                <input type="hidden" name="indicado_por" id="indicado_por">
            </div>
            <div class="form-group">
                <label>Servidor:</label>
                <select name="servidor" class="form-control" required>
                    <option value="">----</option>
                    <?php foreach ($servidores as $servidor) { ?>
                        <option value="<?php echo $servidor['id']; ?>"><?php echo $servidor['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dispositivo:</label>
                <select name="dispositivo" class="form-control">
                    <option value="">----</option>
                    <?php foreach ($dispositivos as $dispositivo) { ?>
                        <option value="<?php echo $dispositivo['id']; ?>"><?php echo $dispositivo['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Aplicativo:</label>
                <select name="aplicativo" id="aplicativo" class="form-control">
                    <option value="">----</option>
                    <?php foreach ($aplicativos as $aplicativo) { ?>
                        <option value="<?php echo $aplicativo['id']; ?>"><?php echo $aplicativo['nome']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div id="mac-field" class="form-group" style="display: none;">
                <label>MAC:</label>
                <input type="text" name="mac" class="form-control">
            </div>
            <div id="device-key-field" class="form-group" style="display: none;">
                <label>Device Key:</label>
                <input type="text" name="device_key" class="form-control">
            </div>
            <div id="vencimento-aplicativo-field" class="form-group" style="display: none;">
                <label>Data de Vencimento do Aplicativo:</label>
                <input type="date" name="vencimento_aplicativo" class="form-control">
            </div>
            <div class="form-group">
                <label>Receber mensagem:</label>
                <input type="checkbox" name="receber_mensagem">
            </div>
            <div class="form-group">
                <label>Observação:</label>
                <textarea name="observacao" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#aplicativo').change(function() {
                var macField = $('#mac-field');
                var deviceKeyField = $('#device-key-field');
                var vencimentoAplicativoField = $('#vencimento-aplicativo-field');
                if ($(this).val()) {
                    macField.show();
                    deviceKeyField.show();
                    vencimentoAplicativoField.show();
                } else {
                    macField.hide();
                    deviceKeyField.hide();
                    vencimentoAplicativoField.hide();
                }
            });

            $(document).ready(function() {
                var clientes = [
                    <?php foreach ($clientes as $cliente) { ?>
                        {
                            label: "<?php echo $cliente['nome']; ?>",
                            value: "<?php echo $cliente['id']; ?>"
                        },
                    <?php } ?>
                ];

                $("#search_indicado").autocomplete({
                    source: clientes,
                    select: function(event, ui) {
                        $("#search_indicado").val(ui.item.label);
                        $("#indicado_por").val(ui.item.value);
                        return false;
                    }
                });
            });

            $('#search_indicado').on('input', function() {
                var searchValue = $(this).val().toLowerCase();
                $('#indicado_por option').each(function() {
                    var optionText = $(this).text().toLowerCase();
                    $(this).toggle(optionText.includes(searchValue));
                });
            });
        });
    </script>
</body>
</html>
