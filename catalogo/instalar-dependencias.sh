#!/bin/bash

# Script para instalar dependências do projeto Endall
# Execute com: bash instalar-dependencias.sh

echo "================================================"
echo "📦 Instalando Dependências - Endall Inspeções"
echo "================================================"
echo ""

# Verificar se está no diretório correto
if [ ! -f "composer.json" ]; then
    echo "❌ ERRO: composer.json não encontrado!"
    echo "Execute este script no diretório do projeto."
    exit 1
fi

echo "✅ Diretório correto identificado"
echo ""

# Verificar se o Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ ERRO: Composer não está instalado!"
    echo ""
    echo "Instale o Composer primeiro:"
    echo "  macOS: brew install composer"
    echo "  Linux: apt-get install composer"
    echo "  Manual: https://getcomposer.org"
    exit 1
fi

echo "✅ Composer está instalado"
echo "   Versão: $(composer --version | head -n1)"
echo ""

# Remover vendor antigo (se existir)
if [ -d "vendor" ]; then
    echo "🗑️  Removendo pasta vendor antiga..."
    rm -rf vendor
    echo "✅ Pasta vendor removida"
    echo ""
fi

# Instalar dependências
echo "📥 Instalando dependências via Composer..."
echo ""
composer install --optimize-autoloader

# Verificar se a instalação foi bem-sucedida
if [ $? -eq 0 ]; then
    echo ""
    echo "================================================"
    echo "✅ SUCESSO! Dependências instaladas:"
    echo "================================================"
    echo ""
    
    # Verificar PHPMailer
    if [ -d "vendor/phpmailer/phpmailer" ]; then
        echo "✅ PHPMailer instalado"
    else
        echo "⚠️  PHPMailer NÃO instalado"
    fi
    
    # Verificar mPDF
    if [ -d "vendor/mpdf/mpdf" ]; then
        echo "✅ mPDF instalado"
    else
        echo "⚠️  mPDF NÃO instalado"
    fi
    
    echo ""
    echo "================================================"
    echo "🎉 Instalação concluída!"
    echo "================================================"
    echo ""
    echo "Próximos passos:"
    echo "1. Acesse: http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php"
    echo "2. Verifique se todos os status estão ✅"
    echo "3. Clique em 'Gerar PDF' em um orçamento"
    echo ""
    
else
    echo ""
    echo "================================================"
    echo "❌ ERRO durante a instalação!"
    echo "================================================"
    echo ""
    echo "Tente executar manualmente:"
    echo "  composer install"
    echo ""
    exit 1
fi
