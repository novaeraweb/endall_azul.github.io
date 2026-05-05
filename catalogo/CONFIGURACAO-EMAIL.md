# 📧 Guia de Configuração de E-mail - Endall Inspeções

## 🎯 Visão Geral

O sistema envia automaticamente dois e-mails quando um orçamento é solicitado:

1. **E-mail para o Cliente**: Confirmação de recebimento com número do orçamento
2. **E-mail para a Empresa**: Notificação com dados do cliente e produtos solicitados

---

## 🔧 Configuração Básica

### Passo 1: Editar `includes/config.php`

Localize as linhas 42-50 e configure:

```php
// =============================================
// CONFIGURAÇÕES DE E-MAIL (SMTP)
// =============================================
define('SMTP_HOST', 'smtp.gmail.com');        // Servidor SMTP
define('SMTP_PORT', 587);                      // Porta (587 para TLS, 465 para SSL)
define('SMTP_SECURE', 'tls');                  // tls ou ssl
define('SMTP_USER', 'seu-email@gmail.com');    // Seu e-mail
define('SMTP_PASS', 'sua-senha-de-aplicativo'); // Senha de aplicativo
define('SMTP_FROM_NAME', 'Endall Inspeções');  // Nome do remetente
define('SMTP_FROM_EMAIL', 'seu-email@gmail.com'); // E-mail do remetente
```

---

## 📬 Configuração por Provedor

### Gmail

**Requisitos:**
- Conta Gmail ativa
- Verificação em duas etapas ativada

**Configuração:**
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'seu-email@gmail.com');
define('SMTP_PASS', 'xxxx xxxx xxxx xxxx'); // 16 caracteres da senha de app
```

**Gerar senha de aplicativo:**
1. Acesse: https://myaccount.google.com/security
2. Role até "Verificação em duas etapas" e ative
3. Volte e procure "Senhas de app"
4. Selecione "E-mail" e "Outro (nome personalizado)"
5. Digite "Endall Sistema Vendas"
6. Copie a senha de 16 caracteres e cole em `SMTP_PASS`

---

### Outlook / Hotmail

```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'seu-email@outlook.com');
define('SMTP_PASS', 'sua-senha');
```

---

### Yahoo

```php
define('SMTP_HOST', 'smtp.mail.yahoo.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'seu-email@yahoo.com');
define('SMTP_PASS', 'senha-de-app'); // Gere em: https://login.yahoo.com/account/security
```

---

### Servidor Próprio / cPanel

```php
define('SMTP_HOST', 'mail.seudominio.com.br');
define('SMTP_PORT', 587); // ou 465 para SSL
define('SMTP_SECURE', 'tls'); // ou 'ssl'
define('SMTP_USER', 'contato@seudominio.com.br');
define('SMTP_PASS', 'sua-senha-cpanel');
```

**Onde encontrar dados SMTP no cPanel:**
1. Faça login no cPanel
2. Procure "Contas de E-mail"
3. Clique em "Configurar Cliente de E-mail" ao lado da conta desejada
4. Use as informações de "Servidor de Saída (SMTP)"

---

### SendGrid (Recomendado para produção)

```php
define('SMTP_HOST', 'smtp.sendgrid.net');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'apikey'); // Sempre "apikey"
define('SMTP_PASS', 'SG.xxxxxxxxxxxxxxxxxxxxxxxx'); // Sua chave API
```

**Criar conta e obter API Key:**
1. Crie conta gratuita em: https://sendgrid.com/ (100 e-mails/dia grátis)
2. Vá em Settings > API Keys
3. Clique em "Create API Key"
4. Dê permissão "Full Access" ou "Mail Send"
5. Copie a chave e cole em `SMTP_PASS`

---

### Mailgun

```php
define('SMTP_HOST', 'smtp.mailgun.org');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'postmaster@seu-dominio.mailgun.org');
define('SMTP_PASS', 'sua-senha-smtp');
```

---

## 📦 Instalar PHPMailer

### Via Composer (Recomendado)

```bash
cd /caminho/para/projeto
composer require phpmailer/phpmailer
```

### Download Manual

1. Baixe em: https://github.com/PHPMailer/PHPMailer/releases
2. Extraia para `/vendor/phpmailer/phpmailer/`
3. Certifique-se que existe `/vendor/autoload.php`

**Nota:** Se o PHPMailer não estiver instalado, o sistema usará a função `mail()` nativa do PHP (menos confiável).

---

## 🧪 Testar Configuração

### Teste 1: E-mail de Teste Simples

Acesse no navegador:
```
http://localhost/vendas/enviar-email.php?teste=1&email=seu-email@teste.com
```

**Resposta esperada:**
```json
{
  "sucesso": true,
  "mensagem": "E-mail de teste enviado para seu-email@teste.com"
}
```

### Teste 2: Envio de Orçamento Completo

1. Adicione produtos ao carrinho
2. Preencha o formulário de orçamento
3. Clique em "Solicitar Orçamento"
4. Verifique:
   - Cliente deve receber e-mail de confirmação
   - Empresa deve receber notificação com dados do orçamento

---

## 🔍 Solução de Problemas

### Erro: "SMTP connect() failed"

**Causas comuns:**
- Credenciais incorretas
- Porta bloqueada pelo firewall
- Verificação em duas etapas não ativada (Gmail)

**Soluções:**
1. Verifique usuário e senha
2. Teste outra porta (587 → 465 ou vice-versa)
3. Ative verificação em duas etapas e gere senha de app
4. Desabilite temporariamente firewall/antivírus para testar

---

### Erro: "Could not authenticate"

**Solução:**
- Gmail: Use senha de aplicativo (não a senha normal)
- Outros: Verifique se a senha está correta
- Alguns provedores exigem "permitir aplicativos menos seguros"

---

### E-mails caem na caixa de spam

**Soluções:**
1. **Use um domínio próprio** (evite Gmail/Hotmail para empresa)
2. **Configure SPF, DKIM e DMARC** no DNS do domínio:
   ```
   TXT @ "v=spf1 include:_spf.google.com ~all"
   ```
3. **Use serviço profissional** (SendGrid, Mailgun, AWS SES)
4. **Inclua link de descadastro** nos e-mails
5. **Teste score de spam**: https://mail-tester.com

---

### Função mail() não funciona

**No servidor local (XAMPP/WAMP):**

1. Edite `php.ini`:
   ```ini
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = seu-email@gmail.com
   ```

2. Ou instale **PHPMailer** (recomendado)

**No servidor de produção:**
- Verifique se o servidor permite envio SMTP
- Alguns hosts compartilhados bloqueiam `mail()`
- Use PHPMailer com SMTP externo

---

## 📊 Logs e Monitoramento

### Ativar logs de e-mail

Edite `enviar-email.php` linha 138:
```php
$mail->SMTPDebug = 2; // 0=off, 1=client, 2=client+server, 3=connection, 4=low-level
```

**Ver logs no navegador:**
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

**Ver logs do servidor:**
```bash
# Linux
tail -f /var/log/apache2/error.log

