# 👨‍💻 Guia de Desenvolvimento - Endall Inspeções

Este guia ajuda desenvolvedores a entender, modificar e expandir o sistema.

---

## 📋 Índice

- [Arquitetura do Sistema](#arquitetura-do-sistema)
- [Padrões de Código](#padrões-de-código)
- [Convenções de Nomenclatura](#convenções-de-nomenclatura)
- [Como Adicionar Funcionalidades](#como-adicionar-funcionalidades)
- [Debugging](#debugging)
- [Performance](#performance)
- [Testes](#testes)

---

## 🏗️ Arquitetura do Sistema

### Padrão MVC Simplificado

```
┌─────────────────────────────────────────┐
│          CLIENTE (Navegador)            │
│  HTML + CSS + JavaScript (Frontend)     │
└──────────────┬──────────────────────────┘
               │
               │ HTTP Request
               ▼
┌─────────────────────────────────────────┐
│         SERVIDOR WEB (Apache)           │
│              index.php                   │
│  ┌──────────────────────────────────┐   │
│  │  INCLUDES (Configuração/Lógica)  │   │
│  │  - config.php                    │   │
│  │  - db.php (Conexão PDO)          │   │
│  │  - functions.php (Utilitários)   │   │
│  │  - header.php / footer.php       │   │
│  └──────────────────────────────────┘   │
└──────────────┬──────────────────────────┘
               │
               │ SQL Queries
               ▼
┌─────────────────────────────────────────┐
│        BANCO DE DADOS (MySQL)           │
│  - series                                │
│  - produtos                              │
│  - orcamentos                            │
│  - configuracoes                         │
│  - usuarios_admin                        │
│  - logs_sistema                          │
└─────────────────────────────────────────┘
```

### Fluxo de Dados

1. **Requisição:** Cliente solicita página PHP
2. **Configuração:** `config.php` define constantes
3. **Conexão:** `db.php` conecta ao MySQL (Singleton)
4. **Lógica:** Funções em `functions.php` processam dados
5. **Template:** `header.php` + conteúdo + `footer.php`
6. **Resposta:** HTML+CSS+JS enviado ao cliente
7. **Interação:** JavaScript manipula DOM e faz AJAX

---

## 📝 Padrões de Código

### PHP

#### Estilo

```php
<?php
/**
 * Descrição da função
 * 
 * @param string $param Descrição do parâmetro
 * @return mixed Descrição do retorno
 */
function nomeDaFuncao($param) {
    // Usar camelCase para variáveis
    $minhaVariavel = 'valor';
    
    // Usar PascalCase para classes
    $minhaClasse = new MinhaClasse();
    
    // Espaçamento consistente
    if ($condicao) {
        // código
    } else {
        // código
    }
    
    return $resultado;
}
?>
```

#### Segurança

✅ **SEMPRE:**
```php
// Usar prepared statements
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = db()->query($sql, [$id]);

// Sanitizar outputs
echo htmlspecialchars($texto);

// Validar inputs
if (!validarEmail($email)) {
    // erro
}
```

❌ **NUNCA:**
```php
// SQL Injection vulnerável
$sql = "SELECT * FROM produtos WHERE id = $id";

// XSS vulnerável
echo $_GET['nome'];

// Confiar em inputs
$_POST['valor']; // sem validação
```

### JavaScript

#### Estilo

```javascript
/**
 * Descrição da função
 * @param {string} param - Descrição do parâmetro
 * @returns {boolean} Descrição do retorno
 */
function nomeDaFuncao(param) {
    // Use const/let, nunca var
    const minhaConstante = 'valor';
    let minhaVariavel = 10;
    
    // Arrow functions quando apropriado
    const minhaFuncao = (x) => x * 2;
    
    // Template strings
    const mensagem = `Valor: ${minhaVariavel}`;
    
    return true;
}
```

#### AJAX

```javascript
// Padrão para requisições AJAX
fetch('ajax/endpoint.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': obterCSRFToken()
    },
    body: JSON.stringify(dados)
})
.then(response => response.json())
.then(data => {
    if (data.sucesso) {
        // sucesso
    } else {
        mostrarToast(data.mensagem, 'error');
    }
})
.catch(error => {
    console.error('Erro:', error);
    mostrarToast('Erro na requisição', 'error');
});
```

### CSS

#### Metodologia

- **Usar CSS Variables** para cores, espaçamentos, etc.
- **Mobile-first:** Escrever para mobile, depois `@media` para desktop
- **BEM opcional:** `.bloco__elemento--modificador`

```css
/* Exemplo */
.produto-card {
    /* Estilos base (mobile) */
    padding: var(--espaco-4);
    background: var(--cor-branco);
}

.produto-card__titulo {
    color: var(--cor-primaria);
}

.produto-card--destaque {
    border: 2px solid var(--cor-secundaria);
}

/* Desktop */
@media (min-width: 768px) {
    .produto-card {
        padding: var(--espaco-6);
    }
}
```

---

## 🏷️ Convenções de Nomenclatura

### Banco de Dados

- **Tabelas:** Plural, minúsculas, snake_case
  - ✅ `produtos`, `orcamentos`, `usuarios_admin`
  - ❌ `Produto`, `Orcamento`, `UsuarioAdmin`

- **Colunas:** Singular, minúsculas, snake_case
  - ✅ `nome`, `cliente_email`, `diametro_camera`
  - ❌ `Nome`, `clienteEmail`, `DiametroCamara`

- **Foreign Keys:** `{tabela_singular}_id`
  - ✅ `serie_id`, `produto_id`, `usuario_id`

### PHP

- **Arquivos:** minúsculas, kebab-case
  - ✅ `config.php`, `gerar-pdf.php`, `enviar-email.php`

- **Funções:** camelCase
  - ✅ `formatarMoeda()`, `buscarProduto()`, `validarEmail()`

- **Classes:** PascalCase
  - ✅ `Database`, `Usuario`, `ProdutoController`

- **Constantes:** UPPER_SNAKE_CASE
  - ✅ `DB_HOST`, `SITE_URL`, `LIMITE_CARRINHO`

### JavaScript

- **Variáveis:** camelCase
  - ✅ `meuObjeto`, `contadorProdutos`, `filtrosAtivos`

- **Constantes:** camelCase ou UPPER_SNAKE_CASE
  - ✅ `API_URL`, `limiteItens`, `MAX_FILE_SIZE`

- **Funções:** camelCase
  - ✅ `adicionarProduto()`, `atualizarContador()`, `mostrarToast()`

- **Classes/Objetos:** PascalCase
  - ✅ `Carrinho`, `FiltroManager`, `ProdutoCard`

### CSS

- **Classes:** kebab-case
  - ✅ `.produto-card`, `.btn-primary`, `.serie-badge`

- **IDs:** camelCase
  - ✅ `#carrinhoItens`, `#produtosGrid`, `#btnEnviar`

- **Variáveis CSS:** kebab-case com prefixo
  - ✅ `--cor-primaria`, `--espaco-4`, `--border-radius-md`

---

## ➕ Como Adicionar Funcionalidades

### 1. Nova Página

```php
<?php
// 1. Definir constante
define('SISTEMA_ENDALL', true);

// 2. Carregar dependências
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// 3. Iniciar sessão
session_name(SESSION_NAME);
session_start();

// 4. Definir meta tags
$page_title = 'Título da Página';
$page_description = 'Descrição SEO';
$additional_js = ['assets/js/meu-script.js'];
$additional_css = ['assets/css/meu-estilo.css'];

// 5. Lógica da página
// ... seu código aqui ...

// 6. Incluir header
include __DIR__ . '/includes/header.php';
?>

<!-- 7. HTML da página -->
<div class="container">
    <h1>Conteúdo</h1>
</div>

<?php
// 8. Incluir footer
include __DIR__ . '/includes/footer.php';
?>
```

### 2. Novo Endpoint AJAX

```php
<?php
// ajax/meu-endpoint.php
define('SISTEMA_ENDALL', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Verificar AJAX
if (!isAjax()) {
    jsonError('Requisição inválida', 400);
}

// Obter dados
$json = file_get_contents('php://input');
$dados = json_decode($json, true);

// Validar
if (empty($dados)) {
    jsonError('Dados inválidos', 400);
}

// Processar
try {
    // ... lógica aqui ...
    
    jsonSuccess($resultado, 'Sucesso');
} catch (Exception $e) {
    jsonError($e->getMessage(), 500);
}
?>
```

### 3. Nova Função Global

Adicionar em `includes/functions.php`:

```php
/**
 * Descrição da função
 * 
 * @param mixed $param Descrição
 * @return mixed Descrição do retorno
 */
function minhaNovaFuncao($param) {
    // Validar entrada
    if (empty($param)) {
        return false;
    }
    
    // Processar
    $resultado = // ... lógica ...
    
    // Retornar
    return $resultado;
}
```

### 4. Nova Tabela no Banco

Adicionar em `install/setup.sql`:

```sql
-- Criar tabela
CREATE TABLE minha_tabela (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(200) NOT NULL,
  ativo TINYINT DEFAULT 1,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados iniciais (se necessário)
INSERT INTO minha_tabela (nome) VALUES ('Exemplo 1'), ('Exemplo 2');
```

---

## 🐛 Debugging

### Ativar Debug Mode

`includes/config.php`:
```php
define('DEBUG_MODE', true); // linha 75
```

### Debug Helper

```php
// Imprimir variável formatada
debug($minhaVariavel);

// Imprimir e parar execução
debug($minhaVariavel, true);
```

### Logs do PHP

```bash
# Linux/Mac
tail -f /var/log/apache2/error.log

# XAMPP Windows
C:\xampp\apache\logs\error.log
```

### Console do Navegador

```javascript
// Logs estruturados
console.log('Info:', dados);
console.error('Erro:', erro);
console.warn('Aviso:', aviso);
console.table(array); // Visualizar arrays
```

### Inspecionar Banco de Dados

```sql
-- Ver últimos orçamentos
SELECT * FROM orcamentos ORDER BY criado_em DESC LIMIT 10;

-- Ver produtos por série
SELECT s.nome as serie, COUNT(p.id) as total
FROM series s
LEFT JOIN produtos p ON s.id = p.serie_id
GROUP BY s.id;

-- Ver logs do sistema
SELECT * FROM logs_sistema ORDER BY criado_em DESC LIMIT 20;
```

---

## ⚡ Performance

### Otimizações Aplicadas

✅ **Banco de Dados:**
- Índices em colunas frequentemente consultadas
- Queries com LIMIT
- Views pré-calculadas
- Prepared statements (cache de queries)

✅ **Frontend:**
- CSS minificado (produção)
- JavaScript minificado (produção)
- Imagens otimizadas
- Lazy loading de imagens
- Debounce em inputs

✅ **Servidor:**
- Compressão GZIP habilitada
- Cache do navegador configurado
- Headers de cache otimizados

### Dicas de Performance

1. **Evitar N+1 Queries**
```php
// ❌ Ruim
foreach ($produtos as $produto) {
    $serie = buscarSerie($produto['serie_id']); // Query dentro do loop
}

// ✅ Bom
$sql = "SELECT p.*, s.nome as serie_nome FROM produtos p INNER JOIN series s ON p.serie_id = s.id";
$produtos = db()->query($sql);
```

2. **Usar Cache quando possível**
```php
// Cache de configurações
static $cache = [];
if (isset($cache[$chave])) {
    return $cache[$chave];
}
```

3. **Limitar Resultados**
```sql
-- Sempre usar LIMIT em listagens
SELECT * FROM produtos ORDER BY nome LIMIT 100;
```

---

## 🧪 Testes

### Checklist de Testes

#### Funcionalidade
- [ ] Catálogo carrega corretamente
- [ ] Filtros funcionam (todos os 6 tipos)
- [ ] Busca retorna resultados corretos
- [ ] Adicionar ao carrinho funciona
- [ ] Remover do carrinho funciona
- [ ] Formulário de orçamento valida campos
- [ ] Orçamento é salvo no banco
- [ ] Número do orçamento é gerado

#### Segurança
- [ ] SQL Injection prevenido (testar com ' OR '1'='1)
- [ ] XSS prevenido (testar com <script>alert('XSS')</script>)
- [ ] CSRF token validado em POST
- [ ] Acesso direto a includes bloqueado
- [ ] Upload de arquivos validado

#### Performance
- [ ] Página carrega em < 3 segundos
- [ ] Filtros respondem em < 500ms
- [ ] Queries executam em < 100ms
- [ ] Sem vazamentos de memória em JavaScript

#### Responsividade
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

#### Navegadores
- [ ] Chrome/Edge (últimas 2 versões)
- [ ] Firefox (últimas 2 versões)
- [ ] Safari (últimas 2 versões)
- [ ] Mobile Safari
- [ ] Chrome Mobile

---

## 📞 Suporte

Dúvidas sobre desenvolvimento? Entre em contato:

- **E-mail:** dev@endall.com.br
- **Documentação:** README.md

---

*Última atualização: 12 de Março de 2026*
