<?php
/**
 * ENDALL INSPEÇÕES - Teste de Conexão SMTP
 * Verifica se as configurações SMTP estão corretas
 */

// Definir constante do sistema
define('SISTEMA_ENDALL', true);

// Carregar configurações
require_once __DIR__ . '/includes/config.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste SMTP - Endall</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --cor-primaria: #0D1B2A;
            --cor-secundaria: #F5A623;
            --cor-sucesso: #28a745;
            --cor-erro: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--cor-primaria) 0%, #1a2f4a 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: var(--cor-primaria);
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .config-box {
            background: #f8f9fa;
            border-left: 4px solid var(--cor-secundaria);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .config-box h3 {
            margin-top: 0;
            color: var(--cor-secundaria);
        }
        
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .config-item:last-child {
            border-bottom: none;
        }
        
        .config-label {
            font-weight: 600;
            color: #495057;
        }
        
        .config-value {
            color: var(--cor-primaria);
            font-family: 'Courier New', monospace;
        }
        
        .result-box {
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .result-box.success {
            background: #d4edda;
            border-left: 4px solid var(--cor-sucesso);
            color: #155724;
        }
        
        .result-box.error {
            background: #f8d7da;
            border-left: 4px solid var(--cor-erro);
            color: #721c24;
        }
        
        .result-box h3 {
            margin-top: 0;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: var(--cor-secundaria);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
            margin: 0.5rem;
        }
        
        .btn:hover {
            background: #e09416;
            transform: translateY(-2px);
        }
        
        .actions {
            text-align: center;
            margin-top: 2rem;
        }
        
        .log-output {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-satellite-dish"></i>
            Teste de Conexão SMTP
        </h1>
        
        <div class="config-box">
            <h3><i class="fas fa-cog"></i> Configurações Atuais</h3>
            <div class="config-item">
                <span class="config-label">Host SMTP:</span>
                <span class="config-value"><?= SMTP_HOST ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Porta:</span>
                <span class="config-value"><?= SMTP_PORT ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Segurança:</span>
                <span class="config-value"><?= SMTP_SECURE ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Usuário:</span>
                <span class="config-value"><?= SMTP_USER ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Senha:</span>
                <span class="config-value"><?= str_repeat('•', strlen(SMTP_PASS)) ?> (<?= strlen(SMTP_PASS) ?> caracteres)</span>
            </div>
            <div class="config-item">
                <span class="config-label">Remetente (Nome):</span>
                <span class="config-value"><?= SMTP_FROM_NAME ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Remetente (E-mail):</span>
                <span class="config-value"><?= SMTP_FROM_EMAIL ?></span>
            </div>
        </div>
        
        <?php
        // Testar conexão
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $timeout = 10;
        $errno = 0;
        $errstr = '';
        
        echo '<div class="config-box">';
        echo '<h3><i class="fas fa-vial"></i> Teste de Conexão</h3>';
        echo '<div class="log-output">';
        
        echo "Tentando conectar a {$host}:{$port}...\n\n";
        
        // Teste 1: Resolver DNS
        echo "1. Teste de DNS:\n";
        $ip = gethostbyname($host);
        if ($ip === $host) {
            echo "   ❌ ERRO: Não foi possível resolver o host\n";
            echo "   O domínio '{$host}' não foi encontrado\n\n";
        } else {
            echo "   ✅ Host resolvido: {$ip}\n\n";
        }
        
        // Teste 2: Conexão de socket
        echo "2. Teste de Conexão Socket:\n";
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        
        if ($socket) {
            echo "   ✅ Conexão estabelecida com sucesso!\n";
            echo "   Porta {$port} está aberta e acessível\n\n";
            
            echo "3. Resposta do Servidor SMTP:\n";
            $response = fgets($socket, 512);
            echo "   " . trim($response) . "\n\n";
            
            // Tentar comando EHLO
            echo "4. Teste de Comando EHLO:\n";
            fputs($socket, "EHLO localhost\r\n");
            $response = '';
            while ($line = fgets($socket, 512)) {
                $response .= "   " . trim($line) . "\n";
                if (substr($line, 3, 1) == ' ') break;
            }
            echo $response . "\n";
            
            // Verificar suporte a autenticação
            if (stripos($response, 'AUTH') !== false) {
                echo "   ✅ Servidor suporta autenticação\n";
            } else {
                echo "   ⚠️  Servidor pode não suportar autenticação\n";
            }
            
            // Verificar STARTTLS
            if (stripos($response, 'STARTTLS') !== false) {
                echo "   ✅ Servidor suporta STARTTLS (TLS)\n";
            }
            
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            $connection_ok = true;
        } else {
            echo "   ❌ ERRO ao conectar!\n";
            echo "   Código: {$errno}\n";
            echo "   Mensagem: {$errstr}\n\n";
            
            echo "Possíveis causas:\n";
            echo "   - Host incorreto ('{$host}')\n";
            echo "   - Porta bloqueada (porta {$port})\n";
            echo "   - Firewall do servidor bloqueando\n";
            echo "   - Servidor SMTP offline\n\n";
            
            $connection_ok = false;
        }
        
        echo '</div>';
        echo '</div>';
        ?>
        
        <?php if (isset($connection_ok)): ?>
            <div class="result-box <?= $connection_ok ? 'success' : 'error' ?>">
                <?php if ($connection_ok): ?>
                    <h3><i class="fas fa-check-circle"></i> Conexão Bem-Sucedida!</h3>
                    <p><strong>O servidor SMTP está acessível e respondendo.</strong></p>
                    <p>Se o envio de e-mail ainda não funcionar, o problema pode ser:</p>
                    <ul>
                        <li>Credenciais incorretas (usuário ou senha)</li>
                        <li>E-mail não configurado no painel Umbler</li>
                        <li>Limite de envio atingido</li>
                        <li>Tipo de segurança incorreto (TLS vs SSL)</li>
                    </ul>
                    <p><strong>Próximo passo:</strong> Teste enviar um e-mail real.</p>
                <?php else: ?>
                    <h3><i class="fas fa-times-circle"></i> Falha na Conexão</h3>
                    <p><strong>Não foi possível conectar ao servidor SMTP.</strong></p>
                    <p><strong>O que fazer:</strong></p>
                    <ol>
                        <li>Verifique se o host está correto: <code><?= SMTP_HOST ?></code></li>
                        <li>Teste outras portas (587, 465, 25)</li>
                        <li>Verifique no painel Umbler qual é a configuração correta</li>
                        <li>Entre em contato com o suporte da Umbler</li>
                    </ol>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="teste-email.html" class="btn">
                <i class="fas fa-envelope"></i> Testar Envio de E-mail
            </a>
            <a href="orcamento.php" class="btn">
                <i class="fas fa-file-invoice"></i> Ir para Orçamento
            </a>
            <button class="btn" onclick="location.reload()">
                <i class="fas fa-sync"></i> Testar Novamente
            </button>
        </div>
    </div>
</body>
</html>
