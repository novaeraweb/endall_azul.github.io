# 📧 CONFIGURAÇÕES SMTP - UMBLER

## ✅ CONFIGURAÇÃO ATUAL

```php
define('SMTP_HOST', 'smtp.umbler.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'contato@novaeraweb.com.br');
define('SMTP_PASS', 'em*NEW010203');
```

---

## 🔧 OPÇÕES DE CONFIGURAÇÃO PARA TESTAR

### **OPÇÃO 1: TLS (Porta 587)** - ATUAL
```php
define('SMTP_HOST', 'smtp.umbler.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'contato@novaeraweb.com.br');
define('SMTP_PASS', 'em*NEW010203');
```

---

### **OPÇÃO 2: SSL (Porta 465)**
```php
define('SMTP_HOST', 'smtp.umbler.com');
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');
define('SMTP_USER', 'contato@novaeraweb.com.br');
define('SMTP_PASS', 'em*NEW010203');
```

---

### **OPÇÃO 3: TLS (Porta 25)** - Menos comum
```php
define('SMTP_HOST', 'smtp.umbler.com');
define('SMTP_PORT', 25);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'contato@novaeraweb.com.br');
define('SMTP_PASS', 'em*NEW010203');
```

---

## 🔍 COMO VERIFICAR NO PAINEL UMBLER

### **Passo 1: Acessar Painel**
1. Faça login em: https://www.umbler.com/br/
2. Vá em **E-mail** ou **Contas de E-mail**

### **Passo 2: Verificar Configurações SMTP**
Procure por:
- **Servidor SMTP**: deve ser `smtp.umbler.com` ou similar
- **Porta SMTP**: 587 (TLS) ou 465 (SSL)
- **Autenticação**: Sim (usar usuário e senha)
- **Criptografia**: TLS ou SSL

### **Passo 3: Confirmar Credenciais**
- **E-mail completo**: contato@novaeraweb.com.br
- **Senha**: (a que você definiu no painel)

---

## ⚠️ POSSÍVEIS PROBLEMAS

### **Problema 1: Host Incorreto**
- Umbler pode usar: `smtp.umbler.com`, `smtp.umbler.net` ou específico do plano
- **Solução**: Verificar no painel ou documentação

### **Problema 2: Porta Bloqueada**
- Servidor local pode bloquear portas SMTP
- **Solução**: Testar porta 587 e 465

### **Problema 3: Autenticação Falhou**
- Senha incorreta ou e-mail não criado
- **Solução**: Resetar senha no painel Umbler

### **Problema 4: Limite de Envio**
- Umbler pode ter limite de envios por hora
- **Solução**: Verificar cotas no painel

---

## 🧪 TESTE DE CONEXÃO SMTP

Vou criar um script para **testar a conexão SMTP** diretamente:

```php
// teste-smtp.php
<?php
require_once 'includes/config.php';

echo "Testando conexão SMTP...\n\n";
echo "Host: " . SMTP_HOST . "\n";
echo "Porta: " . SMTP_PORT . "\n";
echo "Segurança: " . SMTP_SECURE . "\n";
echo "Usuário: " . SMTP_USER . "\n";
echo "Senha: " . str_repeat('*', strlen(SMTP_PASS)) . "\n\n";

// Testar conexão socket
$errno = 0;
$errstr = '';
$timeout = 10;

$socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, $timeout);

if ($socket) {
    echo "✅ Conexão estabelecida com sucesso!\n";
    echo "Resposta do servidor:\n";
    echo fgets($socket, 512) . "\n";
    fclose($socket);
} else {
    echo "❌ ERRO ao conectar!\n";
    echo "Código: $errno\n";
    echo "Mensagem: $errstr\n\n";
    echo "Possíveis causas:\n";
    echo "- Host incorreto\n";
    echo "- Porta bloqueada\n";
    echo "- Firewall bloqueando\n";
}
?>
```

---

## 📊 CHECKLIST DE VERIFICAÇÃO

- [ ] Host SMTP correto (smtp.umbler.com)?
- [ ] Porta correta (587 ou 465)?
- [ ] Tipo de segurança (TLS ou SSL)?
- [ ] E-mail existe no painel Umbler?
- [ ] Senha está correta?
- [ ] E-mail está ativo (não suspenso)?
- [ ] Limite de envio não foi atingido?
- [ ] Autenticação SMTP está habilitada?

---

## 🔗 LINKS ÚTEIS

- **Painel Umbler**: https://www.umbler.com/br/painel
- **Suporte Umbler**: https://help.umbler.com/hc/pt-br
- **Docs SMTP Umbler**: https://help.umbler.com/hc/pt-br/articles/360000051426

---

## 🆘 SE NADA FUNCIONAR

Se após testar todas as opções ainda não funcionar:

1. **Entre em contato com o suporte da Umbler**
2. Pergunte:
   - Qual o servidor SMTP correto?
   - Qual porta usar?
   - TLS ou SSL?
   - Há alguma restrição de envio?

3. **Alternativa temporária**:
   - Usar função `mail()` do PHP (sem SMTP)
   - Configurar outro provedor (Gmail, SendGrid, etc.)

---

**Próximo passo**: Verifique no painel Umbler e me informe qual é a configuração correta!
