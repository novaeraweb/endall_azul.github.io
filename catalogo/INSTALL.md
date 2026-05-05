# 🚀 Guia Rápido de Instalação - Endall Inspeções

## ⚡ Instalação em 5 Minutos

### 1️⃣ Preparar Ambiente

```bash
# Verificar requisitos
php -v          # Deve ser PHP 8.0+
mysql --version # Deve ser MySQL 8.0+
```

### 2️⃣ Configurar Banco de Dados

```sql
-- Abrir MySQL
mysql -u root -p

-- Executar script
source /caminho/para/install/setup.sql

-- Ou via phpMyAdmin:
-- Importar arquivo: install/setup.sql
```

✅ **Banco criado:** `endall_vendas`  
✅ **15 produtos de exemplo** inseridos  
✅ **Admin padrão:** admin@endall.com.br / admin123

### 3️⃣ Configurar Conexão

Edite `includes/config.php`:

```php
// Linhas 17-20
define('DB_HOST', 'localhost');
define('DB_NAME', 'endall_vendas');
define('DB_USER', 'root');           // ⚠️ Seu usuário MySQL
define('DB_PASS', '');               // ⚠️ Sua senha MySQL

// Linha 28
define('SITE_URL', 'http://localhost/vendas'); // ⚠️ Sua URL
```

### 4️⃣ Ajustar Permissões

```bash
# Linux/Mac
chmod 755 uploads/
chmod 755 uploads/pdfs uploads/produtos uploads/temp

# Windows
# Garantir que o servidor web tem permissão de escrita em uploads/
```

### 5️⃣ Configurar E-mail (Opcional mas recomendado)

Edite `includes/config.php` - Linhas 42-50:

```php
// Para Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'seu-email@gmail.com');
define('SMTP_PASS', 'sua-senha-de-aplicativo'); // ⚠️ Use senha de aplicativo do Gmail
define('SMTP_FROM_EMAIL', 'seu-email@gmail.com');

// Para outros provedores, consulte documentação
```

**Gmail - Gerar senha de aplicativo:**
1. Acesse: https://myaccount.google.com/security
2. Ative "Verificação em duas etapas"
3. Procure "Senhas de app" e gere uma nova senha
4. Use essa senha em `SMTP_PASS`

**Testar envio:**
```
http://localhost/vendas/enviar-email.php?teste=1&email=seu-email@teste.com
```

### 6️⃣ Instalar PHPMailer (Recomendado para produção)

```bash
# Via Composer (recomendado)
cd /caminho/para/projeto
composer require phpmailer/phpmailer

# Ou baixar manualmente de: https://github.com/PHPMailer/PHPMailer
```

Se o PHPMailer não estiver instalado, o sistema usará a função `mail()` nativa do PHP.

### 7️⃣ Testar

Acesse no navegador:

```
http://localhost/vendas/index.php
```

🎉 **Pronto!** Você deve ver o catálogo com 15 produtos.

---

## 🧪 Checklist de Verificação

- [ ] Página inicial carrega sem erros
- [ ] 15 produtos aparecem no catálogo
- [ ] Filtros funcionam (testar busca)
- [ ] Adicionar produto ao carrinho funciona
- [ ] Contador do carrinho atualiza
- [ ] Página de orçamento abre
- [ ] Formulário de orçamento pode ser preenchido
- [ ] Orçamento é salvo no banco (verificar tabela `orcamentos`)
- [ ] E-mails de confirmação são enviados (cliente + empresa)
- [ ] Página individual do produto funciona (produto.php?sku=MV6-1)

---

## ⚙️ Configurações Rápidas

### Alterar Dados da Empresa

`includes/config.php` - Linhas 32-37

### Ativar Debug

`includes/config.php` - Linha 75
```php
define('DEBUG_MODE', true); // Mostrar erros
```

### Alterar Limite do Carrinho

`includes/config.php` - Linha 55
```php
define('LIMITE_CARRINHO', 30); // Padrão: 20
```

---

## 🐛 Problemas Comuns

### Erro: "Banco de dados não encontrado"
**Solução:** Execute o `install/setup.sql` novamente

### Erro: "Permissão negada em uploads/"
**Solução:** `chmod 755 uploads/` (Linux/Mac)

### Página em branco
**Solução:** 
1. Ative `DEBUG_MODE = true`
2. Verifique logs do PHP
3. Verifique configuração de `DB_*` em `config.php`

### Filtros não funcionam
**Solução:** Verifique console do navegador (F12) para erros JavaScript

---

## 📦 Próximos Passos

1. **Adicionar produtos reais** (substituir os 15 exemplos)
2. ✅ **Configurar SMTP** para envio de e-mails (veja seção 5 acima)
3. **Implementar geração de PDF** (mPDF será necessário)
4. **Criar painel admin** (em desenvolvimento)
5. **Fazer deploy em servidor de produção**

### Instalar mPDF para geração de PDF

```bash
# Via Composer
composer require mpdf/mpdf

# Testar geração de PDF
# Após implementar gerar-pdf.php:
http://localhost/vendas/gerar-pdf.php?orcamento_id=1
```

---

## 📞 Suporte

**Problemas?** Entre em contato:
- E-mail: comercial@endall.com.br
- WhatsApp: (11) 98765-4321

---

*Instalação completa? Leia o [README.md](README.md) para documentação detalhada.*
