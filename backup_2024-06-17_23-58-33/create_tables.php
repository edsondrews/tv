<?php
// Caminho do arquivo de banco de dados SQLite
$dbPath = __DIR__ . '/sistema.db';

try {
    // Conectar ao banco de dados SQLite
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexão bem-sucedida.<br>";

    // Criar a tabela de usuários
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL CHECK(role IN ('master', 'admin'))
    )");

    // Criar a tabela de clientes
    $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        usuario TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL,
        telefone TEXT,
        telefone_secundario TEXT,
        vencimento DATE,
        hora_vencimento TIME,
        email TEXT,
        observacao TEXT,
        captacao INTEGER,
        indicado_por INTEGER,
        servidor INTEGER,
        dispositivo TEXT,
        aplicativo TEXT,
        mac TEXT,
        device_key TEXT,
        link_m3u TEXT,
        aniversario DATE,
        receber_mensagem INTEGER,
        adicionar_pagamento INTEGER,
        enviar_mensagem_pagamento INTEGER,
        FOREIGN KEY (captacao) REFERENCES formas_captacao(id),
        FOREIGN KEY (indicado_por) REFERENCES clientes(id),
        FOREIGN KEY (servidor) REFERENCES servidores(id)
    )");

    // Criar a tabela de pagamentos
    $pdo->exec("CREATE TABLE IF NOT EXISTS pagamentos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cliente_id INTEGER NOT NULL,
        plano INTEGER NOT NULL,
        valor REAL,
        forma_pagamento TEXT,
        telas INTEGER,
        pix TEXT,
        pontos_fidelidade REAL,
        data_pagamento DATE,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id),
        FOREIGN KEY (plano) REFERENCES planos(id)
    )");

    // Criar a tabela de planos
    $pdo->exec("CREATE TABLE IF NOT EXISTS planos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        tipo_periodo TEXT,
        periodo INTEGER,
        creditos_gastos REAL,
        observacao TEXT
    )");

    // Criar a tabela de servidores
    $pdo->exec("CREATE TABLE IF NOT EXISTS servidores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        valor_credito REAL,
        sessao_whatsapp TEXT,
        link_painel TEXT
    )");

    // Criar a tabela de formas de captação
    $pdo->exec("CREATE TABLE IF NOT EXISTS formas_captacao (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tipo TEXT NOT NULL,
        custo REAL
    )");

    // Criar a tabela de mensagens automáticas
    $pdo->exec("CREATE TABLE IF NOT EXISTS mensagens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        mensagem TEXT,
        servidor INTEGER,
        captacao INTEGER,
        dispositivo TEXT,
        aplicativo TEXT,
        intervalo_min INTEGER,
        intervalo_max INTEGER,
        convertido INTEGER,
        motivo_nao_convertido TEXT,
        situacao_teste TEXT,
        diferenca_minutos INTEGER,
        FOREIGN KEY (servidor) REFERENCES servidores(id),
        FOREIGN KEY (captacao) REFERENCES formas_captacao(id)
    )");

    // Criar a tabela de motivos
    $pdo->exec("CREATE TABLE IF NOT EXISTS motivos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        descricao TEXT
    )");

    // Criar a tabela de configurações
    $pdo->exec("CREATE TABLE IF NOT EXISTS configuracoes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        chave TEXT NOT NULL,
        valor TEXT NOT NULL
    )");

    // Inserir o usuário master
    $password = password_hash('master', PASSWORD_BCRYPT);
    $pdo->exec("INSERT INTO users (username, password, role) VALUES ('master', '$password', 'master')");

    echo "Tabelas criadas e usuário master inserido com sucesso.";

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
