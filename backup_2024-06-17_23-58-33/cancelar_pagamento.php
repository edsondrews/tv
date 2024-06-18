<?php
require 'config/db.php'; // Ajuste para o caminho correto do arquivo de configuração do seu sistema

// Verificar se o ID do pagamento e user_id foram passados
if (isset($_POST['pagamento_id']) && isset($_POST['user_id'])) {
    $pagamento_id = $_POST['pagamento_id'];
    $user_id = $_POST['user_id'];

    try {
        // Conectar ao banco de dados SQLite
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar se o pagamento pertence ao usuário
        $sql = "SELECT * FROM pagamentos WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $pagamento_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pagamento) {
            // Se a data original está armazenada, restaure-a e cancele a renovação
            if (!empty($pagamento['data_pagamento_original'])) {
                $sql = "UPDATE pagamentos SET status = 'cancelado', data_pagamento = data_pagamento_original, data_pagamento_original = NULL WHERE id = :id AND user_id = :user_id";
            } else {
                // Se a data original não está armazenada, armazene-a
                $sql = "UPDATE pagamentos SET status = 'cancelado', data_pagamento_original = data_pagamento WHERE id = :id AND user_id = :user_id";
            }

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $pagamento_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "Pagamento cancelado com sucesso.";
            } else {
                echo "Erro ao cancelar o pagamento.";
            }
        } else {
            echo "Pagamento não encontrado ou você não tem permissão para cancelar este pagamento.";
        }

    } catch (PDOException $e) {
        echo "Erro ao cancelar o pagamento: " . $e->getMessage();
    }
} else {
    echo "ID do pagamento ou user_id não fornecido.";
}
?>
