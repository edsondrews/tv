<?php
session_start();
require 'config/config.php'; // Ajuste para o caminho correto do arquivo de configuração do seu sistema
require __DIR__ . '/vendor/autoload.php';

// Caminho do arquivo de banco de dados SQLite
$dbPath = __DIR__ . '/sistema.db';

try {
    // Conectar ao banco de dados SQLite
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Obter dados do cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->bindParam(':id', $_POST['cliente_id']);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente não encontrado.");
}

// Obter a mensagem de aviso selecionada
$stmt = $pdo->prepare("SELECT mensagem FROM mensagens_aviso WHERE id = :mensagem_id");
$stmt->bindParam(':mensagem_id', $_POST['mensagem_id']);
$stmt->execute();
$mensagem_aviso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mensagem_aviso) {
    die("Mensagem de aviso não encontrada.");
}

$texto_mensagem = $mensagem_aviso['mensagem'];

// Substituir tags na mensagem (se houver)
$tags = ['{NOME}', '{USUARIO}', '{TELEFONE}', '{VENCIMENTO}'];
$valores = [
    htmlspecialchars($cliente['nome']),
    htmlspecialchars($cliente['usuario']),
    htmlspecialchars($cliente['telefone']),
    htmlspecialchars($cliente['vencimento']),
];
$texto_mensagem = str_replace($tags, $valores, $texto_mensagem);

// Verificar se o número de telefone está no formato correto
$destination_number = $cliente['telefone'];
if (!preg_match('/^\+?\d+$/', $destination_number)) {
    die("Número de telefone inválido.");
}

// Simular o envio da mensagem
echo "Mensagem simulada para " . htmlspecialchars($cliente['nome']) . ": " . htmlspecialchars($destination_number) . "<br>Mensagem: " . htmlspecialchars($texto_mensagem) . "<br>";

/*
// Código comentado para evitar envio real de mensagens
// Credenciais da conta Twilio (em produção)
$sid    = "AC344e43594ab99bf686030ed3d4701d4e"; // SID de produção
$token  = "cf1cd2511f2f050e286e924f4d9733b6"; // Token de produção
$twilio = new Client($sid, $token);

// Número de telefone do Sandbox do WhatsApp
$twilio_number = 'whatsapp:+14155238886';

// Enviar mensagem
$message = $twilio->messages->create(
    "whatsapp:$destination_number", // Número de destino
    array(
        'from' => $twilio_number,
        'body' => $texto_mensagem
    )
);

echo "Mensagem enviada para " . htmlspecialchars($cliente['nome']) . ": " . htmlspecialchars($destination_number) . "<br>SID: " . $message->sid . "<br>";
*/

?>
