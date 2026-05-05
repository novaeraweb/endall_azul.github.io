<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Logs PHP - Endall</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --cor-primaria: #0D1B2A;
            --cor-secundaria: #F5A623;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--cor-primaria) 0%, #1a2f4a 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 1400px;
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
        
        .instrucoes {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .instrucoes h3 {
            margin-top: 0;
            color: #856404;
        }
        
        .instrucoes ol {
            margin-left: 1.5rem;
            color: #856404;
        }
        
        .debug-output {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1.5rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            max-height: 600px;
            overflow-y: auto;
            margin-bottom: 2rem;
            white-space: pre-wrap;
            word-wrap: break-word;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-bug"></i>
            Verificar Logs PHP - Debug do Orçamento
        </h1>
        
        <div class="instrucoes">
            <h3><i class="fas fa-info-circle"></i> Como Usar Esta Página</h3>
            <ol>
                <li>Abra a página de orçamento: <code>orcamento.php</code></li>
                <li>Preencha o formulário</li>
                <li>Clique em "Solicitar Orçamento"</li>
                <li>Volte para esta página</li>
                <li>Clique no botão "Recarregar Logs" abaixo</li>
                <li>Os logs aparecerão na caixa preta</li>
            </ol>
        </div>
        
        <h3><i class="fas fa-terminal"></i> Informações do PHP</h3>
        <div class="debug-output">
<?php
// Definir constante do sistema
define('SISTEMA_ENDALL', true);

// Carregar configurações
require_once __DIR__ . '/includes/config.php';

echo "=== CONFIGURAÇÃO DO PHP ===\n\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Error Log: " . ini_get('error_log') . "\n";
echo "Display Errors: " . ini_get('display_errors') . "\n";
echo "Error Reporting: " . error_reporting() . "\n";
echo "Log Errors: " . ini_get('log_errors') . "\n\n";

echo "=== CAMINHOS DO PROJETO ===\n\n";
echo "BASE_PATH: " . BASE_PATH . "\n";
echo "SITE_URL: " . SITE_URL . "\n";
echo "DEBUG_MODE: " . (DEBUG_MODE ? 'true' : 'false') . "\n\n";

echo "=== ÚLTIMAS 50 LINHAS DO ERROR_LOG (se existir) ===\n\n";

// Tentar ler o error_log
$error_log_file = ini_get('error_log');

if (empty($error_log_file)) {
    // Tentar locais comuns
    $possible_logs = [
        __DIR__ . '/error_log',
        __DIR__ . '/../error_log',
        '/var/log/php_errors.log',
        '/var/log/apache2/error.log',
        'C:/xampp/apache/logs/error.log',
        'C:/wamp64/logs/php_error.log'
    ];
    
    foreach ($possible_logs as $log) {
        if (file_exists($log)) {
            $error_log_file = $log;
            echo "✅ Arquivo de log encontrado: $log\n\n";
            break;
        }
    }
}

if ($error_log_file && file_exists($error_log_file)) {
    echo "Lendo arquivo: $error_log_file\n";
    echo str_repeat('-', 80) . "\n\n";
    
    $lines = file($error_log_file);
    $last_lines = array_slice($lines, -50);
    
    // Filtrar apenas linhas relacionadas ao orçamento
    $filtered = array_filter($last_lines, function($line) {
        return stripos($line, 'orcamento') !== false || 
               stripos($line, 'itens_json') !== false ||
               stripos($line, 'ERRO') !== false;
    });
    
    if (!empty($filtered)) {
        echo "Linhas filtradas (orçamento/itens_json/ERRO):\n\n";
        echo implode('', $filtered);
    } else {
        echo "⚠️  Nenhuma linha relacionada a orçamento encontrada.\n";
        echo "Últimas 10 linhas do log:\n\n";
        echo implode('', array_slice($lines, -10));
    }
} else {
    echo "❌ Arquivo de log PHP não encontrado.\n";
    echo "Possíveis causas:\n";
    echo "- Log não está configurado\n";
    echo "- Log está em outro local\n";
    echo "- Permissões de leitura insuficientes\n\n";
    
    echo "Para habilitar logs, adicione em php.ini:\n";
    echo "log_errors = On\n";
    echo "error_log = /caminho/para/error_log\n";
}

echo "\n\n=== TESTE: Escrever no log ===\n\n";
error_log('=== TESTE DE LOG DO PHP - ' . date('Y-m-d H:i:s') . ' ===');
echo "✅ Tentativa de escrever no log realizada.\n";
echo "Se não aparecer acima, verifique a configuração do php.ini\n";
?>
        </div>
        
        <div class="actions">
            <button class="btn" onclick="location.reload()">
                <i class="fas fa-sync"></i> Recarregar Logs
            </button>
            <a href="orcamento.php" class="btn">
                <i class="fas fa-file-invoice"></i> Ir para Orçamento
            </a>
            <a href="teste-orcamento.html" class="btn">
                <i class="fas fa-vial"></i> Ir para Teste
            </a>
        </div>
    </div>
</body>
</html>
