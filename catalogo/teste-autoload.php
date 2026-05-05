<?php
/**
 * Teste Rápido de Autoload
 * Verifica se PHPMailer e mPDF estão carregando
 */

define('SISTEMA_ENDALL', true);

echo '<html><head><meta charset="UTF-8"><title>Teste Autoload</title>';
echo '<style>body{font-family:Arial;padding:2rem;background:#f5f5f5}';
echo '.box{background:white;padding:1.5rem;border-radius:8px;margin:1rem 0;box-shadow:0 2px 4px rgba(0,0,0,0.1)}';
echo '.success{color:#28a745;font-weight:bold}';
echo '.error{color:#dc3545;font-weight:bold}';
echo 'h1{color:#667eea}h2{color:#495057;margin-top:2rem}</style></head><body>';

echo '<h1>🔍 Teste de Autoload</h1>';

// Caminho do vendor
$vendorPath = __DIR__ . '/vendor';
$autoloadPath = $vendorPath . '/autoload.php';

echo '<div class="box">';
echo '<h2>📂 Verificação de Arquivos</h2>';

if (file_exists($autoloadPath)) {
    echo '<p class="success">✅ autoload.php existe</p>';
    echo '<p style="color:#6c757d">Caminho: ' . $autoloadPath . '</p>';
} else {
    echo '<p class="error">❌ autoload.php NÃO ENCONTRADO</p>';
    echo '<p>Procurado em: ' . $autoloadPath . '</p>';
    exit;
}

// Verificar pasta mpdf
$mpdfPath = $vendorPath . '/mpdf/mpdf/src';
if (is_dir($mpdfPath)) {
    echo '<p class="success">✅ Pasta mPDF existe</p>';
    echo '<p style="color:#6c757d">Caminho: ' . $mpdfPath . '</p>';
    
    // Verificar arquivo Mpdf.php
    $mpdfFile = $mpdfPath . '/Mpdf.php';
    if (file_exists($mpdfFile)) {
        echo '<p class="success">✅ Mpdf.php existe</p>';
        echo '<p style="color:#6c757d">Tamanho: ' . number_format(filesize($mpdfFile)) . ' bytes</p>';
    } else {
        echo '<p class="error">❌ Mpdf.php NÃO ENCONTRADO</p>';
    }
} else {
    echo '<p class="error">❌ Pasta mPDF NÃO ENCONTRADA</p>';
    echo '<p>Procurada em: ' . $mpdfPath . '</p>';
}

echo '</div>';

// Carregar autoload
echo '<div class="box">';
echo '<h2>📥 Carregando Autoload</h2>';

try {
    require_once $autoloadPath;
    echo '<p class="success">✅ Autoload carregado com sucesso</p>';
} catch (Exception $e) {
    echo '<p class="error">❌ Erro ao carregar autoload: ' . $e->getMessage() . '</p>';
    exit;
}

echo '</div>';

// Testar PHPMailer
echo '<div class="box">';
echo '<h2>📧 PHPMailer</h2>';

if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    echo '<p class="success">✅ PHPMailer está disponível</p>';
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        echo '<p class="success">✅ Instância criada com sucesso</p>';
    } catch (Exception $e) {
        echo '<p class="error">❌ Erro ao criar instância: ' . $e->getMessage() . '</p>';
    }
} else {
    echo '<p class="error">❌ PHPMailer NÃO está disponível</p>';
}

echo '</div>';

// Testar mPDF
echo '<div class="box">';
echo '<h2>📄 mPDF</h2>';

// Debug: mostrar o que está sendo procurado
echo '<p style="color:#6c757d">Procurando classe: Mpdf\\Mpdf</p>';

if (class_exists('Mpdf\\Mpdf')) {
    echo '<p class="success">✅ mPDF está disponível</p>';
    try {
        $mpdf = new Mpdf\Mpdf(['mode' => 'utf-8']);
        echo '<p class="success">✅ Instância criada com sucesso</p>';
        echo '<p style="color:#28a745">Versão: ' . (defined('Mpdf\\VERSION') ? Mpdf\VERSION : 'Desconhecida') . '</p>';
    } catch (Exception $e) {
        echo '<p class="error">❌ Erro ao criar instância: ' . $e->getMessage() . '</p>';
    }
} else {
    echo '<p class="error">❌ mPDF NÃO está disponível</p>';
    
    // Debug adicional
    echo '<hr style="margin:1rem 0">';
    echo '<p style="color:#6c757d"><strong>Debug:</strong></p>';
    echo '<pre style="background:#f8f9fa;padding:1rem;border-radius:4px;overflow-x:auto">';
    
    // Listar classes carregadas
    $loadedClasses = get_declared_classes();
    $mpdfClasses = array_filter($loadedClasses, function($class) {
        return strpos($class, 'Mpdf') !== false;
    });
    
    if (!empty($mpdfClasses)) {
        echo "Classes Mpdf carregadas:\n";
        foreach ($mpdfClasses as $class) {
            echo "  - $class\n";
        }
    } else {
        echo "Nenhuma classe Mpdf foi carregada.\n";
    }
    
    // Verificar se o arquivo existe
    echo "\nVerificando arquivo Mpdf.php:\n";
    echo "  Caminho: $mpdfFile\n";
    echo "  Existe: " . (file_exists($mpdfFile) ? 'SIM' : 'NÃO') . "\n";
    echo "  Legível: " . (is_readable($mpdfFile) ? 'SIM' : 'NÃO') . "\n";
    
    // Tentar incluir manualmente
    echo "\nTentando incluir manualmente...\n";
    if (file_exists($mpdfFile)) {
        try {
            require_once $mpdfFile;
            echo "  ✅ Arquivo incluído!\n";
            echo "  Class exists agora: " . (class_exists('Mpdf\\Mpdf') ? 'SIM' : 'NÃO') . "\n";
        } catch (Exception $e) {
            echo "  ❌ Erro: " . $e->getMessage() . "\n";
        }
    }
    
    echo '</pre>';
}

echo '</div>';

// Resumo final
echo '<div class="box" style="background:#f8f9fa;border-left:4px solid #667eea">';
echo '<h2>📊 Resumo</h2>';

$vendorOk = file_exists($autoloadPath);
$phpmailerOk = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
$mpdfOk = class_exists('Mpdf\\Mpdf');

if ($vendorOk && $phpmailerOk && $mpdfOk) {
    echo '<p class="success" style="font-size:1.5rem">🎉 TUDO FUNCIONANDO!</p>';
    echo '<p>Você pode usar PHPMailer e mPDF no projeto.</p>';
    echo '<p><a href="teste-pdf.php" style="color:#667eea;font-weight:bold">➡️ Ir para Teste de PDF</a></p>';
} else {
    echo '<p class="error" style="font-size:1.5rem">⚠️ Alguns componentes faltando</p>';
    echo '<ul>';
    echo '<li>' . ($vendorOk ? '✅' : '❌') . ' Vendor/Autoload</li>';
    echo '<li>' . ($phpmailerOk ? '✅' : '❌') . ' PHPMailer</li>';
    echo '<li>' . ($mpdfOk ? '✅' : '❌') . ' mPDF</li>';
    echo '</ul>';
}

echo '</div>';

echo '</body></html>';
