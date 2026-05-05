# 🔧 Correção: Erro prepare() no enviar-email.php

## 🔴 Problema Identificado

**Erro:**
```
Fatal error: Uncaught Error: Call to a member function prepare() on null
in enviar-email.php:182
```

**Causa:**
A variável `$pdo` estava sendo declarada como `global $pdo`, mas nunca foi inicializada. Em PHP, se você declara uma variável como `global` mas ela não existe no escopo global, ela será `null`.

---

## ❌ **CÓDIGO ERRADO (ANTES)**

```php
function enviarEmailsOrcamento($orcamentoId) {
    global $pdo;  // ❌ ERRADO: $pdo não existe no escopo global
    
    try {
        // Tentando usar $pdo (que é null)
        $stmt = $pdo->prepare("SELECT * FROM orcamentos...");  // ❌ ERRO!
    }
}
```

---

## ✅ **CÓDIGO CORRETO (DEPOIS)**

```php
function enviarEmailsOrcamento($orcamentoId) {
    // ✅ CORRETO: Obter conexão através da classe Database
    $pdo = db()->getConnection();
    
    try {
        // Agora $pdo é uma instância válida de PDO
        $stmt = $pdo->prepare("SELECT * FROM orcamentos...");  // ✅ FUNCIONA!
    }
}
```

---

## 🎯 **Por Que Isso Aconteceu?**

### **No sistema Endall:**
- A conexão com o banco é feita através da **classe Database** (Singleton)
- A função `db()` retorna a instância única da classe
- O método `getConnection()` retorna o objeto PDO

### **Fluxo Correto:**
```php
1. db()                    // Retorna Database::getInstance()
2. getConnection()         // Retorna o objeto PDO
3. prepare()              // Agora funciona!
```

---

## 📝 **O Que Foi Mudado**

| Arquivo | Linha | Antes | Depois |
|---------|-------|-------|--------|
| **enviar-email.php** | 169 | `global $pdo;` | `$pdo = db()->getConnection();` |

---

## 🔍 **Como Verificar Se Está Correto**

Você pode adicionar um debug temporário:

```php
function enviarEmailsOrcamento($orcamentoId) {
    $pdo = db()->getConnection();
    
    // Debug temporário
    error_log("PDO é null? " . ($pdo === null ? 'SIM' : 'NÃO'));
    error_log("PDO class: " . get_class($pdo));
    
    // Deve mostrar:
    // PDO é null? NÃO
    // PDO class: PDO
}
```

---

## 🧪 **Teste Agora**

### **1️⃣ Limpe o cache:**
```
Ctrl + Shift + R
```

### **2️⃣ Acesse:**
```
http://localhost:8888/Endall/catalogo/projeto/orcamento.php
```

### **3️⃣ Preencha e envie o formulário**

---

## ✅ **Resultado Esperado**

Agora você DEVE ver:

1. ✅ **Sem erros de conexão**
2. ✅ **Orçamento buscado no banco**
3. ✅ **E-mails sendo enviados**
4. ✅ **Mensagem de sucesso verde**

---

## 🛠️ **Outras Funções Que Usam a Mesma Lógica**

No projeto Endall, sempre use:

```php
// ✅ CORRETO: Obter conexão PDO
$pdo = db()->getConnection();

// ✅ CORRETO: Executar queries diretamente
$resultado = db()->execute($sql, $params);

// ✅ CORRETO: Buscar dados
$dados = db()->query($sql, $params);

// ✅ CORRETO: Buscar uma linha
$linha = db()->queryRow($sql, $params);
```

**❌ NUNCA use:**
```php
global $pdo;  // ❌ NÃO EXISTE!
```

---

## 📚 **Documentação da Classe Database**

### **Métodos Disponíveis:**

```php
// Obter instância
$db = Database::getInstance();
$db = db();  // Atalho (definido em includes/functions.php)

// Obter conexão PDO
$pdo = $db->getConnection();

// Executar SELECT
$results = $db->query($sql, $params);

// Executar SELECT (uma linha)
$row = $db->queryRow($sql, $params);

// Executar INSERT/UPDATE/DELETE
$id = $db->execute($sql, $params);  // Retorna ID se for INSERT

// Contar registros
$total = $db->count($table, $where, $params);
```

---

## 🚀 **Próximos Passos**

Se o erro foi resolvido:
1. ✅ Teste o envio completo
2. ✅ Verifique se o e-mail foi enviado
3. ✅ Confirme se o PDF foi anexado
4. ✅ Valide os dados salvos no banco

---

## 📋 **Checklist de Hoje**

- [x] ✅ Imagens corrigidas (placeholder local)
- [x] ✅ Filtros corrigidos (retorno JSON)
- [x] ✅ Campo enviar_orcamento (campo hidden)
- [x] ✅ JSON corrompido (Base64)
- [x] ✅ Erro lastInsertId() (usar $resultado)
- [x] ✅ Syntax error "use" (movido para topo)
- [x] ✅ Erro prepare() null (usar db()->getConnection())

---

**Data da Correção:** <?= date('d/m/Y H:i:s') ?>  
**Arquivo:** enviar-email.php  
**Linha modificada:** 169  
**Status:** ✅ Corrigido
