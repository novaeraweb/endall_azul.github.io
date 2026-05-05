#!/bin/bash

# Criar autoload.php melhorado
# Execute com: sudo bash criar-autoload.sh

echo "================================================"
echo "📝 Criando Autoload Melhorado"
echo "================================================"
echo ""

# Verificar se está como root/sudo
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Execute com sudo:"
    echo "   sudo bash criar-autoload.sh"
    exit 1
fi

# Diretório do projeto
PROJETO_DIR="/Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto"

cd "$PROJETO_DIR" || exit 1

# Criar autoload.php completo
cat > vendor/autoload.php << 'AUTOLOAD'
<?php
/**
 * Autoload Manual para PHPMailer e mPDF
 * Versão: 2.0 - Com carregamento forçado
 */

$vendorDir = __DIR__;

// ====================
// PHPMailer
// ====================
if (file_exists($vendorDir . '/phpmailer/phpmailer/src/PHPMailer.php')) {
    require_once $vendorDir . '/phpmailer/phpmailer/src/Exception.php';
    require_once $vendorDir . '/phpmailer/phpmailer/src/PHPMailer.php';
    require_once $vendorDir . '/phpmailer/phpmailer/src/SMTP.php';
}

// ====================
// mPDF - Carregamento Forçado
// ====================

// 1. Verificar se o diretório existe
$mpdfDir = $vendorDir . '/mpdf/mpdf/src';

if (is_dir($mpdfDir)) {
    // 2. Definir constantes necessárias
    if (!defined('_MPDF_TEMP_PATH')) {
        define('_MPDF_TEMP_PATH', dirname($vendorDir) . '/uploads/temp/');
    }
    
    if (!defined('_MPDF_TTFONTDATAPATH')) {
        define('_MPDF_TTFONTDATAPATH', dirname($vendorDir) . '/uploads/temp/');
    }
    
    // 3. Carregar classes principais do mPDF na ordem correta
    $mpdfFiles = [
        // Interfaces primeiro
        $mpdfDir . '/ServiceFactory.php',
        $mpdfDir . '/Barcode.php',
        $mpdfDir . '/Color/ColorConverter.php',
        $mpdfDir . '/Css/Border.php',
        $mpdfDir . '/Css/DefaultCss.php',
        $mpdfDir . '/Css/TextVars.php',
        $mpdfDir . '/File/LocalContentLoader.php',
        $mpdfDir . '/Fonts/FontCache.php',
        $mpdfDir . '/Fonts/FontFileFinder.php',
        $mpdfDir . '/Form.php',
        $mpdfDir . '/Gif/ColorTable.php',
        $mpdfDir . '/Gif/FileHeader.php',
        $mpdfDir . '/Gif/Image.php',
        $mpdfDir . '/Gif/ImageDescriptor.php',
        $mpdfDir . '/Gif/Lzw.php',
        $mpdfDir . '/Http/Request.php',
        $mpdfDir . '/Image/ImageProcessor.php',
        $mpdfDir . '/Language/LanguageToFont.php',
        $mpdfDir . '/Language/ScriptToLanguage.php',
        $mpdfDir . '/Mpdf.php', // Classe principal por último
    ];
    
    foreach ($mpdfFiles as $file) {
        if (file_exists($file)) {
            require_once $file;
        }
    }
    
    // 4. Autoloader dinâmico para classes restantes
    spl_autoload_register(function ($class) use ($mpdfDir) {
        if (strpos($class, 'Mpdf\\') === 0) {
            $classPath = str_replace('\\', '/', substr($class, 5));
            $file = $mpdfDir . '/' . $classPath . '.php';
            
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
        return false;
    });
}

// ====================
// Autoloader Genérico
// ====================
spl_autoload_register(function ($class) use ($vendorDir) {
    // PHPMailer
    if (strpos($class, 'PHPMailer\\') === 0) {
        $file = $vendorDir . '/phpmailer/phpmailer/src/' . str_replace('\\', '/', substr($class, 11)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    return false;
});

// ====================
// Verificação Final
// ====================
if (!class_exists('Mpdf\\Mpdf', false)) {
    // Última tentativa: carregar Mpdf.php diretamente
    $mpdfFile = $vendorDir . '/mpdf/mpdf/src/Mpdf.php';
    if (file_exists($mpdfFile) && !class_exists('Mpdf\\Mpdf')) {
        @include_once $mpdfFile;
    }
}

// Debug (comentar em produção)
// error_log('Autoload carregado - Mpdf existe: ' . (class_exists('Mpdf\\Mpdf') ? 'SIM' : 'NÃO'));
AUTOLOAD

chmod 644 vendor/autoload.php
chown admin:staff vendor/autoload.php

echo "✅ autoload.php criado"
echo ""

# Criar diretórios necessários
mkdir -p uploads/temp
chmod 755 uploads/temp
chown admin:staff uploads/temp

echo "✅ Diretórios criados"
echo ""

# Verificar estrutura
echo "================================================"
echo "🔍 Verificando Estrutura"
echo "================================================"
echo ""

if [ -f "vendor/autoload.php" ]; then
    echo "✅ autoload.php: CRIADO"
    echo "   Tamanho: $(wc -c < vendor/autoload.php) bytes"
else
    echo "❌ autoload.php: ERRO"
fi

if [ -f "vendor/mpdf/mpdf/src/Mpdf.php" ]; then
    echo "✅ Mpdf.php: ENCONTRADO"
    echo "   Caminho: vendor/mpdf/mpdf/src/Mpdf.php"
else
    echo "❌ Mpdf.php: NÃO ENCONTRADO"
fi

if [ -d "uploads/temp" ]; then
    echo "✅ Temp directory: CRIADO"
else
    echo "❌ Temp directory: FALTANDO"
fi

echo ""
echo "================================================"
echo "🎉 AUTOLOAD CRIADO!"
echo "================================================"
echo ""
echo "📋 Próximos passos:"
echo "1. Recarregue: http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php"
echo "2. Deve mostrar: ✅ mPDF: Instalado"
echo "3. Se ainda não funcionar, me envie um print"
echo ""
