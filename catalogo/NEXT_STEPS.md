# 🚀 PRÓXIMOS PASSOS - Endall Inspeções

**Status Atual:** Fase 1 (MVP) CONCLUÍDA ✅  
**Próxima Etapa:** Fase 2 - Funcionalidades Críticas  
**Prioridade:** ALTA

---

## 📋 Tarefas Pendentes (Fase 2)

### 1️⃣ Página Individual do Produto (produto.php)

**Prioridade:** 🔴 **ALTA**  
**Estimativa:** 4-6 horas  
**Dependências:** Nenhuma

#### Checklist de Implementação

- [ ] Criar arquivo `produto.php`
- [ ] Implementar breadcrumb dinâmico
- [ ] Criar galeria de imagens com:
  - [ ] Thumbnail clicáveis
  - [ ] Imagem principal com zoom ao hover
  - [ ] Lightbox para visualização ampliada
- [ ] Exibir informações completas:
  - [ ] Nome e SKU
  - [ ] Badge da série
  - [ ] Descrição completa
  - [ ] Tabela de especificações técnicas
  - [ ] Recursos especiais com ícones
- [ ] Implementar CTAs:
  - [ ] Botão "Adicionar ao Orçamento" (grande)
  - [ ] Botão "Baixar Ficha Técnica PDF"
  - [ ] Botão "Assistir Vídeo" (se disponível)
  - [ ] Botão "Compartilhar"
- [ ] Seção de produtos relacionados (mesma série)
- [ ] Tabs de conteúdo:
  - [ ] Especificações Completas
  - [ ] Aplicações e Casos de Uso
  - [ ] Downloads (PDFs, manuais)
- [ ] Integrar com carrinho (botão de adicionar)
- [ ] Registrar visualização no banco
- [ ] Testar responsividade
- [ ] Validar SEO (meta tags, title, description)

#### Exemplo de Estrutura

```php
<?php
// produto.php
define('SISTEMA_ENDALL', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Obter ID do produto
$produto_id = (int) getParam('id', 0);

// Buscar produto
$produto = buscarProduto($produto_id);

if (!$produto) {
    header('Location: index.php');
    exit;
}

// Registrar visualização
$sql = "UPDATE produtos SET visualizacoes = visualizacoes + 1 WHERE id = ?";
db()->execute($sql, [$produto_id]);

// Buscar produtos relacionados
$sql = "SELECT p.*, s.nome as serie_nome, s.cor as serie_cor 
        FROM produtos p
        INNER JOIN series s ON p.serie_id = s.id
        WHERE p.serie_id = ? AND p.id != ? AND p.ativo = 1
        LIMIT 4";
$relacionados = db()->query($sql, [$produto['serie_id'], $produto_id]);

// Decodificar JSON
$imagens = decodificarImagens($produto['imagens']);
$recursos = decodificarRecursos($produto['recursos_especiais']);

// Meta tags
$page_title = $produto['nome'] . ' - ' . $produto['sku'];
$page_description = truncarTexto($produto['descricao'], 160);

include __DIR__ . '/includes/header.php';
?>

<!-- HTML da página aqui -->

<?php include __DIR__ . '/includes/footer.php'; ?>
```

---

### 2️⃣ Geração de PDF (gerar-pdf.php)

**Prioridade:** 🔴 **ALTA**  
**Estimativa:** 6-8 horas  
**Dependências:** Instalar mPDF

#### Checklist de Implementação

- [ ] Instalar mPDF via Composer:
  ```bash
  composer require mpdf/mpdf
  ```

- [ ] Criar arquivo `gerar-pdf.php`
- [ ] Implementar template HTML do PDF:
  - [ ] Cabeçalho com logo Endall
  - [ ] Dados da empresa (contatos, endereço)
  - [ ] Número do orçamento e data
  - [ ] Dados do cliente formatados
  - [ ] Tabela de produtos com:
    - [ ] Foto miniatura
    - [ ] Nome e SKU
    - [ ] Specs principais
    - [ ] Quantidade
    - [ ] Observações
  - [ ] Rodapé com contatos e observações
  - [ ] Marca d'água sutil (opcional)
