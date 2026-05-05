# 📄 Configuração de Geração de PDF - Endall Inspeções

## 🎯 **Objetivo**

Gerar PDF profissional dos orçamentos com:
- ✅ Logo da Endall
- ✅ Dados do cliente
- ✅ Lista de produtos
- ✅ Especificações técnicas
- ✅ Observações
- ✅ Identidade visual Endall

---

## 🧪 **Teste de PDF**

### **1️⃣ Abra a página de teste:**
```
http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php
```

### **2️⃣ Verifique o status:**
A página mostrará:
- ✅ **Vendor (Composer):** Deve estar "Instalado"
- ✅ **mPDF:** Deve estar "Instalado"
- ✅ **Diretório de PDFs:** Status do diretório

### **3️⃣ Se tudo estiver OK:**
- Você verá a lista dos últimos orçamentos
- Clique em **"Gerar PDF"** no orçamento #ORC20260313-0417
- O PDF deve ser baixado automaticamente

---

## ❌ **Se Aparecer Erros**

### **Erro 1: "Dependências Não Instaladas"**

**Solução:**
```bash
cd /Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto
composer install
```

### **Erro 2: "mPDF Não Encontrado"**

**Solução:**
```bash
cd /Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto
composer require mpdf/mpdf
```

### **Erro 3: "Permission denied" no diretório**

**Solução:**
```bash
cd /Users/admin/Documents/NovaEraWeb/F1Clientes/Endall/catalogo/projeto
mkdir -p uploads/pdfs
chmod 755 uploads/pdfs
```

---

## 📋 **O Que o PDF Contém**

O PDF gerado inclui:

### **Cabeçalho:**
- Logo da Endall
- Título "ORÇAMENTO"
- Número do orçamento
- Data de emissão

### **Dados do Cliente:**
- Nome completo
- Empresa
- E-mail
- Telefone
- Cargo

### **Lista de Produtos:**
Para cada produto:
- SKU
- Nome do produto
- Série
- Especificações técnicas:
  - Diâmetro da câmera
  - Comprimento do cabo
- Quantidade
- Observações (se houver)

### **Rodapé:**
- Dados da Endall Inspeções
- Endereço
- Telefone
- E-mail
- Website

---

## 🎨 **Identidade Visual**

O PDF usa as cores da Endall:
- **Azul Escuro:** #0D1B2A (Principal)
- **Azul Médio:** #1a3a52 (Secundário)
- **Laranja:** #F5A623 (Destaque)
- **Cinza:** #6c757d (Texto secundário)

---

## 🔧 **Personalizações Possíveis**

Se você quiser ajustar o PDF, edite o arquivo `gerar-pdf.php`:

### **Mudar cores:**
```php
// Linha ~143 (dentro de gerarHTMLPDF)
.header {
    background: linear-gradient(135deg, #0D1B2A 0%, #1a3a52 100%);
    // Mude para suas cores aqui
}
```

### **Adicionar logo:**
```php
// Linha ~149 (dentro de gerarHTMLPDF)
.header-logo {
    // Adicione sua logo aqui
    background-image: url("caminho/para/logo.png");
}
```

### **Mudar margens:**
```php
// Linha 68-76
$mpdf = new Mpdf([
    'margin_left' => 15,    // Margem esquerda
    'margin_right' => 15,   // Margem direita
    'margin_top' => 20,     // Margem superior
    'margin_bottom' => 20,  // Margem inferior
]);
```

---

## 📊 **Fluxo de Geração**

```
1. Cliente solicita orçamento
   ↓
2. Sistema salva no banco de dados
   ↓
3. Sistema chama enviar-email.php
   ↓
4. enviar-email.php chama gerar-pdf.php
   ↓
5. gerar-pdf.php cria o PDF
   ↓
6. PDF é salvo em uploads/pdfs/
   ↓
7. PDF é anexado ao e-mail
   ↓
8. E-mail é enviado com PDF anexo
```

---

## 🧪 **Teste Manual (Alternativo)**

Se quiser testar sem passar pelo formulário:

### **Método 1: URL Direta**
```
http://localhost:8888/Endall/catalogo/projeto/gerar-pdf.php?id=1
```
(Substitua `1` pelo ID do orçamento)

### **Método 2: Por Número**
```
http://localhost:8888/Endall/catalogo/projeto/gerar-pdf.php?numero=ORC20260313-0417
```

---

## ✅ **Checklist de Teste**

Ao gerar o PDF, verifique:

- [ ] PDF foi baixado automaticamente
- [ ] Cabeçalho com identidade visual
- [ ] Número do orçamento correto (#ORC20260313-0417)
- [ ] Data de emissão formatada (dd/mm/aaaa)
- [ ] Nome do cliente correto
- [ ] E-mail do cliente correto
- [ ] Telefone do cliente correto
- [ ] Empresa do cliente (se informada)
- [ ] Cargo do cliente (se informado)
- [ ] Lista de produtos completa
- [ ] SKU de cada produto
- [ ] Nome de cada produto
- [ ] Série de cada produto
- [ ] Especificações técnicas (diâmetro, cabo)
- [ ] Quantidade de cada produto
- [ ] Observações (se houver)
- [ ] Rodapé com dados da Endall
- [ ] PDF sem erros de formatação
- [ ] Texto em português correto
- [ ] Acentos renderizados corretamente

---

## 🛠️ **Troubleshooting**

### **PDF em branco ou vazio**

**Causa:** Dados não foram decodificados corretamente

**Solução:**
1. Verifique se o orçamento tem itens
2. Verifique se o JSON está válido
3. Adicione debug no gerar-pdf.php:
```php
// Linha 60
var_dump($itens);
exit;
```

### **PDF com caracteres estranhos**

**Causa:** Problema de encoding UTF-8

**Solução:**
```php
// Linha 69 (já está configurado)
'mode' => 'utf-8',
```

### **Erro "Class 'Mpdf\Mpdf' not found"**

**Causa:** mPDF não instalado

**Solução:**
```bash
composer require mpdf/mpdf
```

### **Erro "Permission denied"**

**Causa:** Diretório sem permissão de escrita

**Solução:**
```bash
chmod -R 755 uploads/pdfs/
```

---

## 🚀 **Integração com E-mail**

O PDF é automaticamente:
1. ✅ Gerado quando o orçamento é criado
2. ✅ Salvo em `uploads/pdfs/orcamento_NUMERO.pdf`
3. ✅ Caminho salvo no banco de dados
4. ✅ Anexado ao e-mail do cliente
5. ✅ Anexado ao e-mail da empresa

**Não precisa fazer nada manualmente!**

---

## 📸 **Próximo Passo**

1. Acesse: http://localhost:8888/Endall/catalogo/projeto/teste-pdf.php
2. Clique em **"Gerar PDF"** no orçamento #ORC20260313-0417
3. **Me envie um print** do PDF gerado (abra o PDF e tire print)
4. Ou me diga se deu algum erro

---

## 🎉 **Quando Funcionar**

O sistema estará **100% COMPLETO**:
- ✅ Catálogo de produtos
- ✅ Filtros dinâmicos
- ✅ Carrinho de compras
- ✅ Formulário de orçamento
- ✅ Validações e salvamento
- ✅ **Geração de PDF** ← TESTANDO AGORA
- ⚠️ Envio de e-mail (já configurado, faltando apenas SMTP correto)

---

**Arquivo de Teste:** `teste-pdf.php`  
**Gerador:** `gerar-pdf.php`  
**Biblioteca:** mPDF (Composer)  
**Próximo Passo:** Testar e me enviar o resultado!