# XAMPP Windows
C:\xampp\apache\logs\error.log
```

---

## 🎨 Personalizar Templates de E-mail

### Template do Cliente

Arquivo: `includes/email-template-cliente.php`

**Principais seções:**
- Header (logo e nome da empresa)
- Número do orçamento
- Dados do cliente
- Próximos passos
- Botões de ação (WhatsApp, Telefone)
- Footer com contatos

### Template da Empresa

Arquivo: `includes/email-template-empresa.php`

**Principais seções:**
- Alerta de novo orçamento
- Dados completos do cliente
- Mensagem do cliente
- Lista de produtos solicitados
- Ações rápidas (e-mail, WhatsApp, telefone)

**Para personalizar:**
1. Abra o arquivo PHP correspondente
2. Modifique o HTML dentro da função
3. Use variáveis disponíveis em `$dados`

---

## 🚀 Recomendações para Produção

### 1. Use um serviço SMTP profissional
- **SendGrid**: 100 e-mails/dia grátis
- **Mailgun**: 5.000 e-mails/mês grátis
- **AWS SES**: $0.10 por 1.000 e-mails

### 2. Configure DNS corretamente
```
SPF:   v=spf1 include:_spf.sendgrid.net ~all
DKIM:  (fornecido pelo provedor)
DMARC: v=DMARC1; p=quarantine; rua=mailto:dmarc@seudominio.com
```

### 3. Monitore entregas
- Configure webhook no SendGrid/Mailgun
- Salve status de entrega no banco
- Crie relatório de e-mails enviados

### 4. Implemente fila de e-mails
- Para grandes volumes, use Redis ou RabbitMQ
- Evite timeout no envio de orçamento
- Reenvie automaticamente em caso de falha

---

## 📞 Suporte

**Problemas com configuração de e-mail?**
- WhatsApp: (11) 98765-4321
- E-mail: comercial@endall.com.br

---

## ✅ Checklist Final

- [ ] SMTP configurado em `config.php`
- [ ] PHPMailer instalado via Composer
- [ ] Teste simples funcionando (enviar-email.php?teste=1)
- [ ] Cliente recebe e-mail de confirmação
- [ ] Empresa recebe notificação de novo orçamento
- [ ] E-mails não caem em spam
- [ ] Templates personalizados com logo da empresa
- [ ] DNS configurado (SPF, DKIM, DMARC) se usar domínio próprio

---

*Configuração completa? Volte para o [README.md](README.md) ou [INSTALL.md](INSTALL.md)*
