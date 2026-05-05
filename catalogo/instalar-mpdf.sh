#!/bin/bash

# Script para instalar APENAS o mPDF
# Execute com: sudo bash instalar-mpdf.sh

echo "================================================"
echo "📄 Instalando mPDF"
echo "================================================"
echo ""

# Verificar se está como root/sudo
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Execute com sudo:"
    echo "   sudo bash instalar-mpdf.sh"
    exit 1
fi

# Diretório do projeto
PROJETO_DIR="/Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto"

cd "$PROJETO_DIR" || exit 1

echo "📂 Diretório: $PROJETO_DIR"
echo ""

# Criar diretório vendor/mpdf se não existir
mkdir -p vendor/mpdf

# Baixar mPDF
echo "📄 Baixando mPDF v8.2.4..."
curl -sL https://github.com/mpdf/mpdf/archive/refs/tags/v8.2.4.tar.gz -o /tmp/mpdf.tar.gz

if [ $? -eq 0 ]; then
    echo "✅ Download concluído"
    echo "📦 Extraindo arquivos..."
    
    # Extrair
    tar -xzf /tmp/mpdf.tar.gz -C /tmp/
    
    # Remover instalação antiga se existir
    rm -rf vendor/mpdf/mpdf
    
    # Mover para vendor
    mv /tmp/mpdf-8.2.4 vendor/mpdf/mpdf
    
    # Limpar
    rm /tmp/mpdf.tar.gz
    
    echo "✅ mPDF extraído"
else
    echo "❌ Erro ao baixar mPDF"
    exit 1
fi

echo ""

# Baixar dependências essenciais do mPDF
echo "📥 Baixando dependências do mPDF..."

# setasign/fpdi
mkdir -p vendor/setasign/fpdi
curl -sL https://github.com/Setasign/FPDI/archive/refs/tags/v2.6.0.tar.gz -o /tmp/fpdi.tar.gz
tar -xzf /tmp/fpdi.tar.gz -C /tmp/
mv /tmp/FPDI-2.6.0 vendor/setasign/fpdi
rm /tmp/fpdi.tar.gz
echo "✅ FPDI instalado"

# psr/log
mkdir -p vendor/psr/log
curl -sL https://github.com/php-fig/log/archive/refs/tags/3.0.0.tar.gz -o /tmp/psr-log.tar.gz
tar -xzf /tmp/psr-log.tar.gz -C /tmp/
mv /tmp/log-3.0.0 vendor/psr/log
rm /tmp/psr-log.tar.gz
echo "✅ PSR Log instalado"

echo ""

# Atualizar autoload.php
echo "📝 Atualizando autoload.php..."

cat > vendor/autoload.php << 'AUTOLOAD'
<?php
/**
 * Autoload para PHPMailer e mPDF
 * Com todas as dependências necessárias
 */

// Diretório base do vendor
$vendorDir = __DIR__;

// PHPMailer
spl_autoload_register(function ($class) use ($vendorDir) {
    if (strpos($class, 'PHPMailer\\PHPMailer\\') === 0) {
        $file = $vendorDir . '/phpmailer/phpmailer/src/' . str_replace('\\', '/', substr($class, 20)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
});

// mPDF e suas dependências
spl_autoload_register(function ($class) use ($vendorDir) {
    // mPDF principal
    if (strpos($class, 'Mpdf\\') === 0) {
        $file = $vendorDir . '/mpdf/mpdf/src/' . str_replace('\\', '/', substr($class, 5)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // setasign/fpdi
    if (strpos($class, 'setasign\\Fpdi\\') === 0) {
        $file = $vendorDir . '/setasign/fpdi/src/' . str_replace('\\', '/', substr($class, 14)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Psr\Log
    if (strpos($class, 'Psr\\Log\\') === 0) {
        $file = $vendorDir . '/psr/log/src/' . str_replace('\\', '/', substr($class, 8)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
});

// Carregar classes principais diretamente
if (file_exists($vendorDir . '/phpmailer/phpmailer/src/PHPMailer.php')) {
    require_once $vendorDir . '/phpmailer/phpmailer/src/PHPMailer.php';
    require_once $vendorDir . '/phpmailer/phpmailer/src/Exception.php';
    require_once $vendorDir . '/phpmailer/phpmailer/src/SMTP.php';
}

// Verificar se mPDF está disponível
if (!class_exists('Mpdf\\Mpdf')) {
    if (file_exists($vendorDir . '/mpdf/mpdf/src/Mpdf.php')) {
        // Carregar dependências do mPDF primeiro
        if (file_exists($vendorDir . '/psr/log/src/LoggerInterface.php')) {
            require_once $vendorDir . '/psr/log/src/LoggerInterface.php';
            require_once $vendorDir . '/psr/log/src/AbstractLogger.php';
            require_once $vendorDir . '/psr/log/src/NullLogger.php';
        }
        
        // Agora carregar mPDF
        require_once $vendorDir . '/mpdf/mpdf/src/Mpdf.php';
    }
}
AUTOLOAD

chmod 644 vendor/autoload.php
echo "✅ autoload.php atualizado"
echo ""

# Ajustar permissões
echo "🔐 Ajustando permissões..."
chown -R admin:staff vendor/
chmod -R 755 vendor/
echo "✅ Permissões ajustadas"
echo ""

# Verificar instalação
echo "================================================"
echo "🔍 Verificando Instalação"
echo "================================================"
echo ""

if [ -d "vendor/mpdf/mpdf/src" ]; then
    echo "✅ mPDF: INSTALADO"
    echo "   📂 vendor/mpdf/mpdf/src/"
else
    echo "❌ mPDF: FALTANDO"
fi

if [ -d "vendor/setasign/fpdi" ]; then
    echo "✅ FPDI (dependência): INSTALADO"
else
    echo "⚠️  FPDI: FALTANDO (não crítico)"
fi

if [ -d "vendor/psr/log" ]; then
    echo "✅ PSR Log (dependência): INSTALADO"
else
    echo "⚠️  PSR Log: FALTANDO (não crítico)"
fi

if [ -f "vendor/autoload.php" ]; then
    echo "✅ autoload.php: ATUALIZADO"
else
    echo "❌ autoload.php: FALTANDO"
fi

echo ""
echo "================================================"
echo "🎉 INSTALAÇÃO CONCLUÍDA!"
echo "================================================"
echo ""
echo "📋 Próximos passos:"
echo "1. Recarregue: http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php"
echo "2. Deve mostrar: ✅ mPDF: Instalado"
echo "3. Clique em 'Gerar PDF'"
echo ""
