<?php
// Funções de autenticação
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isMaster')) {
    function isMaster() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'master';
    }
}

// Funções relacionadas a usuários
if (!function_exists('listarUsuarios')) {
    function listarUsuarios($pdo) {
        $query = "SELECT id, username, role FROM users";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}



if (!function_exists('obterServidor')) {
    function obterServidor($pdo, $id, $user_id) {
        $stmt = $pdo->prepare("SELECT * FROM servers WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch();
    }
}

if (!function_exists('adicionarServidor')) {
    function adicionarServidor($pdo, $name, $credit_value, $whatsapp_session, $panel_link, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO servers (name, credit_value, whatsapp_session, panel_link, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $credit_value, $whatsapp_session, $panel_link, $user_id]);
    }
}

if (!function_exists('atualizarServidor')) {
    function atualizarServidor($pdo, $name, $credit_value, $whatsapp_session, $panel_link, $id, $user_id) {
        $stmt = $pdo->prepare("UPDATE servers SET name = ?, credit_value = ?, whatsapp_session = ?, panel_link = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$name, $credit_value, $whatsapp_session, $panel_link, $id, $user_id]);
    }
}

// Função para excluir servidor
if (!function_exists('excluirServidor')) {
    function excluirServidor($pdo, $servidor_id) {
        $stmt = $pdo->prepare("DELETE FROM servers WHERE id = :servidor_id");
        return $stmt->execute(['servidor_id' => $servidor_id]);
    }
}
// Função para listar planos
if (!function_exists('listarPlanos')) {
    function listarPlanos($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT id, name, period_type, period, credits_cost, observation FROM plans WHERE user_id = :user_id ORDER BY name ASC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
// Função para obter um plano pelo ID
if (!function_exists('obterPlano')) {
    function obterPlano($pdo, $id, $user_id) {
        $stmt = $pdo->prepare("SELECT id, name, period_type, period, credits_cost, observation FROM plans WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
// Função para adicionar plano
if (!function_exists('adicionarPlano')) {
    function adicionarPlano($pdo, $name, $period_type, $period, $credits_cost, $observation, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO plans (name, period_type, period, credits_cost, observation, user_id) VALUES (:name, :period_type, :period, :credits_cost, :observation, :user_id)");
        return $stmt->execute(['name' => $name, 'period_type' => $period_type, 'period' => $period, 'credits_cost' => $credits_cost, 'observation' => $observation, 'user_id' => $user_id]);
    }
}
if (!function_exists('formatarData')) {
    function formatarData($data) {
        $date = new DateTime($data);
        return $date->format('d/m/Y');
    }
}


// Função para atualizar um plano
if (!function_exists('atualizarPlano')) {
    function atualizarPlano($pdo, $name, $period_type, $period, $credits_cost, $observation, $id, $user_id) {
        $stmt = $pdo->prepare("UPDATE plans SET name = :name, period_type = :period_type, period = :period, credits_cost = :credits_cost, observation = :observation WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['name' => $name, 'period_type' => $period_type, 'period' => $period, 'credits_cost' => $credits_cost, 'observation' => $observation, 'id' => $id, 'user_id' => $user_id]);
    }
}
// Função para excluir um plano
if (!function_exists('excluirPlano')) {
    function excluirPlano($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM plans WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
if (!function_exists('listarPagamentosPaginados')) {
    function listarPagamentosPaginados($pdo, $user_id, $limit, $offset) {
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome AS cliente_nome
            FROM pagamentos p
            JOIN clientes c ON p.cliente_id = c.id
            WHERE p.user_id = ?
            ORDER BY p.data_pagamento DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('contarPagamentos')) {
    function contarPagamentos($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pagamentos WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}// Função para listar formas de pagamento
if (!function_exists('listarFormasPagamento')) {
    function listarFormasPagamento($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT id, nome FROM formas_pagamento WHERE user_id = :user_id ORDER BY nome ASC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
// Função para obter uma forma de pagamento pelo ID
if (!function_exists('obterFormaPagamento')) {
    function obterFormaPagamento($pdo, $id, $user_id) {
        $stmt = $pdo->prepare("SELECT id, nome FROM formas_pagamento WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Função para adicionar forma de pagamento
if (!function_exists('adicionarFormaPagamento')) {
    function adicionarFormaPagamento($pdo, $nome, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO formas_pagamento (nome, user_id) VALUES (:nome, :user_id)");
        return $stmt->execute(['nome' => $nome, 'user_id' => $user_id]);
    }
}

// Função para atualizar uma forma de pagamento
if (!function_exists('atualizarFormaPagamento')) {
    function atualizarFormaPagamento($pdo, $nome, $id, $user_id) {
        $stmt = $pdo->prepare("UPDATE formas_pagamento SET nome = :nome WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['nome' => $nome, 'id' => $id, 'user_id' => $user_id]);
    }
}
// Função para excluir uma forma de pagamento
if (!function_exists('excluirFormaPagamento')) {
    function excluirFormaPagamento($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM formas_pagamento WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

// Funções relacionadas a dispositivos
if (!function_exists('listarDispositivos')) {
    function listarDispositivos($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT * FROM devices WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('adicionarDispositivo')) {
    function adicionarDispositivo($pdo, $name, $description, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO devices (name, description, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $user_id]);
    }
}
if (!function_exists('obterPrecoRevendedor')) {
    function obterPrecoRevendedor($pdo, $revendedor_id, $servidor_id) {
        $stmt = $pdo->prepare("SELECT preco FROM precos_revendedores WHERE revendedor_id = ? AND servidor_id = ?");
        $stmt->execute([$revendedor_id, $servidor_id]);
        return $stmt->fetchColumn();
    }
}
if (!function_exists('obterDispositivo')) {
    function obterDispositivo($pdo, $id, $user_id) {
        $stmt = $pdo->prepare("SELECT * FROM devices WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('atualizarDispositivo')) {
    function atualizarDispositivo($pdo, $name, $description, $id, $user_id) {
        $stmt = $pdo->prepare("UPDATE devices SET name = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$name, $description, $id, $user_id]);
    }
}

if (!function_exists('excluirDispositivo')) {
    function excluirDispositivo($pdo, $id, $user_id) {
        $stmt = $pdo->prepare("DELETE FROM devices WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
    }
}

// Função para adicionar um novo aplicativo
if (!function_exists('adicionarAplicativo')) {
    function adicionarAplicativo($pdo, $nome, $descricao, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO aplicativos (nome, descricao, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $descricao, $user_id]);
    }
}

// Função para listar aplicativos
if (!function_exists('listarAplicativos')) {
    function listarAplicativos($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT id, nome, descricao FROM aplicativos WHERE user_id = :user_id ORDER BY nome ASC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
// Função para excluir aplicativo
if (!function_exists('excluirAplicativo')) {
    function excluirAplicativo($pdo, $aplicativo_id) {
        $stmt = $pdo->prepare("DELETE FROM aplicativos WHERE id = :aplicativo_id");
        return $stmt->execute(['aplicativo_id' => $aplicativo_id]);
    }
}
// Função para editar aplicativo
if (!function_exists('editarAplicativo')) {
    function editarAplicativo($pdo, $aplicativo_id, $nome, $descricao) {
        $stmt = $pdo->prepare("UPDATE aplicativos SET nome = :nome, descricao = :descricao WHERE id = :aplicativo_id");
        return $stmt->execute(['nome' => $nome, 'descricao' => $descricao, 'aplicativo_id' => $aplicativo_id]);
    }
}
// Função para adicionar uma nova forma de captação
if (!function_exists('adicionarFormaCaptacao')) {
    function adicionarFormaCaptacao($pdo, $tipo, $custo, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO capture_methods (name, cost, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$tipo, $custo, $user_id]);
    }
}
// Função para obter uma forma de captação específica
if (!function_exists('obterFormaCaptacao')) {
    function obterFormaCaptacao($pdo, $id, $user_id) {
        $stmt = $pdo->prepare("SELECT * FROM capture_methods WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
// Função para buscar aplicativo por ID
if (!function_exists('buscarAplicativoPorId')) {
    function buscarAplicativoPorId($pdo, $aplicativo_id) {
        $stmt = $pdo->prepare("SELECT id, nome, descricao FROM aplicativos WHERE id = :aplicativo_id");
        $stmt->execute(['aplicativo_id' => $aplicativo_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Função para atualizar uma forma de captação existente
if (!function_exists('atualizarFormaCaptacao')) {
    function atualizarFormaCaptacao($pdo, $nome, $custo, $id, $user_id) {
        $stmt = $pdo->prepare("UPDATE capture_methods SET name = ?, cost = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$nome, $custo, $id, $user_id]);
    }
}

// Função para excluir uma forma de captação
if (!function_exists('excluirFormaCaptacao')) {
    function excluirFormaCaptacao($pdo, $id, $user_id) {
        $stmt = $pdo->prepare("DELETE FROM capture_methods WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
    }
}

// Funções relacionadas a clientes
if (!function_exists('adicionarCliente')) {
    function adicionarCliente($pdo, $nome, $usuario, $senha, $telefone, $vencimento, $hora_vencimento, $email, $observacao, $plano, $valor, $forma_pagamento, $telas, $captacao, $indicado_por, $servidor, $dispositivo, $aplicativo, $mac, $device_key, $vencimento_aplicativo, $receber_mensagem, $user_id) {
        $stmt = $pdo->prepare("
            INSERT INTO clientes (nome, usuario, senha, telefone, vencimento, hora_vencimento, email, observacao, plano, valor, forma_pagamento, telas, captacao, indicado_por, servidor, dispositivo, aplicativo, mac, device_key, vencimento_aplicativo, receber_mensagem, user_id, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, date('now'))
        ");
        $stmt->execute([$nome, $usuario, $senha, $telefone, $vencimento, $hora_vencimento, $email, $observacao, $plano, $valor, $forma_pagamento, $telas, $captacao, $indicado_por, $servidor, $dispositivo, $aplicativo, $mac, $device_key, $vencimento_aplicativo, $receber_mensagem, $user_id]);
    }
}

if (!function_exists('listarClientes')) {
    function listarClientes($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE user_id = ? ORDER BY CASE WHEN status = 'vencido' THEN 1 ELSE 2 END, vencimento ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('listarClientesPaginados')) {
    function listarClientesPaginados($pdo, $user_id, $limit, $offset) {
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE user_id = ? ORDER BY CASE WHEN status = 'vencido' THEN 1 ELSE 2 END, vencimento ASC LIMIT ? OFFSET ?");
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('contarClientes')) {
    function contarClientes($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}


if (!function_exists('atualizarStatusClientes')) {
    function atualizarStatusClientes($pdo, $user_id) {
        $hoje = date('Y-m-d');

        // Atualiza status para 'vencido' onde a data de vencimento é anterior a hoje
        $stmt = $pdo->prepare("UPDATE clientes SET status = 'vencido' WHERE vencimento < :hoje AND user_id = :user_id");
        $stmt->execute(['hoje' => $hoje, 'user_id' => $user_id]);

        // Atualiza status para 'ativo' onde a data de vencimento é hoje ou posterior
        $stmt = $pdo->prepare("UPDATE clientes SET status = 'ativo' WHERE vencimento >= :hoje AND user_id = :user_id");
        $stmt->execute(['hoje' => $hoje, 'user_id' => $user_id]);
    }
}


if (!function_exists('listarFormasCaptacao')) {
    function listarFormasCaptacao($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT * FROM capture_methods WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}if (!function_exists('contarPagamentos')) {
    function contarPagamentos($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pagamentos WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}

if (!function_exists('ativarCliente')) {
    function ativarCliente($pdo, $cliente_id) {
        $stmt = $pdo->prepare("UPDATE clientes SET status = 'ativo' WHERE id = ?");
        $stmt->execute([$cliente_id]);
    }
}
if (!function_exists('ativarCliente')) {
    function ativarCliente($pdo, $cliente_id) {
        // Obter a data de vencimento atual do cliente
        $stmt = $pdo->prepare("SELECT vencimento FROM clientes WHERE id = ?");
        $stmt->execute([$cliente_id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $vencimentoAtual = $cliente['vencimento'];
            $dataAtual = date('Y-m-d');

            // Verificar se a data de vencimento é no passado ou futuro
            if (strtotime($vencimentoAtual) < strtotime($dataAtual)) {
                $statusAtualizado = 'vencido';
            } else {
                $statusAtualizado = 'ativo';
            }

            // Atualizar o status no banco de dados
            $stmtUpdate = $pdo->prepare("UPDATE clientes SET status = ? WHERE id = ?");
            $stmtUpdate->execute([$statusAtualizado, $cliente_id]);

            return true;
        }

        return false;
    }
}
if (!function_exists('atualizarCliente')) {
    function atualizarCliente($pdo, $cliente_id, $nome, $usuario, $senha, $telefone, $vencimento, $hora_vencimento, $email, $observacao, $plano, $valor, $forma_pagamento, $telas, $captacao, $indicado_por, $servidor, $dispositivo, $aplicativo, $mac, $device_key, $vencimento_aplicativo, $receber_mensagem, $user_id) {
        // Verificar o status baseado na nova data de vencimento
        $dataAtual = date('Y-m-d');
        $statusAtualizado = (strtotime($vencimento) < strtotime($dataAtual)) ? 'vencido' : 'ativo';

        // Atualizar o cliente no banco de dados
        $stmt = $pdo->prepare("
            UPDATE clientes 
            SET nome = ?, usuario = ?, senha = ?, telefone = ?, vencimento = ?, vencimento_anterior = ?, hora_vencimento = ?, email = ?, observacao = ?, plano = ?, valor = ?, forma_pagamento = ?, telas = ?, captacao = ?, indicado_por = ?, servidor = ?, dispositivo = ?, aplicativo = ?, mac = ?, device_key = ?, vencimento_aplicativo = ?, receber_mensagem = ?, status = ?
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$nome, $usuario, $senha, $telefone, $vencimento, $vencimento, $hora_vencimento, $email, $observacao, $plano, $valor, $forma_pagamento, $telas, $captacao, $indicado_por, $servidor, $dispositivo, $aplicativo, $mac, $device_key, $vencimento_aplicativo, $receber_mensagem, $statusAtualizado, $cliente_id, $user_id]);
    }
}



if (!function_exists('renovarCliente')) {
    function renovarCliente($pdo, $cliente_id, $user_id, $periodo) {
        // Obter a data de vencimento atual, valor e plano do cliente
        $stmt = $pdo->prepare("SELECT vencimento, valor, plano FROM clientes WHERE id = ? AND user_id = ?");
        $stmt->execute([$cliente_id, $user_id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $vencimentoAtual = $cliente['vencimento'];
            $valor = $cliente['valor'];
            $plano = $cliente['plano'];

            // Calcular a nova data de vencimento
            $hoje = date('Y-m-d');
            if (strtotime($vencimentoAtual) < strtotime($hoje)) {
                // Se a data de vencimento é no passado, adicionar o período a partir de hoje
                $novaDataVencimento = date('Y-m-d', strtotime($periodo, strtotime($hoje)));
            } else {
                // Se a data de vencimento é no futuro ou hoje, adicionar o período a partir da data de vencimento atual
                $novaDataVencimento = date('Y-m-d', strtotime($periodo, strtotime($vencimentoAtual)));
            }

            // Atualizar a data de vencimento e o status no banco de dados
            $stmt = $pdo->prepare("UPDATE clientes SET vencimento = ?, vencimento_anterior = ?, status = 'ativo' WHERE id = ? AND user_id = ?");
            $stmt->execute([$novaDataVencimento, $vencimentoAtual, $cliente_id, $user_id]);

            // Registrar o pagamento
            $stmt = $pdo->prepare("INSERT INTO pagamentos (cliente_id, valor, data_pagamento, user_id, plano) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $valor, date('Y-m-d'), $user_id, $plano]);

            return true;
        }

        return false;
    }
}






// Função para calcular o faturamento mensal
if (!function_exists('calcularFaturamentoMensal')) {
    function calcularFaturamentoMensal($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT SUM(valor) FROM clientes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}

// Função para calcular a projeção mensal
if (!function_exists('calcularProjecaoMensal')) {
    function calcularProjecaoMensal($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT SUM(valor) * 3 FROM clientes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}

// Função para calcular os recebidos hoje
if (!function_exists('calcularRecebidosHoje')) {
    function calcularRecebidosHoje($pdo, $user_id) {
        // Valor dos clientes adicionados hoje
        $stmt = $pdo->prepare("SELECT SUM(valor) FROM clientes WHERE DATE(data_criacao) = DATE('now') AND user_id = ?");
        $stmt->execute([$user_id]);
        $adicionadosHoje = $stmt->fetchColumn();

        // Valor dos clientes renovados hoje
        $stmt = $pdo->prepare("SELECT SUM(valor) FROM pagamentos WHERE DATE(data_pagamento) = DATE('now') AND user_id = ?");
        $stmt->execute([$user_id]);
        $renovadosHoje = $stmt->fetchColumn();

        return $adicionadosHoje + $renovadosHoje;
    }
}

if (!function_exists('registrarPagamento')) {
    function registrarPagamento($pdo, $cliente_id, $valor, $data_pagamento, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO pagamentos (cliente_id, valor, data_pagamento, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$cliente_id, $valor, $data_pagamento, $user_id]);
    }
}

// Função para contar clientes ativos
if (!function_exists('contarClientesAtivos')) {
    function contarClientesAtivos($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE user_id = ? AND status = 'ativo'");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}

// Função para contar clientes vencidos
if (!function_exists('contarClientesVencidos')) {
    function contarClientesVencidos($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE user_id = ? AND status = 'vencido'");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}

// Função para contar o total de clientes
if (!function_exists('contarTotalClientes')) {
    function contarTotalClientes($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}


if (!function_exists('listarPagamentos')) {
    function listarPagamentos($pdo, $userId) {
        try {
            // SQL para selecionar pagamentos do usuário e incluir o nome do cliente
            $sql = "SELECT p.*, c.nome AS cliente_nome 
                    FROM pagamentos p 
                    JOIN clientes c ON p.cliente_id = c.id 
                    WHERE p.user_id = :user_id 
                    ORDER BY p.data_pagamento DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao listar pagamentos: " . $e->getMessage();
            return [];
        }
    }
}

if (!function_exists('desfazerPagamento')) {
    function desfazerPagamento($pdo, $pagamento_id, $user_id) {
        // Obter os detalhes do pagamento que será desfeito
        $stmt = $pdo->prepare("SELECT * FROM pagamentos WHERE id = ? AND user_id = ?");
        $stmt->execute([$pagamento_id, $user_id]);
        $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pagamento) {
            $cliente_id = $pagamento['cliente_id'];
            $data_pagamento = $pagamento['data_pagamento'];
            $valor = $pagamento['valor'];

            // Obter a data de vencimento anterior do cliente
            $stmt = $pdo->prepare("SELECT vencimento_anterior FROM clientes WHERE id = ? AND user_id = ?");
            $stmt->execute([$cliente_id, $user_id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                $vencimento_anterior = $cliente['vencimento_anterior'] ?? '1970-01-01';

                // Atualizar a data de vencimento e o status do cliente
                $dataAtual = date('Y-m-d');
                $statusAtualizado = (strtotime($vencimento_anterior) < strtotime($dataAtual)) ? 'vencido' : 'ativo';

                $stmt = $pdo->prepare("UPDATE clientes SET vencimento = ?, status = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$vencimento_anterior, $statusAtualizado, $cliente_id, $user_id]);

                // Apagar o registro de pagamento
                $stmt = $pdo->prepare("DELETE FROM pagamentos WHERE id = ? AND user_id = ?");
                $stmt->execute([$pagamento_id, $user_id]);

                return true;
            }
        }

        return false;
    }
}








if (!function_exists('atualizarStatusClientes')) {
    function atualizarStatusClientes($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT id, vencimento, status FROM clientes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clientes as $cliente) {
            $dataAtual = date('Y-m-d');
            $statusAtualizado = $cliente['status'];

            if ($cliente['vencimento'] < $dataAtual && $cliente['status'] != 'vencido') {
                $statusAtualizado = 'vencido';
            } elseif ($cliente['vencimento'] >= $dataAtual && $cliente['status'] != 'ativo') {
                $statusAtualizado = 'ativo';
            }

            if ($statusAtualizado != $cliente['status']) {
                $stmtUpdate = $pdo->prepare("UPDATE clientes SET status = ? WHERE id = ?");
                $stmtUpdate->execute([$statusAtualizado, $cliente['id']]);
            }
        }
    }
}
     if (!function_exists('contarVendas')) {
         function contarVendas($pdo) {
             $stmt = $pdo->query("SELECT COUNT(*) FROM vendas_creditos");
             return $stmt->fetchColumn();
         }
     }

     if (!function_exists('listarVendasPaginadas')) {
         function listarVendasPaginadas($pdo, $limit, $offset) {
             $stmt = $pdo->prepare("SELECT v.*, r.nome AS revendedor_nome, s.name AS servidor_nome 
                                    FROM vendas_creditos v
                                    JOIN revendedores r ON v.revendedor_id = r.id
                                    JOIN servers s ON v.servidor_id = s.id
                                    ORDER BY v.data_venda DESC
                                    LIMIT ? OFFSET ?");
             $stmt->execute([$limit, $offset]);
             return $stmt->fetchAll(PDO::FETCH_ASSOC);
         }
     }

     if (!function_exists('totalReceitas')) {
         function totalReceitas($pdo) {
             $stmt = $pdo->query("SELECT SUM(valor_total) FROM vendas_creditos");
             return $stmt->fetchColumn();
         }
     }


     if (!function_exists('totalDespesas')) {
         function totalDespesas($pdo) {
             $stmt = $pdo->query("SELECT SUM(s.credit_value * v.quantidade) FROM vendas_creditos v
                                  JOIN servers s ON v.servidor_id = s.id");
             return $stmt->fetchColumn();
         }
     }

     if (!function_exists('obterSaldo')) {
         function obterSaldo($pdo) {
             return totalReceitas($pdo) - totalDespesas($pdo);
         }
     }

     if (!function_exists('obterVendasPorServidor')) {
         function obterVendasPorServidor($pdo) {
             $stmt = $pdo->query("SELECT s.name, SUM(v.quantidade) AS total_vendas 
                                  FROM vendas_creditos v
                                  JOIN servers s ON v.servidor_id = s.id
                                  GROUP BY s.name");
             $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
             return $result;
         }
     }

     if (!function_exists('obterLucroPorServidor')) {
         function obterLucroPorServidor($pdo) {
             $stmt = $pdo->query("SELECT s.name, SUM((v.preco_credito - s.credit_value) * v.quantidade) AS total_lucro 
                                  FROM vendas_creditos v
                                  JOIN servers s ON v.servidor_id = s.id
                                  GROUP BY s.name");
             $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
             return $result;
         }
     }

     if (!function_exists('obterVendasPorRevendedor')) {
         function obterVendasPorRevendedor($pdo) {
             $stmt = $pdo->query("SELECT r.nome, SUM(v.quantidade) AS total_vendas 
                                  FROM vendas_creditos v
                                  JOIN revendedores r ON v.revendedor_id = r.id
                                  GROUP BY r.nome");
             $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
             return $result;
         }
     }

     if (!function_exists('calcularLucro')) {
         function calcularLucro($pdo, $venda) {
             $stmt = $pdo->prepare("SELECT s.credit_value FROM servers s WHERE s.id = ?");
             $stmt->execute([$venda['servidor_id']]);
             $valor_pago = $stmt->fetchColumn();
             return ($venda['preco_credito'] - $valor_pago) * $venda['quantidade'];
         }
     }// Funções necessárias para cálculos

         if (!function_exists('totalReceitas')) {
             function totalReceitas($pdo, $user_id) {
                 $stmt = $pdo->prepare("SELECT SUM(valor_total) as total FROM vendas_creditos WHERE user_id = ?");
                 $stmt->execute([$user_id]);
                 return $stmt->fetchColumn();
             }
         }

         if (!function_exists('totalDespesas')) {
             function totalDespesas($pdo, $user_id) {
                 $stmt = $pdo->prepare("SELECT SUM(ser.credit_value * v.quantidade) as total FROM vendas_creditos v JOIN servers ser ON v.servidor_id = ser.id WHERE v.user_id = ?");
                 $stmt->execute([$user_id]);
                 return $stmt->fetchColumn();
             }
         }

         if (!function_exists('obterValorServidor')) {
             function obterValorServidor($pdo, $servidor_id) {
                 $stmt = $pdo->prepare("SELECT credit_value FROM servers WHERE id = ?");
                 $stmt->execute([$servidor_id]);
                 return $stmt->fetchColumn();
             }
         }

?>