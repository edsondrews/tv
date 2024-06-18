<?php
session_start();
require 'config/config.php'; // Ajuste para o caminho correto do arquivo de configuração do seu sistema
require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;

// Caminho do arquivo de banco de dados SQLite
$dbPath = __DIR__ . '/sistema.db';

try {
    // Conectar ao banco de dados SQLite
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Credenciais da conta Twilio
$sid    = "AC344e43594ab99bf686030ed3d4701d4e";
$token  = "cf1cd2511f2f050e286e924f4d9733b6"; // Substitua 'your_auth_token' pelo seu token de autenticação
$twilio = new Client($sid, $token);

// Número de telefone do Sandbox do WhatsApp e destino
$twilio_number = 'whatsapp:+14155238886';
$destination_number = 'whatsapp:+554984251522'; // Ajuste para o número de telefone de destino

try {
    // Enviar mensagem
    $message = $twilio->messages->create(
        $destination_number, // Número de destino
        array(
            'from' => $twilio_number,
            'body' => 'Sua consulta está marcada para o dia 21 de julho às 15h.'
        )
    );

    echo "Mensagem enviada! SID: " . $message->sid;
} catch (Exception $e) {
    echo "Erro ao enviar a mensagem: " . $e->getMessage();
}
?>
