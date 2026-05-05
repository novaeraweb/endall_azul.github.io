# ✅ Configurações Atualizadas - Endall Inspeções

## 📝 Alterações Aplicadas

### 1. ✅ Senha do MySQL
```php
define('DB_PASS', 'root');
```
**Antes:** `''` (vazio)  
**Agora:** `'root'`

---

### 2. ✅ URL do Projeto
```php
define('SITE_URL', 'http://localhost:8888/Endall/catalogo/projeto');
```
**Antes:** `projeto2`  
**Agora:** `projeto`

---

## 🧪 URLs Corretas para Teste

### Catálogo:
```
http://localhost:8888/Endall/catalogo/projeto/index.php
```

### Orçamento:
```
http://localhost:8888/Endall/catalogo/projeto/orcamento.php
```

### Adicionar Produto Teste:
```
http://localhost:8888/Endall/catalogo/projeto/adicionar-produto-teste.html
```

### Debug Carrinho:
```
http://localhost:8888/Endall/catalogo/projeto/debug-carrinho.html
```

### Teste URL:
```
http://localhost:8888/Endall/catalogo/projeto/teste-url.php
```

### Teste E-mail:
```
http://localhost:8888/Endall/catalogo/projeto/teste-email.html
```

---

## 🔧 Arquivo de Configuração Completo

**Local:** `includes/config.php`

```php
// BANCO DE DADOS
define('DB_HOST', 'localhost');
define('DB_NAME', 'endall_vendas');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4');

// SISTEMA
define('SITE_NAME', 'Endall Inspeções - Sistema de Vendas');
define('SITE_URL', 'http://localhost:8888/Endall/catalogo/projeto');
define('BASE_PATH', __DIR__ . '/..');

// EMPRESA
define('EMPRESA_NOME', 'Endall Inspeções');
define('EMPRESA_EMAIL', 'comercial@endall.com.br');
define('EMPRESA_TELEFONE', '(11) 3456-7890');
define('TELEFONE', '(11) 3456-7890');
define('EMPRESA_WHATSAPP', '5511987654321');
define('WHATSAPP', '5511987654321');
define('EMPRESA_ENDERECO', 'Rua Exemplo, 123 - São Paulo - SP');
define('EMPRESA_SITE', 'https://endall.com.br');

// E-MAIL (SMTP)
define('SMTP_HOST', 'smtp.umbler.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'contato@novaeraweb.com.br');
define('SMTP_PASS', 'em*NEW010203');
define('SMTP_FROM_NAME', 'Endall Inspeções');
define('SMTP_FROM_EMAIL', 'contato@novaeraweb.com.br');

// LIMITES
define('LIMITE_CARRINHO', 20);
define('PRODUTOS_POR_PAGINA', 12);
define('SESSAO_TEMPO', 7200);

// DIRETÓRIOS
define('DIR_UPLOADS', BASE_PATH . '/uploads');
define('DIR_PDFS', BASE_PATH . '/uploads/pdfs');
define('DIR_PRODUTOS', BASE_PATH . '/uploads/produtos');
define('DIR_TEMP', BASE_PATH . '/uploads/temp');

// URLS
define('URL_ASSETS', SITE_URL . '/assets');
define('URL_UPLOADS', SITE_URL . '/uploads');
define('URL_PRODUTOS', SITE_URL . '/uploads/produtos');

// SEGURANÇA
define('SALT_KEY', 'endall_2026_seguro_xyz123');
define('SESSION_NAME', 'endall_vendas_session');
define('ASSETS_VERSION', time());
```

---

## 🎯 Próximos Testes

Agora que as configurações estão corretas:

### 1. Teste a Conexão com Banco:
```
http://localhost:8888/Endall/catalogo/projeto/teste-url.php
```

### 2. Teste o Debug do Carrinho:
```
http://localhost:8888/Endall/catalogo/projeto/debug-carrinho.html
```

### 3. Adicione Produto e Teste Orçamento:
```
1. adicionar-produto-teste.html
2. Adicione produto
3. Vá para orcamento.php
4. Preencha e envie
```

---

## ✅ Checklist de Verificação

- [x] Senha do MySQL alterada para "root"
- [x] URL do projeto corrigida
- [x] Banco de dados deve estar acessível
- [ ] Testar conexão com banco
- [ ] Testar carregamento do carrinho.js
- [ ] Testar formulário de orçamento
- [ ] Testar envio de e-mail

---

## 📞 Se Houver Erro

### Erro: "Access denied for user 'root'@'localhost'"
**Solução:** Verifique se a senha "root" está correta no MySQL

### Erro: "Unknown database 'endall_vendas'"
**Solução:** Execute o arquivo `install/setup.sql`

### Erro: 404 - Not Found
**Solução:** Verifique se está usando a URL correta:
```
http://localhost:8888/Endall/catalogo/projeto/
```

---

*Configurações atualizadas em: 12/03/2026*
