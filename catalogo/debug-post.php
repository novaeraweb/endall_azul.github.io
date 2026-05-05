<?php
/**
 * ENDALL INSPEÇÕES - Debug de POST
 * Página para capturar e exibir EXATAMENTE o que está sendo enviado pelo formulário
 */

// Definir constante do sistema
define('SISTEMA_ENDALL', true);

// Carregar configurações
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug POST - Endall</title>
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
        
        .debug-box {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1.5rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            overflow-x: auto;
        }
        
        .debug-box h3 {
            color: var(--cor-secundaria);
            margin-top: 0;
        }
        
        .success {
            color: #50fa7b;
        }
        
        .error {
            color: #ff5555;
        }
        
        .warning {
            color: #f1fa8c;
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
            Debug de POST - Captura Completa
        </h1>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            
            <div class="debug-box">
                <h3>✅ POST RECEBIDO!</h3>
                <p class="success">Dados capturados em: <?= date('Y-m-d H:i:s') ?></p>
            </div>
            
            <div class="debug-box">
                <h3>📦 $_POST (dados do formulário)</h3>
                <pre><?php print_r($_POST); ?></pre>
            </div>
            
            <div class="debug-box">
                <h3>🔍 Análise Detalhada</h3>
                <pre><?php
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "isset(\$_POST['enviar_orcamento']): " . (isset($_POST['enviar_orcamento']) ? 'true' : 'false') . "\n\n";

echo "=== CAMPOS DO FORMULÁRIO ===\n\n";

$campos = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_empresa', 'cliente_cargo', 'cliente_mensagem', 'itens_json', 'csrf_token'];

foreach ($campos as $campo) {
    echo "$campo:\n";
    echo "  - isset: " . (isset($_POST[$campo]) ? 'true' : 'false') . "\n";
    echo "  - empty: " . (empty($_POST[$campo]) ? 'true' : 'false') . "\n";
    
    if (isset($_POST[$campo])) {
        $valor = $_POST[$campo];
        echo "  - tipo: " . gettype($valor) . "\n";
        echo "  - tamanho: " . strlen($valor) . " caracteres\n";
        
        if ($campo === 'itens_json') {
            echo "  - primeiros 100 chars: " . substr($valor, 0, 100) . "...\n";
            
            $itens = json_decode($valor, true);
            echo "  - json_decode success: " . ($itens !== null ? 'true' : 'false') . "\n";
            echo "  - json_last_error: " . json_last_error_msg() . "\n";
            
            if ($itens !== null) {
                echo "  - tipo após decode: " . gettype($itens) . "\n";
                echo "  - count: " . (is_array($itens) ? count($itens) : 'N/A') . "\n";
                echo "  - empty após decode: " . (empty($itens) ? 'true' : 'false') . "\n";
                
                if (!empty($itens)) {
                    echo "\n  CONTEÚDO DECODIFICADO:\n";
                    print_r($itens);
                }
            }
        } else {
            echo "  - valor: " . htmlspecialchars($valor) . "\n";
        }
    }
    
    echo "\n";
}
                ?></pre>
            </div>
            
            <div class="debug-box">
                <h3>🧪 Teste de Validações</h3>
                <pre><?php
require_once __DIR__ . '/includes/functions.php';

$cliente_nome = postParam('cliente_nome');
$cliente_email = postParam('cliente_email');
$cliente_telefone = postParam('cliente_telefone');
$itens_json = postParam('itens_json');

echo "=== VALIDAÇÕES ===\n\n";
echo "validarObrigatorio(\$cliente_nome): " . (validarObrigatorio($cliente_nome) ? 'true ✅' : 'false ❌') . "\n";
echo "validarEmail(\$cliente_email): " . (validarEmail($cliente_email) ? 'true ✅' : 'false ❌') . "\n";
echo "validarTelefone(\$cliente_telefone): " . (validarTelefone($cliente_telefone) ? 'true ✅' : 'false ❌') . "\n\n";

echo "empty(\$itens_json): " . (empty($itens_json) ? 'true ❌' : 'false ✅') . "\n\n";

if (!empty($itens_json)) {
    $itens = json_decode($itens_json, true);
    echo "empty(\$itens após decode): " . (empty($itens) ? 'true ❌' : 'false ✅') . "\n";
}

echo "\n=== RESULTADO ===\n\n";

$erros = [];

if (!validarObrigatorio($cliente_nome)) {
    $erros[] = 'Nome completo é obrigatório';
}

if (!validarEmail($cliente_email)) {
    $erros[] = 'E-mail inválido';
}

if (!validarTelefone($cliente_telefone)) {
    $erros[] = 'Telefone inválido';
}

if (empty($itens_json)) {
    $erros[] = 'Nenhum produto selecionado';
} else {
    $itens = json_decode($itens_json, true);
    
    if (empty($itens)) {
        $erros[] = 'Erro ao processar os produtos selecionados';
    }
}

if (empty($erros)) {
    echo "✅ TODOS OS TESTES PASSARAM!\n";
    echo "✅ O formulário está pronto para ser processado!\n";
} else {
    echo "❌ ERROS ENCONTRADOS:\n\n";
    foreach ($erros as $erro) {
        echo "  - $erro\n";
    }
}
                ?></pre>
            </div>
            
        <?php else: ?>
            
            <div class="debug-box">
                <h3 class="warning">⚠️  Nenhum POST recebido ainda</h3>
                <p>Para usar esta página de debug:</p>
                <ol>
                    <li>Vá para a página de orçamento</li>
                    <li>Preencha o formulário</li>
                    <li>ANTES de clicar "Solicitar Orçamento", mude o action do form para apontar para esta página</li>
                    <li>OU use o formulário de teste abaixo</li>
                </ol>
            </div>
            
        <?php endif; ?>
        
        <div class="actions">
            <a href="orcamento.php" class="btn">
                <i class="fas fa-file-invoice"></i> Ir para Orçamento
            </a>
            <a href="teste-orcamento.html" class="btn">
                <i class="fas fa-vial"></i> Página de Teste
            </a>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <button class="btn" onclick="location.reload()">
                <i class="fas fa-sync"></i> Limpar (GET)
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/carrinho.js"></script>
</body>
</html>
