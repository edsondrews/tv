<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $mensagem = $_POST['mensagem'];
    $tags = $_POST['tags'];

    include('config/db.php');
    try {
        $pdo = $dbConnection; // Ajuste conforme sua conexão
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Inserir nova mensagem na tabela
        $stmt = $pdo->prepare("INSERT INTO mensagens_aviso (nome, mensagem, tags) VALUES (:nome, :mensagem, :tags)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':mensagem', $mensagem);
        $stmt->bindParam(':tags', $tags);
        $stmt->execute();

        echo "Mensagem adicionada com sucesso.";
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}
?>
