#!/bin/bash

# Script para download manual das bibliotecas
# Execute com: bash download-libs.sh

echo "================================================"
echo "📥 Download Manual de Bibliotecas"
echo "================================================"
echo ""

# Criar diretório vendor se não existir
mkdir -p vendor

# PHPMailer
echo "📧 Baixando PHPMailer..."
curl -L https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.tar.gz -o phpmailer.tar.gz
mkdir -p vendor/phpmailer
tar -xzf phpmailer.tar.gz -C vendor/phpmailer
mv vendor/phpmailer/PHPMailer-6.9.1 vendor/phpmailer/phpmailer
rm phpmailer.tar.gz
echo "✅ PHPMailer baixado"
echo ""

# mPDF
echo "📄 Baixando mPDF..."
curl -L https://github.com/mpdf/mpdf/archive/refs/tags/v8.1.0.tar.gz -o mpdf.tar.gz
mkdir -p vendor/mpdf
tar -xzf mpdf.tar.gz -C vendor/mpdf
mv vendor/mpdf/mpdf-8.1.0 vendor/mpdf/mpdf
rm mpdf.tar.gz
echo "✅ mPDF baixado"
echo ""

# Criar autoload.php
echo "📝 Criando autoload.php..."
cat > vendor/autoload.php << 'EOF'
<?php
// Autoload manual

spl_autoload_register(function ($class) {
    // PHPMailer
    if (strpos($class, 'PHPMailer\\') === 0) {
        $file = __DIR__ . '/phpmailer/phpmailer/src/' . str_replace('\\', '/', substr($class, 11)) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    
    // mPDF
    if (strpos($class, 'Mpdf\\') === 0) {
        $file = __DIR__ . '/mpdf/mpdf/src/' . str_replace('\\', '/', substr($class, 5)) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
});
EOF

echo "✅ autoload.php criado"
echo ""

echo "================================================"
echo "✅ CONCLUÍDO!"
echo "================================================"
echo ""
echo "Bibliotecas instaladas em:"
echo "  • vendor/phpmailer/phpmailer/"
echo "  • vendor/mpdf/mpdf/"
echo "  • vendor/autoload.php"
echo ""
echo "Teste em: http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php"
