# 📦 Instalação de Dependências - Guia Completo

## ❌ Problema Identificado

```
Vendor (Composer): NÃO INSTALADO
```

As bibliotecas PHP (PHPMailer e mPDF) não estão instaladas.

---

## ✅ Solução (3 Métodos)

### **Método 1: Script Automático (RECOMENDADO)**

#### **Passo 1: Abra o Terminal**
```bash
# Vá para a pasta do projeto
cd /Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto
```

#### **Passo 2: Execute o script**
```bash
bash instalar-dependencias.sh
```

#### **Resultado esperado:**
```
✅ SUCESSO! Dependências instaladas:
✅ PHPMailer instalado
✅ mPDF instalado
🎉 Instalação concluída!
```

---

### **Método 2: Comando Manual**

```bash
# Vá para a pasta do projeto
cd /Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto

# Execute o Composer
composer install
```

**OU** se não funcionar:

```bash
composer install --no-scripts --optimize-autoloader
```

---

### **Método 3: Instalar Individualmente**

Se o `composer install` falhar, instale cada biblioteca:

```bash
# PHPMailer
composer require phpmailer/phpmailer

# mPDF
composer require mpdf/mpdf
```

---

## ⚠️ Se o Composer Não Estiver Instalado

### **No macOS:**

#### **Opção 1: Homebrew (Recomendado)**
```bash
brew install composer
```

#### **Opção 2: Download Manual**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
rm composer-setup.php
```

### **Verificar instalação:**
```bash
composer --version
```

Deve mostrar algo como:
```
Composer version 2.6.5 2023-10-06 10:34:00
```

---

## 🧪 Teste Após Instalação

### **1️⃣ Verifique se a pasta vendor existe:**
```bash
ls -la vendor/
```

Deve mostrar:
```
drwxr-xr-x  phpmailer/
drwxr-xr-x  mpdf/
-rw-r--r--  autoload.php
```

### **2️⃣ Acesse a página de teste:**
```
http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php
```

Deve mostrar:
```
✅ Vendor (Composer): Instalado
✅ mPDF: Instalado
✅ Diretório de PDFs: Existe
```

### **3️⃣ Gere um PDF:**
- Clique em **"📄 Gerar PDF"** em um orçamento
- O PDF deve baixar automaticamente

---

## 📂 Estrutura Esperada

Após a instalação, você deve ter:

```
projeto/
├── vendor/              ← NOVA PASTA
│   ├── phpmailer/
│   │   └── phpmailer/
│   ├── mpdf/
│   │   └── mpdf/
│   ├── composer/
│   └── autoload.php
├── composer.json        ← Já existe
├── composer.lock        ← Será criado
├── gerar-pdf.php
├── enviar-email.php
└── ...
```

---

## 🔍 Troubleshooting

### **Erro: "composer: command not found"**

**Causa:** Composer não está instalado

**Solução:**
```bash
# macOS
brew install composer

# OU download manual (veja acima)
```

---

### **Erro: "Your requirements could not be resolved"**

**Causa:** Conflito de versões ou PHP incompatível

**Solução 1: Verificar versão do PHP**
```bash
php -v
```

Deve ser **PHP 8.0** ou superior.

**Solução 2: Atualizar Composer**
```bash
composer self-update
composer update
```

**Solução 3: Ignorar requisitos de plataforma**
```bash
composer install --ignore-platform-reqs
```

---

### **Erro: "Failed to download"**

**Causa:** Problema de conexão com o repositório

**Solução:**
```bash
# Limpar cache
composer clear-cache

# Tentar novamente
composer install
```

---

### **Erro: "Memory limit exceeded"**

**Causa:** Composer precisa de mais memória

**Solução:**
```bash
php -d memory_limit=-1 $(which composer) install
```

---

### **Erro: "vendor/autoload.php not found" (mesmo após instalação)**

**Causa:** Instalação incompleta

**Solução:**
```bash
# Remover tudo e reinstalar
rm -rf vendor composer.lock
composer install
```

---

## ✅ Verificação Final

Execute este comando para verificar tudo:

```bash
php -r "
require 'vendor/autoload.php';
echo '✅ Autoload OK\n';
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo '✅ PHPMailer OK\n';
} else {
    echo '❌ PHPMailer FALTANDO\n';
}
if (class_exists('Mpdf\Mpdf')) {
    echo '✅ mPDF OK\n';
} else {
    echo '❌ mPDF FALTANDO\n';
}
"
```

**Resultado esperado:**
```
✅ Autoload OK
✅ PHPMailer OK
✅ mPDF OK
```

---

## 🚀 Depois de Instalar

1. ✅ Atualize a página: http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php
2. ✅ Todos os status devem estar ✅ verde
3. ✅ Clique em "Gerar PDF" em um orçamento
4. ✅ PDF deve baixar automaticamente
5. 📸 Me envie um print do PDF gerado!

---

## 📞 Se Ainda Não Funcionar

Me envie:
1. **Print do erro** exibido no terminal
2. **Resultado do comando:**
   ```bash
   php -v
   composer --version
   ```
3. **Print da página teste-pdf.php** após a instalação

---

## 📊 Resumo

| Arquivo | Tamanho Aproximado | Descrição |
|---------|-------------------|-----------|
| `vendor/` | ~50 MB | Bibliotecas PHP |
| `phpmailer/` | ~300 KB | Envio de e-mails |
| `mpdf/` | ~40 MB | Geração de PDFs |
| `composer.lock` | ~100 KB | Controle de versões |

**Tempo de instalação:** 1-3 minutos (dependendo da internet)

---

**Próximo Passo:** Execute o comando e me diga o resultado! 🚀
