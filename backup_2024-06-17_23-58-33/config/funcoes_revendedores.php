<?php
if (!function_exists('listarServidores')) {
    function listarServidores($pdo) {
        $stmt = $pdo->query("SELECT * FROM servers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('listarRevendedores')) {
    function listarRevendedores($pdo) {
        $stmt = $pdo->query("
            SELECT r.*, GROUP_CONCAT(s.name || ' - R$' || pr.preco) AS servidores, SUM(pr.preco) AS total_vendas, COUNT(pr.servidor_id) AS quantidade_servidores
            FROM revendedores r
            LEFT JOIN precos_revendedores pr ON r.id = pr.revendedor_id
            LEFT JOIN servers s ON pr.servidor_id = s.id
            GROUP BY r.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('adicionarOuAtualizarRevendedor')) {
    function adicionarOuAtualizarRevendedor($pdo, $nome, $servidores_preco, $user_id, $revendedor_id = null) {
        if ($revendedor_id) {
            $stmt = $pdo->prepare("UPDATE revendedores SET nome = ?, user_id = ? WHERE id = ?");
            $stmt->execute([$nome, $user_id, $revendedor_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO revendedores (nome, user_id, data_criacao) VALUES (?, ?, date('now'))");
            $stmt->execute([$nome, $user_id]);
            $revendedor_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("DELETE FROM precos_revendedores WHERE revendedor_id = ?");
        $stmt->execute([$revendedor_id]);

        foreach ($servidores_preco as $servidor_id => $preco) {
            $stmt = $pdo->prepare("INSERT INTO precos_revendedores (revendedor_id, servidor_id, preco, data_inicio) VALUES (?, ?, ?, date('now'))");
            $stmt->execute([$revendedor_id, $servidor_id, $preco]);
        }
    }
}

if (!function_exists('obterRevendedorPorId')) {
    function obterRevendedorPorId($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM revendedores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('obterPrecosRevendedor')) {
    function obterPrecosRevendedor($pdo, $revendedor_id) {
        $stmt = $pdo->prepare("SELECT * FROM precos_revendedores WHERE revendedor_id = ?");
        $stmt->execute([$revendedor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('listarVendasCreditos')) {
    function listarVendasCreditos($pdo) {
        $stmt = $pdo->query("
            SELECT v.*, r.nome AS revendedor_nome, s.name AS servidor_nome
            FROM vendas_creditos v
            LEFT JOIN revendedores r ON v.revendedor_id = r.id
            LEFT JOIN servers s ON v.servidor_id = s.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('contarVendas')) {
    function contarVendas($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM vendas_creditos WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}

if (!function_exists('totalReceitas')) {
    function totalReceitas($pdo, $user_id, $mes, $ano) {
        $stmt = $pdo->prepare("
            SELECT SUM(valor_total) FROM vendas_creditos
            WHERE user_id = ? AND strftime('%m', data_venda) = ? AND strftime('%Y', data_venda) = ?
        ");
        $stmt->execute([$user_id, $mes, $ano]);
        return $stmt->fetchColumn();
    }
}

if (!function_exists('totalDespesas')) {
    function totalDespesas($pdo, $user_id, $mes, $ano) {
        $stmt = $pdo->prepare("
            SELECT SUM(preco) FROM precos_revendedores pr
            JOIN vendas_creditos v ON pr.revendedor_id = v.revendedor_id
            WHERE v.user_id = ? AND strftime('%m', v.data_venda) = ? AND strftime('%Y', v.data_venda) = ?
        ");
        $stmt->execute([$user_id, $mes, $ano]);
        return $stmt->fetchColumn();
    }
}

if (!function_exists('obterValorServidor')) {
    function obterValorServidor($pdo, $servidor_id) {
        $stmt = $pdo->prepare("SELECT credit_value FROM servers WHERE id = :servidor_id");
        $stmt->bindValue(':servidor_id', $servidor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}

if (!function_exists('calcularLucro')) {
    function calcularLucro($preco_credito, $preco_pago, $quantidade) {
        return ($preco_credito - $preco_pago) * $quantidade;
    }
}

if (!function_exists('listarVendasComLucroENomes')) {
    function listarVendasComLucroENomes($pdo) {
        $stmt = $pdo->query("
            SELECT v.*, r.nome AS revendedor_nome, s.name AS servidor_nome,
                   (v.preco_credito - s.credit_value) * v.quantidade AS lucro
            FROM vendas_creditos v
            LEFT JOIN revendedores r ON v.revendedor_id = r.id
            LEFT JOIN servers s ON v.servidor_id = s.id
            ORDER BY v.data_venda DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('editarVenda')) {
    function editarVenda($pdo, $id, $quantidade, $preco_credito) {
        $stmt = $pdo->prepare("UPDATE vendas_creditos SET quantidade = ?, preco_credito = ? WHERE id = ?");
        return $stmt->execute([$quantidade, $preco_credito, $id]);
    }
}

if (!function_exists('desfazerVenda')) {
    function desfazerVenda($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM vendas_creditos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

if (!function_exists('agruparVendasPorRevendedorEServidor')) {
    function agruparVendasPorRevendedorEServidor($pdo) {
        $stmt = $pdo->query("
            SELECT r.nome AS revendedor_nome, s.name AS servidor_nome, SUM(v.valor_total) AS total_vendas
            FROM vendas_creditos v
            LEFT JOIN revendedores r ON v.revendedor_id = r.id
            LEFT JOIN servers s ON v.servidor_id = s.id
            GROUP BY r.nome, s.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('listarVendasPorMes')) {
    function listarVendasPorMes($pdo, $mes, $ano) {
        $stmt = $pdo->prepare("
            SELECT v.*, r.nome AS revendedor_nome, s.name AS servidor_nome,
                   (v.preco_credito - s.credit_value) * v.quantidade AS lucro
            FROM vendas_creditos v
            LEFT JOIN revendedores r ON v.revendedor_id = r.id
            LEFT JOIN servers s ON v.servidor_id = s.id
            WHERE strftime('%m', v.data_venda) = ? AND strftime('%Y', v.data_venda) = ?
            ORDER BY v.data_venda DESC
        ");
        $stmt->execute([$mes, $ano]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