- [ ] Aplicar identidade visual Endall:
  - [ ] Cores (#0D1B2A, #F5A623)
  - [ ] Tipografia Inter
  - [ ] Logo (obter do cliente)
- [ ] Salvar PDF em `uploads/pdfs/`
- [ ] Atualizar campo `pdf_path` no banco
- [ ] Implementar download direto
- [ ] Tratar erros de geração
- [ ] Testar com diferentes quantidades de produtos
- [ ] Validar impressão

#### Exemplo Básico

```php
<?php
// gerar-pdf.php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$orcamento_id = (int) getParam('id', 0);
$orcamento = db()->queryRow("SELECT * FROM orcamentos WHERE id = ?", [$orcamento_id]);

if (!$orcamento) {
    die('Orçamento não encontrado');
}

$itens = json_decode($orcamento['itens'], true);

// Criar instância do mPDF
$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 20,
    'margin_bottom' => 20
]);

// Template HTML
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #0D1B2A; }
        .header { background: #0D1B2A; color: white; padding: 20px; }
        .logo { font-size: 24px; font-weight: bold; }
        .orange { color: #F5A623; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #0D1B2A; color: white; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ENDALL <span class="orange">INSPEÇÕES</span></div>
        <p>Orçamento: ' . $orcamento['numero'] . ' | Data: ' . date('d/m/Y') . '</p>
    </div>
    
    <h2>Dados do Cliente</h2>
    <p><strong>Nome:</strong> ' . htmlspecialchars($orcamento['cliente_nome']) . '</p>
    <p><strong>E-mail:</strong> ' . htmlspecialchars($orcamento['cliente_email']) . '</p>
    <!-- Mais campos... -->
    
    <h2>Produtos Solicitados</h2>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Produto</th>
                <th>Specs</th>
                <th>Qtd</th>
            </tr>
        </thead>
        <tbody>';

foreach ($itens as $item) {
    $html .= '<tr>
        <td>' . htmlspecialchars($item['sku']) . '</td>
        <td>' . htmlspecialchars($item['nome']) . '</td>
        <td>Ø ' . $item['diametro_camera'] . 'mm | ' . $item['comprimento_cabo'] . 'm</td>
        <td>' . $item['quantidade'] . '</td>
    </tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Endall Inspeções</strong> | ' . EMPRESA_TELEFONE . ' | ' . EMPRESA_EMAIL . '</p>
    </div>
</body>
</html>
';

$mpdf->WriteHTML($html);

// Salvar
$filename = 'orcamento_' . $orcamento['numero'] . '.pdf';
$filepath = DIR_PDFS . '/' . $filename;
$mpdf->Output($filepath, \Mpdf\Output\Destination::FILE);

// Atualizar banco
db()->execute("UPDATE orcamentos SET pdf_path = ? WHERE id = ?", [$filepath, $orcamento_id]);

// Download
$mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
?>
```

---

### 3️⃣ Sistema de E-mails (enviar-email.php)

**Prioridade:** 🔴 **ALTA**  
**Estimativa:** 4-6 horas  
**Dependências:** Instalar PHPMailer, Configurar SMTP

#### Checklist de Implementação

- [ ] Instalar PHPMailer via Composer:
  ```bash
  composer require phpmailer/phpmailer
  ```

- [ ] Configurar credenciais SMTP em `config.php`:
  - [ ] Host (smtp.gmail.com ou outro)
  - [ ] Porta (587 para TLS)
  - [ ] Usuário SMTP
  - [ ] Senha de aplicativo
  - [ ] From name e from email

- [ ] Criar templates HTML de e-mail:
  - [ ] **Para o cliente:**
    - Agradecimento pela solicitação
    - Número do orçamento
    - Resumo dos produtos
    - Próximos passos
    - Contatos da empresa
  - [ ] **Para a empresa:**
    - Notificação de novo orçamento
    - Dados do cliente
    - Produtos solicitados
    - Link para admin (quando implementado)

- [ ] Implementar função de envio:
  ```php
  function enviarEmail($destinatario, $assunto, $corpo, $anexos = []) {
      // Configurar PHPMailer
      // Enviar e-mail
      // Retornar sucesso/erro
  }
  ```

- [ ] Integrar com `orcamento.php`:
  - Enviar e-mail após salvar orçamento
  - Anexar PDF gerado
  - Log de envio

- [ ] Implementar retry em caso de falha
- [ ] Salvar log na tabela `logs_sistema`
- [ ] Testar envio real
- [ ] Validar formatação HTML em diferentes clientes de e-mail

#### Exemplo Básico

```php
<?php
// enviar-email.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

function enviarEmailOrcamento($orcamento_id) {
    $orcamento = db()->queryRow("SELECT * FROM orcamentos WHERE id = ?", [$orcamento_id]);
    
    if (!$orcamento) return false;
    
    $mail = new PHPMailer(true);
    
    try {
        // Configurar SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Remetente
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Destinatário (cliente)
        $mail->addAddress($orcamento['cliente_email'], $orcamento['cliente_nome']);
        
        // Assunto
        $mail->Subject = 'Orçamento ' . $orcamento['numero'] . ' - Endall Inspeções';
        
        // Corpo HTML
        $mail->isHTML(true);
        $mail->Body = gerarTemplateEmailCliente($orcamento);
        
        // Anexar PDF
        if (!empty($orcamento['pdf_path']) && file_exists($orcamento['pdf_path'])) {
            $mail->addAttachment($orcamento['pdf_path'], 'orcamento.pdf');
        }
        
        // Enviar
        $mail->send();
        
        // Enviar cópia para empresa
        enviarEmailEmpresa($orcamento);
        
        // Log
        registrarLog('email_enviado', "E-mail de orçamento {$orcamento['numero']} enviado para {$orcamento['cliente_email']}");
        
        return true;
        
    } catch (Exception $e) {
        registrarLog('email_erro', "Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}

function gerarTemplateEmailCliente($orcamento) {
    $itens = json_decode($orcamento['itens'], true);
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: #0D1B2A; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
            .button { background: #F5A623; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>ENDALL INSPEÇÕES</h1>
                <p>Seu orçamento foi recebido!</p>
            </div>
            <div class="content">
                <p>Olá, <strong>' . htmlspecialchars($orcamento['cliente_nome']) . '</strong>!</p>
                <p>Recebemos sua solicitação de orçamento com sucesso.</p>
                <p><strong>Número do Orçamento:</strong> ' . $orcamento['numero'] . '</p>
                <p><strong>Data:</strong> ' . formatarData($orcamento['criado_em'], true) . '</p>
                <p><strong>Total de Produtos:</strong> ' . $orcamento['total_itens'] . '</p>
                <p>Em breve nossa equipe comercial entrará em contato com uma proposta personalizada.</p>
                <a href="https://wa.me/' . EMPRESA_WHATSAPP . '" class="button">Falar no WhatsApp</a>
            </div>
            <div class="footer">
                <p><strong>Endall Inspeções</strong></p>
                <p>' . EMPRESA_TELEFONE . ' | ' . EMPRESA_EMAIL . '</p>
                <p>' . EMPRESA_ENDERECO . '</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    return $html;
}
?>
```

---

## ⏰ Cronograma Sugerido

| Tarefa | Prazo | Horas |
|--------|-------|-------|
| Página do Produto | 2 dias | 6h |
| Geração de PDF | 2 dias | 8h |
| Sistema de E-mails | 2 dias | 6h |
| **Testes Integrados** | 1 dia | 4h |
| **TOTAL FASE 2** | **7 dias** | **24h** |

---

## 🔧 Configurações Necessárias

### 1. Composer (Gerenciador de Dependências)

```bash
# Instalar Composer (se ainda não tiver)
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Criar composer.json
cd /caminho/para/vendas
composer init

# Instalar dependências
composer require mpdf/mpdf
composer require phpmailer/phpmailer
```

### 2. Credenciais SMTP (Gmail como exemplo)

1. Acessar: https://myaccount.google.com/apppasswords
2. Gerar senha de aplicativo
3. Configurar em `includes/config.php`:
   ```php
   define('SMTP_USER', 'comercial@endall.com.br');
   define('SMTP_PASS', 'xxxx xxxx xxxx xxxx'); // Senha de aplicativo
   ```

### 3. Logo da Empresa

- Obter logo oficial da Endall em alta resolução
- Salvar em `assets/img/logo-endall.png`
- Usar no PDF e e-mails

---

## ✅ Critérios de Aceitação (Fase 2)

### Página do Produto
- [ ] Todas as informações são exibidas corretamente
- [ ] Galeria de imagens funciona perfeitamente
- [ ] Botão "Adicionar ao Orçamento" integrado
- [ ] Produtos relacionados aparecem
- [ ] Responsivo em todos os dispositivos

### Geração de PDF
- [ ] PDF gerado com identidade visual Endall
- [ ] Todas as informações estão corretas
- [ ] PDF salvo corretamente em `uploads/pdfs/`
- [ ] Download funciona
- [ ] Impressão está formatada

### Sistema de E-mails
- [ ] E-mail enviado para cliente após orçamento
- [ ] E-mail enviado para empresa (notificação)
- [ ] PDF anexado corretamente
- [ ] Templates HTML bem formatados
- [ ] E-mails chegam na caixa de entrada (não spam)
- [ ] Log de envios registrado

---

## 📚 Recursos Úteis

### Documentação

- **mPDF:** https://mpdf.github.io/
- **PHPMailer:** https://github.com/PHPMailer/PHPMailer
- **Composer:** https://getcomposer.org/doc/

### Tutoriais

- mPDF Basics: https://mpdf.github.io/getting-started/introduction.html
- PHPMailer Gmail: https://github.com/PHPMailer/PHPMailer/wiki/Using-Gmail-with-XOAUTH2

---

## 🐛 Possíveis Problemas

### mPDF
- **Erro:** "Call to undefined function mb_substr"
  - **Solução:** Instalar extensão `php-mbstring`

- **Erro:** Fontes não carregam
  - **Solução:** Verificar permissões da pasta `vendor/mpdf/mpdf/tmp`

### PHPMailer
- **Erro:** SMTP Error: Could not connect
  - **Solução:** Verificar firewall, usar porta 587 (TLS)

- **Erro:** SMTP Error: Authentication failed
  - **Solução:** Usar senha de aplicativo do Gmail, não senha da conta

### Geral
- **Erro:** Timeout ao gerar PDF
  - **Solução:** Aumentar `max_execution_time` no php.ini

---

## 📞 Contato

**Dúvidas?** Entre em contato:
- E-mail: comercial@endall.com.br
- WhatsApp: (11) 98765-4321

---

**Boa sorte e bom desenvolvimento! 🚀**

*Última atualização: 12 de Março de 2026*
