# 🛒 Como Testar o Sistema de Orçamento

## ⚠️ Problema Identificado

A mensagem **"Erro ao processar os produtos selecionados"** aparece porque:
- O carrinho está vazio
- Você precisa adicionar produtos primeiro

---

## ✅ Solução Rápida - 2 Opções

### **Opção 1: Usar Página de Teste (MAIS FÁCIL)**

1. **Abra a página de teste:**
   ```
   http://localhost:8888/Endall/catalogo/projeto2/adicionar-produto-teste.html
   ```

2. **Clique em "Adicionar Produto ao Carrinho"**

3. **Clique em "Ir para Orçamento"**

4. **Preencha o formulário e envie**

---

### **Opção 2: Usar o Catálogo Normal**

1. **Acesse o catálogo:**
   ```
   http://localhost:8888/Endall/catalogo/projeto2/index.php
   ```

2. **Clique em qualquer produto**

3. **Clique em "Adicionar ao Orçamento"** ou navegue para a página do produto

4. **Vá para o carrinho** (ícone no canto superior direito)

5. **Clique em "Finalizar Orçamento"**

6. **Preencha e envie**

---

## 🧪 Fluxo Completo de Teste

### 1. Adicionar Produto
```
adicionar-produto-teste.html
↓
Clique em "Adicionar Produto ao Carrinho"
```

### 2. Ver Carrinho
```
Badge do carrinho mostra: 🛒 1
```

### 3. Ir para Orçamento
```
orcamento.php
↓
Produto aparece na lista
```

### 4. Preencher Formulário
```
Nome: João da Silva
E-mail: seu-email@real.com  ← USE SEU E-MAIL REAL
Telefone: (11) 98765-4321
```

### 5. Enviar
```
Sistema salva no banco
↓
Envia 2 e-mails:
  • Cliente recebe confirmação
  • Empresa recebe notificação
↓
Página de sucesso
```

---

## 📋 Checklist de Teste

- [ ] Acesse `adicionar-produto-teste.html`
- [ ] Clique em "Adicionar Produto ao Carrinho"
- [ ] Veja mensagem de sucesso
- [ ] Clique em "Ir para Orçamento"
- [ ] Verifique que produto aparece na lista
- [ ] Preencha formulário com **e-mail real**
- [ ] Clique em "Solicitar Orçamento"
- [ ] Veja página de sucesso
- [ ] Verifique e-mail recebido (inbox ou spam)

---

## 📧 E-mails que Você Receberá

### Para Você (Cliente):
- **Assunto:** Orçamento #XXXX - Endall Inspeções
- **Conteúdo:** Confirmação com número do orçamento

### Para Empresa:
- **Para:** comercial@endall.com.br
- **Assunto:** 🔔 Novo Orçamento Recebido - #XXXX
- **Conteúdo:** Dados completos do cliente e produtos

---

## 🐛 Se Ainda Der Erro

### Erro: "Erro ao processar os produtos selecionados"

**Causa:** Carrinho vazio

**Solução:**
1. Use a página `adicionar-produto-teste.html`
2. Adicione pelo menos 1 produto
3. Então vá para `orcamento.php`

---

### Erro: "Função undefined"

**Causa:** JavaScript não carregou

**Solução:**
1. Verifique console do navegador (F12)
2. Recarregue página (Ctrl+F5)
3. Limpe cache do navegador

---

### E-mail não chega

**Soluções:**
1. Verifique pasta de spam
2. Aguarde 1-2 minutos
3. Teste o SMTP:
   ```
   teste-email.html
   ```

---

## 🎯 URLs Úteis

| Página | URL |
|--------|-----|
| Adicionar Produto Teste | `adicionar-produto-teste.html` |
| Catálogo | `index.php` |
| Orçamento | `orcamento.php` |
| Teste E-mail | `teste-email.html` |
| Teste E-mail API | `enviar-email.php?teste=1&email=seu@email.com` |

---

## ✨ Tudo Funcionando?

Se tudo funcionar:
- ✅ Produto adicionado ao carrinho
- ✅ Formulário de orçamento preenchido
- ✅ Orçamento salvo no banco
- ✅ E-mails enviados
- ✅ Página de sucesso exibida

**🎉 Sistema 100% operacional!**

---

## 📞 Precisa de Ajuda?

1. Abra console do navegador (F12)
2. Veja se há erros JavaScript
3. Verifique se arquivos JS carregaram:
   - `assets/js/main.js`
   - `assets/js/carrinho.js`

---

*Teste agora: `adicionar-produto-teste.html`*
