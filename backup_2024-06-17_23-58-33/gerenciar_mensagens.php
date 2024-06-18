<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Mensagens de Aviso</title>
</head>
<body>
    <h1>Gerenciamento de Mensagens de Aviso</h1>

    <form action="adicionar_mensagem.php" method="POST">
        <label for="nome">Nome da Mensagem:</label>
        <input type="text" id="nome" name="nome" required>
        <br>
        <label for="mensagem">Mensagem:</label>
        <textarea id="mensagem" name="mensagem" required></textarea>
        <br>
        <label for="tags">Tags (separadas por vírgula):</label>
        <input type="text" id="tags" name="tags" placeholder="{NOME},{TELEFONE},{VENCIMENTO}">
        <br>
        <button type="submit">Adicionar Mensagem</button>
    </form>

    <h2>Mensagens Existentes</h2>
    <ul>
        <?php
        include('config/db.php');
        $pdo = $dbConnection; // Ajuste conforme sua conexão

        $result = $pdo->query("SELECT * FROM mensagens_aviso");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . htmlspecialchars($row['nome']) . ": " . htmlspecialchars($row['mensagem']) . " (Tags: " . htmlspecialchars($row['tags']) . ")</li>";
        }
        ?>
    </ul>
</body>
</html>
