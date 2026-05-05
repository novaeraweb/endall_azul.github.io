# ✅ SMTP Configurado - Endall Inspeções

## 🎯 Configuração Aplicada

**Servidor SMTP:** smtp.umbler.com  
**Porta:** 587  
**Segurança:** TLS  
**Usuário:** contato@novaeraweb.com.br  
**Remetente:** Endall Inspeções <contato@novaeraweb.com.br>

---

## 🧪 Como Testar

### Teste 1: E-mail Simples

Acesse no navegador:
```
http://localhost:8888/Endall/catalogo/projeto/enviar-email.php?teste=1&email=SEU-EMAIL@AQUI.com
```

**Substitua** `SEU-EMAIL@AQUI.com` por um e-mail real que você tenha acesso.

**Resposta esperada:**
```json
{
  "sucesso": true,
  "mensagem": "E-mail de teste enviado para SEU-EMAIL@AQUI.com"
}
```

---

### Teste 2: Fluxo Completo de Orçamento

1. Acesse: `http://localhost:8888/Endall/catalogo/projeto/`
2. Adicione produtos ao carrinho
3. Clique no ícone do carrinho (canto superior direito)
4. Clique em "Finalizar Orçamento"
5. Preencha o formulário com **seu e-mail real**
6. Clique em "Solicitar Orçamento"

**Resultado esperado:**
- ✅ Você receberá um e-mail de confirmação
- ✅ O e-mail `comercial@endall.com.br` receberá notificação do orçamento
- ✅ Página exibirá "Orçamento Enviado com Sucesso!"

---

## 📧 E-mails que Serão Enviados

### Para o Cliente:
- **Assunto:** Orçamento #XXXX - Endall Inspeções
- **Conteúdo:**
  - Confirmação de recebimento
  - Número do orçamento
  - Dados do cliente
  - Produtos solicitados
  - Botões de ação (WhatsApp, Telefone)

### Para a Empresa:
- **Assunto:** 🔔 Novo Orçamento Recebido - #XXXX - Nome do Cliente
- **Conteúdo:**
  - Alerta de novo orçamento
  - Dados completos do cliente
  - Mensagem do cliente
  - Tabela de produtos solicitados
  - Ações rápidas (E-mail, WhatsApp, Ligar)

---

## 🔧 Verificar Configuração

Se quiser verificar se está tudo correto, abra o arquivo:

```
includes/config.php
```

Linhas 43-52 devem estar assim:

```php
// =============================================
// CONFIGURAÇÕES DE E-MAIL (SMTP)
// =============================================
define('SMTP_HOST', 'smtp.umbler.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'contato@novaeraweb.com.br');
define('SMTP_PASS', 'em*NEW010203');
define('SMTP_FROM_NAME', 'Endall Inspeções');
define('SMTP_FROM_EMAIL', 'contato@novaeraweb.com.br');
```

---

## 🚨 Solução de Problemas

### Erro: "SMTP connect() failed"

**Possíveis causas:**
1. Servidor SMTP bloqueado pelo firewall
2. Porta 587 bloqueada

**Soluções:**
- Tente trocar a porta para 465 e `SMTP_SECURE` para `'ssl'`
- Desabilite temporariamente o firewall para testar
- Verifique se o servidor local permite conexões SMTP

---

### Erro: "Could not authenticate"

**Solução:**
- Verifique se a senha está correta: `em*NEW010203`
- Confirme com a Umbler se o e-mail `contato@novaeraweb.com.br` está ativo

---

### E-mails caem na caixa de spam

**Soluções:**
1. Configure SPF no DNS do domínio `novaeraweb.com.br`:
   ```
   TXT @ "v=spf1 include:_spf.umbler.com ~all"
   ```

2. Marque o e-mail como "não é spam" na primeira vez

3. Adicione `contato@novaeraweb.com.br` aos contatos

---

### PHPMailer não instalado

Se aparecer erro sobre PHPMailer:

```bash
cd /caminho/para/projeto
composer install
```

**Ou baixe manualmente:**
https://github.com/PHPMailer/PHPMailer/releases

---

## ✅ Checklist de Teste

- [ ] Teste 1 executado com sucesso
- [ ] E-mail de teste recebido na caixa de entrada
- [ ] Fluxo completo testado (adicionar produtos → orçamento)
- [ ] Cliente recebe e-mail de confirmação
- [ ] Empresa recebe notificação com dados do orçamento
- [ ] E-mails não caem em spam
- [ ] Templates estão com visual correto (logo, cores Endall)

---

## 📊 Status

✅ **SMTP Configurado**  
✅ **Templates Criados**  
✅ **Integração Ativa**  
⏳ **Aguardando Teste**

---

## 🎯 Próximo Passo

**Execute o Teste 1 agora:**

```
http://localhost:8888/Endall/catalogo/projeto/enviar-email.php?teste=1&email=seu-email@teste.com
```

Se o teste 1 funcionar, o sistema está 100% operacional! 🎉

---

## 📞 Suporte

**Problemas?**
- Verifique os logs do PHP
- Ative debug em `enviar-email.php` (linha 138): `$mail->SMTPDebug = 2;`
- Consulte `CONFIGURACAO-EMAIL.md` para troubleshooting detalhado

---

*Configuração realizada em: 12/03/2026*
