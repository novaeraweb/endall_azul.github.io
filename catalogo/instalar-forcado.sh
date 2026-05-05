#!/bin/bash

# Script de instalação forçada (ignora permissões)
# Execute com: sudo bash instalar-forcado.sh

echo "================================================"
echo "🔧 Instalação Forçada de Dependências"
echo "================================================"
echo ""

# Verificar se está como root/sudo
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Execute com sudo:"
    echo "   sudo bash instalar-forcado.sh"
    exit 1
fi

echo "✅ Executando como administrador"
echo ""

# Diretório do projeto
PROJETO_DIR="/Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto"

# Verificar se o diretório existe
if [ ! -d "$PROJETO_DIR" ]; then
    echo "❌ Diretório não encontrado: $PROJETO_DIR"
    exit 1
fi

cd "$PROJETO_DIR" || exit 1

echo "📂 Diretório: $PROJETO_DIR"
echo ""

# Criar diretório vendor
echo "📁 Criando diretório vendor..."
mkdir -p vendor
chmod 755 vendor

# Baixar PHPMailer
echo "📧 Baixando PHPMailer v6.9.1..."
curl -sL https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.tar.gz -o /tmp/phpmailer.tar.gz

if [ $? -eq 0 ]; then
    mkdir -p vendor/phpmailer
    tar -xzf /tmp/phpmailer.tar.gz -C /tmp/
    mv /tmp/PHPMailer-6.9.1 vendor/phpmailer/phpmailer
    rm /tmp/phpmailer.tar.gz
    echo "✅ PHPMailer instalado"
else
    echo "❌ Erro ao baixar PHPMailer"
fi

echo ""

# Baixar mPDF
echo "📄 Baixando mPDF v8.2.0..."
curl -sL https://github.com/mpdf/mpdf/archive/refs/tags/v8.2.0.tar.gz -o /tmp/mpdf.tar.gz

if [ $? -eq 0 ]; then
    mkdir -p vendor/mpdf
    tar -xzf /tmp/mpdf.tar.gz -C /tmp/
    mv /tmp/mpdf-8.2.0 vendor/mpdf/mpdf
    rm /tmp/mpdf.tar.gz
    
    # mPDF precisa de dependências adicionais
    mkdir -p vendor/mpdf/mpdf/vendor
    
    echo "✅ mPDF instalado"
else
    echo "❌ Erro ao baixar mPDF"
fi

echo ""

# Criar autoload.php simplificado
echo "📝 Criando autoload.php..."
cat > vendor/autoload.php << 'AUTOLOAD'
<?php
/**
 * Autoload simplificado para PHPMailer e mPDF
 * Criado automaticamente pelo script de instalação
 */

// PHPMailer
spl_autoload_register(function ($class) {
    if (strpos($class, 'PHPMailer\\PHPMailer\\') === 0) {
        $file = __DIR__ . '/phpmailer/phpmailer/src/' . str_replace('\\', '/', substr($class, 20)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    if (strpos($class, 'PHPMailer\\') === 0) {
        $classPath = str_replace('PHPMailer\\PHPMailer\\', '', $class);
        $file = __DIR__ . '/phpmailer/phpmailer/src/' . $classPath . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // mPDF
    if (strpos($class, 'Mpdf\\') === 0) {
        $file = __DIR__ . '/mpdf/mpdf/src/' . str_replace('\\', '/', substr($class, 5)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
});

// Verificar instalação
if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer', false)) {
    if (file_exists(__DIR__ . '/phpmailer/phpmailer/src/PHPMailer.php')) {
        require_once __DIR__ . '/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/phpmailer/phpmailer/src/Exception.php';
        require_once __DIR__ . '/phpmailer/phpmailer/src/SMTP.php';
    }
}

if (!class_exists('Mpdf\\Mpdf', false)) {
    if (file_exists(__DIR__ . '/mpdf/mpdf/src/Mpdf.php')) {
        require_once __DIR__ . '/mpdf/mpdf/src/Mpdf.php';
    }
}
AUTOLOAD

chmod 644 vendor/autoload.php
echo "✅ autoload.php criado"
echo ""

# Ajustar permissões de tudo
echo "🔐 Ajustando permissões..."
chown -R admin:staff vendor/
chmod -R 755 vendor/
echo "✅ Permissões ajustadas"
echo ""

# Criar diretórios necessários
echo "📁 Criando diretórios de upload..."
mkdir -p uploads/pdfs uploads/produtos uploads/temp
chmod -R 755 uploads/
chown -R admin:staff uploads/
echo "✅ Diretórios criados"
echo ""

# Verificar instalação
echo "================================================"
echo "🔍 Verificando Instalação"
echo "================================================"
echo ""

if [ -d "vendor/phpmailer/phpmailer" ]; then
    echo "✅ PHPMailer: INSTALADO"
else
    echo "❌ PHPMailer: FALTANDO"
fi

if [ -d "vendor/mpdf/mpdf" ]; then
    echo "✅ mPDF: INSTALADO"
else
    echo "❌ mPDF: FALTANDO"
fi

if [ -f "vendor/autoload.php" ]; then
    echo "✅ autoload.php: CRIADO"
else
    echo "❌ autoload.php: FALTANDO"
fi

echo ""
echo "================================================"
echo "🎉 INSTALAÇÃO CONCLUÍDA!"
echo "================================================"
echo ""
echo "📋 Próximos passos:"
echo "1. Acesse: http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php"
echo "2. Verifique se todos os status estão ✅"
echo "3. Clique em 'Gerar PDF' em um orçamento"
echo ""
echo "📂 Estrutura instalada:"
echo "   • vendor/phpmailer/phpmailer/"
echo "   • vendor/mpdf/mpdf/"
echo "   • vendor/autoload.php"
echo "   • uploads/pdfs/ (para PDFs gerados)"
echo ""
