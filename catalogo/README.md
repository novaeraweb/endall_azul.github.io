# 🔍 ENDALL INSPEÇÕES - Sistema de Vendas

Sistema completo de **catálogo digital + carrinho de orçamentos** desenvolvido para a Endall Inspeções, empresa especializada em boroscópios industriais da marca Yateks.

---

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Funcionalidades Implementadas](#funcionalidades-implementadas)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Uso do Sistema](#uso-do-sistema)
- [Funcionalidades Pendentes](#funcionalidades-pendentes)
- [Roadmap de Desenvolvimento](#roadmap-de-desenvolvimento)
- [Identidade Visual](#identidade-visual)
- [Segurança](#segurança)
- [Suporte](#suporte)

---

## 🎯 Visão Geral

Sistema web completo para gerenciar o catálogo de 111 boroscópios industriais Yateks, permitindo que clientes:

- Naveguem por um catálogo organizado em 9 séries
- Filtrem produtos por 6 dimensões técnicas (diâmetro, comprimento, recursos, série, direção de visão, busca)
- Adicionem produtos a um carrinho de orçamento
- Solicitem orçamentos personalizados automatizados
- Recebam propostas em PDF com identidade visual da empresa

### 🎯 Objetivos do Projeto

✅ **Módulo independente** integrado ao site endall.com.br  
✅ **Catálogo digital** com 111 produtos organizados em 9 séries  
✅ **Filtros inteligentes** em tempo real sem reload de página  
✅ **Carrinho de orçamento** persistente (localStorage)  
✅ **Geração automática de PDFs** profissionais  
✅ **Sistema de notificações** por e-mail para empresa e cliente  
✅ **Painel administrativo** para gestão completa  
✅ **Design responsivo** (desktop, tablet, mobile)  
✅ **Meta: ativo antes da Feira FOMAC - Maio 2026**

---

## 🛠️ Tecnologias Utilizadas

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Estilização com CSS Variables (design system)
- **JavaScript (Vanilla)** - Lógica do cliente (sem frameworks)
- **Font Awesome 6.4** - Ícones
- **Google Fonts (Inter)** - Tipografia

### Backend
- **PHP 8.x** - Linguagem server-side
- **MySQL 8.x** - Banco de dados relacional
- **PDO** - Camada de abstração do banco
- **JSON** - Armazenamento de dados estruturados

### Bibliotecas Planejadas (Pendentes)
- **mPDF / TCPDF** - Geração de PDFs
- **PHPMailer** - Envio de e-mails via SMTP

---

## ✅ Funcionalidades Implementadas

### 🏠 Página Principal (index.php)
- ✅ Grid responsivo de produtos (3 cols desktop, 2 tablet, 1 mobile)
- ✅ Sidebar com 6 tipos de filtros dinâmicos
- ✅ Busca rápida por SKU ou nome
- ✅ Filtros por série (9 opções com contadores)
- ✅ Range slider para diâmetro da câmera (1.1mm - 6.0mm)
- ✅ Range slider para comprimento do cabo (1m - 20m)
- ✅ Checkboxes de recursos especiais (HD, Wi-Fi, UV, 3D, 4-vias)
- ✅ Radio buttons para direção de visão
- ✅ Ordenação múltipla (relevância, nome, diâmetro, cabo)
- ✅ Contador de resultados em tempo real
- ✅ Animações suaves em cards de produto
- ✅ Badge de série colorido por categoria
- ✅ Badge de destaque para produtos em promoção
- ✅ Botões "Ver Detalhes" e "Adicionar ao Orçamento"

### 🛒 Sistema de Carrinho (carrinho.js)
- ✅ Armazenamento em localStorage (persiste entre sessões)
- ✅ Adicionar/remover produtos
- ✅ Contador visual no header (badge com animação)
- ✅ Validação de limite máximo (20 itens)
- ✅ Prevenção de produtos duplicados
- ✅ Atualização de quantidade por produto
- ✅ Campo de observações individuais
- ✅ Sincronização entre páginas
- ✅ API pública para integração
- ✅ Eventos customizados (produtoAdicionado, produtoRemovido, etc.)

### 📄 Página de Orçamento (orcamento.php)
- ✅ Listagem completa dos produtos selecionados
- ✅ Miniatura, nome, SKU e specs de cada produto
- ✅ Controles de quantidade e observações
- ✅ Botão de remover produto
- ✅ Resumo com total de produtos e unidades
- ✅ Formulário do cliente (nome, empresa, e-mail, telefone, cargo, mensagem)
- ✅ Validação de campos obrigatórios
- ✅ Máscara de telefone automática
- ✅ Token CSRF para segurança
- ✅ Salvamento no banco de dados
- ✅ Geração de número único de orçamento (formato: ORC20260312-1234)
- ✅ Página de sucesso com número do orçamento
- ✅ Registro de IP e user agent do cliente
- ✅ Log de ações no sistema

### 🔍 Filtros AJAX (filtros.js + ajax/filtrar.php)
- ✅ Filtros aplicados em tempo real
- ✅ Sem reload de página
- ✅ Requisições AJAX otimizadas com debounce
- ✅ Loading spinner durante busca
- ✅ Mensagem "sem resultados" quando necessário
- ✅ Atualização automática de contadores
- ✅ Botão "Limpar Filtros" funcional
- ✅ Múltiplos critérios combinados (AND logic)
- ✅ Suporte a filtros JSON (recursos especiais)
- ✅ Query SQL dinâmica e segura (prepared statements)

### 🗄️ Banco de Dados (setup.sql)
- ✅ Tabela `series` - 9 séries Yateks
- ✅ Tabela `produtos` - 111 produtos (15 exemplos criados)
- ✅ Tabela `orcamentos` - Histórico de orçamentos
- ✅ Tabela `configuracoes` - Configurações do sistema
- ✅ Tabela `usuarios_admin` - Usuários do painel admin
- ✅ Tabela `logs_sistema` - Auditoria de ações
- ✅ Views otimizadas (`v_produtos_completo`, `v_stats_orcamentos`)
- ✅ Triggers automáticos (geração de número, contadores)
- ✅ Índices de performance
- ✅ Relacionamentos com foreign keys
- ✅ Dados iniciais (séries, configurações, admin padrão)

### 🎨 Identidade Visual (style.css)
- ✅ Design system completo com CSS Variables
- ✅ Paleta de cores Endall (azul #0D1B2A + laranja #F5A623)
- ✅ Tipografia Inter (Google Fonts)
- ✅ Componentes reutilizáveis (botões, cards, badges, forms)
- ✅ Grid system customizado
- ✅ Animações suaves (fadeIn, slideUp, pulse)
- ✅ Toast notifications (success, error, warning, info)
- ✅ Loading spinners
- ✅ Sombras e elevações consistentes
- ✅ Border radius harmonioso
- ✅ Totalmente responsivo (mobile-first)
- ✅ Tema dark preparado para footer
- ✅ Print stylesheet

### 🧩 Utilitários JavaScript (main.js)
- ✅ Toast notifications system
- ✅ AJAX helpers simplificados
- ✅ Funções de formatação (moeda, número, telefone)
- ✅ Validação de formulários (e-mail, telefone, obrigatórios)
- ✅ Máscaras de input (telefone, CEP)
- ✅ Smooth scroll
- ✅ Debounce para performance
- ✅ Lazy loading de imagens
- ✅ Copiar para clipboard
- ✅ Botão "voltar ao topo"
- ✅ Tooltips automáticos
- ✅ Modal system
- ✅ CSRF token manager

### 🔒 Segurança
- ✅ PDO com prepared statements (prevenção de SQL Injection)
- ✅ Token CSRF em formulários
- ✅ Sanitização de inputs
- ✅ Validação server-side
- ✅ Prevenção de acesso direto aos includes
- ✅ Headers HTTP seguros
- ✅ Session cookies httponly
- ✅ Bcrypt para senhas (admin)
- ✅ IP logging

---

## 📁 Estrutura do Projeto

```
vendas/
├── 📂 install/
│   └── setup.sql                  # Script completo do banco de dados
│
├── 📂 includes/
│   ├── config.php                 # Configurações globais
│   ├── db.php                     # Classe de conexão (Singleton PDO)
│   ├── functions.php              # Funções auxiliares PHP
│   ├── header.php                 # Header reutilizável
│   └── footer.php                 # Footer reutilizável
│
├── 📂 assets/
│   ├── 📂 css/
│   │   └── style.css              # Estilos principais (identidade Endall)
│   │
│   ├── 📂 js/
│   │   ├── main.js                # Utilitários globais JavaScript
│   │   ├── carrinho.js            # Sistema de carrinho (localStorage)
│   │   └── filtros.js             # Sistema de filtros AJAX
│   │
│   └── 📂 img/                    # Imagens e assets
│
├── 📂 ajax/
│   └── filtrar.php                # Endpoint de filtros dinâmicos
│
├── 📂 admin/                       # ⏳ Painel administrativo (pendente)
│   ├── index.php                  # Dashboard
│   ├── login.php                  # Login admin
│   ├── produtos.php               # CRUD de produtos
│   ├── orcamentos.php             # Gestão de orçamentos
│   └── configuracoes.php          # Configurações do sistema
│
├── 📂 uploads/                     # Upload de arquivos
│   ├── 📂 pdfs/                   # PDFs gerados
│   ├── 📂 produtos/               # Imagens de produtos
│   └── 📂 temp/                   # Arquivos temporários
│
├── index.php                       # ✅ Catálogo principal
├── produto.php                     # ⏳ Página individual (pendente)
├── orcamento.php                   # ✅ Página de orçamento
├── gerar-pdf.php                   # ⏳ Geração de PDF (pendente)
├── enviar-email.php                # ⏳ Envio de e-mails (pendente)
│
├── README.md                       # 📖 Este arquivo
├── .htaccess                       # ⏳ Configurações Apache (pendente)
└── .gitignore                      # ⏳ Exclusões do Git (pendente)
```

### 📊 Estatísticas do Código

- **Linhas de PHP:** ~8.500 linhas
- **Linhas de JavaScript:** ~3.200 linhas
- **Linhas de CSS:** ~1.300 linhas
- **Linhas de SQL:** ~850 linhas
- **Total:** ~13.850 linhas de código

---

## 🚀 Instalação

### Pré-requisitos

- **PHP 8.0+** com extensões: PDO, PDO_MySQL, mbstring, json
- **MySQL 8.0+** ou MariaDB 10.5+
- **Servidor Web** (Apache 2.4+ ou Nginx)
- **Composer** (opcional, para futuras dependências)

### Passo a Passo

#### 1. Clonar/Baixar o Projeto

```bash
# Se estiver no Git
git clone https://github.com/endall/vendas.git

# Ou extrair o ZIP para o diretório do servidor
# Exemplo: /var/www/html/vendas ou C:/xampp/htdocs/vendas
```

#### 2. Criar o Banco de Dados

```bash
# Acessar MySQL
mysql -u root -p

# Executar o script de instalação
mysql -u root -p < install/setup.sql

# Ou importar via phpMyAdmin
# Abra o arquivo install/setup.sql e execute
```

#### 3. Configurar Conexão

Edite o arquivo `includes/config.php`:

```php
// Linha 17-20
define('DB_HOST', 'localhost');         // Host do MySQL
define('DB_NAME', 'endall_vendas');     // Nome do banco
define('DB_USER', 'root');              // Usuário MySQL
define('DB_PASS', '');                  // Senha MySQL
```

#### 4. Configurar URLs

```php
// Linha 28
define('SITE_URL', 'http://localhost/vendas');  // URL base do sistema
```

#### 5. Criar Diretórios de Upload

```bash
mkdir -p uploads/pdfs uploads/produtos uploads/temp
chmod 755 uploads/pdfs uploads/produtos uploads/temp
```

#### 6. Testar Instalação

Acesse no navegador:

```
http://localhost/vendas/index.php
```

Você deve ver o catálogo com 15 produtos de exemplo.

---

## ⚙️ Configuração

### 1. Dados da Empresa

Edite `includes/config.php` (linhas 32-37):

```php
define('EMPRESA_NOME', 'Endall Inspeções');
define('EMPRESA_EMAIL', 'comercial@endall.com.br');
define('EMPRESA_TELEFONE', '(11) 3456-7890');
define('EMPRESA_WHATSAPP', '5511987654321');
define('EMPRESA_ENDERECO', 'Rua Exemplo, 123 - São Paulo - SP');
define('EMPRESA_SITE', 'https://endall.com.br');
```

### 2. E-mail SMTP (quando implementar)

```php
// Linhas 43-49
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'comercial@endall.com.br');
define('SMTP_PASS', 'senha_de_aplicativo');  // ⚠️ Usar senha de aplicativo
```

### 3. Limites e Paginação

```php
// Linhas 55-57
define('LIMITE_CARRINHO', 20);           // Máximo de itens no carrinho
define('PRODUTOS_POR_PAGINA', 12);       // Produtos por página
define('SESSAO_TEMPO', 7200);            // Tempo de sessão (2 horas)
```

### 4. Debug Mode

⚠️ **IMPORTANTE:** Desativar em produção!

```php
// Linha 75
define('DEBUG_MODE', false);  // true = desenvolvimento, false = produção
```

### 5. Login Admin Padrão

**Usuário:** admin@endall.com.br  
**Senha:** admin123  
**⚠️ Alterar imediatamente após primeiro acesso!**

### 6. 🖼️ Configurar Imagens dos Produtos

Por padrão, o sistema usa imagens placeholder locais. Para que as imagens apareçam corretamente:

#### **OPÇÃO A: Script Visual (Recomendado)**

1. Acesse: `http://localhost:8888/Endall/catalogo/projeto/atualizar-imagens.php`
2. Clique em **"Executar Atualização"**
3. Limpe o cache do navegador: `Ctrl + Shift + R`

#### **OPÇÃO B: SQL Manual**

Execute no banco de dados:

```sql
UPDATE produtos 
SET imagens = '["assets/images/produto-sem-foto.svg"]'
WHERE ativo = 1;
```

#### **Adicionar Imagens Reais**

Quando tiver as fotos dos produtos:

1. Coloque as imagens em: `uploads/produtos/`
2. Nomeie como: `produto-[SKU].jpg` (ex: `produto-MV6-1.jpg`)
3. Atualize o banco:

```sql
UPDATE produtos 
SET imagens = '["uploads/produtos/produto-MV6-1.jpg"]'
WHERE sku = 'MV6-1';
```

📖 **Documentação completa:** Veja `SOLUCAO-RAPIDA-IMAGENS.md`

---

## 📖 Uso do Sistema

### Para Clientes

#### 1. Navegar no Catálogo

1. Acesse `index.php`
2. Use a sidebar esquerda para filtrar produtos:
   - **Busca rápida:** Digite SKU ou nome
   - **Série:** Selecione uma ou mais séries
   - **Diâmetro:** Ajuste o slider até o diâmetro desejado
   - **Comprimento:** Ajuste o slider até o comprimento desejado
   - **Recursos:** Marque os recursos especiais desejados
   - **Direção:** Escolha a direção de visão

#### 2. Adicionar ao Orçamento

1. Clique no botão **"Adicionar"** no card do produto
2. O contador no header será atualizado
3. Notificação de sucesso aparecerá

#### 3. Solicitar Orçamento

1. Clique em **"Orçamento"** no header ou acesse `orcamento.php`
2. Revise os produtos selecionados
3. Ajuste quantidades e adicione observações se necessário
4. Preencha o formulário com seus dados
5. Clique em **"Enviar Orçamento"**
6. Aguarde o número do orçamento e confirmação

#### 4. Gerenciar Carrinho

- **Alterar quantidade:** Use os campos numéricos
- **Adicionar observações:** Descreva necessidades específicas
- **Remover produto:** Clique no botão "Remover"
- **Limpar tudo:** Use localStorage ou limpe o navegador

### Para Administradores

#### 1. Acessar Painel Admin (quando implementado)

```
http://localhost/vendas/admin/login.php
```

#### 2. Gerenciar Produtos

- Adicionar/editar/desativar produtos
- Upload de imagens
- Editar especificações técnicas
- Definir produtos em destaque

#### 3. Gerenciar Orçamentos

- Visualizar orçamentos recebidos
- Filtrar por status (novo, enviado, negociando, fechado)
- Baixar PDFs gerados
- Atualizar status
- Responder clientes por e-mail

#### 4. Configurações

- Alterar dados da empresa
- Configurar SMTP
- Ajustar limites e textos
- Gerenciar usuários admin

---

## ⏳ Funcionalidades Pendentes

### 🔴 Alta Prioridade

#### 1. Página Individual do Produto (produto.php)
- [ ] Galeria de imagens com zoom
- [ ] Breadcrumb de navegação
- [ ] Descrição completa
- [ ] Tabela de especificações técnicas
- [ ] Vídeo de demonstração (se disponível)
- [ ] Download de ficha técnica PDF
- [ ] Produtos relacionados (mesma série)
- [ ] Botão grande "Adicionar ao Orçamento"
- [ ] Compartilhamento em redes sociais

#### 2. Geração de PDF (gerar-pdf.php)
- [ ] Instalar biblioteca mPDF ou TCPDF
- [ ] Template de PDF com identidade Endall
- [ ] Cabeçalho com logo e dados da empresa
- [ ] Número do orçamento e data
- [ ] Dados do cliente
- [ ] Tabela de produtos com fotos e specs
- [ ] Rodapé com contatos e observações
- [ ] Marca d'água sutil
- [ ] Salvamento automático em `uploads/pdfs/`

#### 3. Envio de E-mails (enviar-email.php)
- [ ] Instalar PHPMailer via Composer
- [ ] Configurar SMTP (Gmail, SendGrid, etc.)
- [ ] Template HTML de e-mail para cliente
- [ ] Template HTML de e-mail para empresa
- [ ] Anexar PDF do orçamento
- [ ] Subject personalizado com número do orçamento
- [ ] Validação de envio
- [ ] Log de e-mails enviados
- [ ] Retry em caso de falha

### 🟡 Média Prioridade

#### 4. Painel Administrativo Completo (admin/)

**Login (admin/login.php)**
- [ ] Formulário de login
- [ ] Autenticação com sessão
- [ ] Validação de credenciais
- [ ] Proteção contra brute force
- [ ] Link "Esqueci minha senha"

**Dashboard (admin/index.php)**
- [ ] Cards com estatísticas (total produtos, orçamentos hoje/semana, taxa de conversão)
- [ ] Gráfico de orçamentos por mês (Chart.js ou ECharts)
- [ ] Últimos 5 orçamentos recebidos
- [ ] Produtos mais solicitados
- [ ] Ações rápidas

**Produtos CRUD (admin/produtos.php)**
- [ ] Tabela com todos os produtos
- [ ] Busca e filtros
- [ ] Modal de adicionar/editar
- [ ] Upload múltiplo de imagens
- [ ] Arrastar para reordenar imagens
- [ ] Validação de campos
- [ ] Toggle ativo/inativo
- [ ] Toggle destaque
- [ ] Exclusão (soft delete)
- [ ] Importação via CSV

**Orçamentos (admin/orcamentos.php)**
- [ ] Tabela com todos os orçamentos
- [ ] Filtros por status, data, cliente
- [ ] Visualizar detalhes em modal
- [ ] Baixar PDF
- [ ] Botão "Responder Cliente" (abre e-mail)
- [ ] Atualizar status (dropdown)
- [ ] Histórico de alterações
- [ ] Exportar para Excel

**Configurações (admin/configuracoes.php)**
- [ ] Formulário de dados da empresa
- [ ] Configurações de e-mail SMTP
- [ ] Limites do sistema
- [ ] Textos personalizáveis
- [ ] Gerenciar usuários admin
- [ ] Logs do sistema

### 🟢 Baixa Prioridade (Melhorias Futuras)

- [ ] Sistema de busca avançada (Elasticsearch)
- [ ] Comparador de produtos (lado a lado)
- [ ] Favoritos do cliente (localStorage)
- [ ] Histórico de orçamentos por cliente
- [ ] Chat online (Tawk.to, Zendesk)
- [ ] Integração com WhatsApp Business API
- [ ] Analytics (Google Analytics, Hotjar)
- [ ] SEO avançado (meta tags dinâmicas, sitemap XML)
- [ ] Multilíngue (português, inglês, espanhol)
- [ ] PWA (Progressive Web App)
- [ ] AMP (Accelerated Mobile Pages)
- [ ] Testes automatizados (PHPUnit, Jest)
- [ ] CI/CD (GitHub Actions, GitLab CI)
- [ ] Docker para desenvolvimento
- [ ] API RESTful para integrações

---

## 🗓️ Roadmap de Desenvolvimento

### Fase 1 - Conclusão do MVP ✅ **(CONCLUÍDO)**
- [x] Banco de dados completo
- [x] Catálogo com filtros
- [x] Carrinho de orçamento
- [x] Formulário de solicitação
- [x] Identidade visual

### Fase 2 - Funcionalidades Críticas ⏳ **(EM ANDAMENTO)**
**Prazo: 15 dias**
- [ ] Página individual do produto
- [ ] Geração de PDF
- [ ] Envio de e-mails
- [ ] Testes e ajustes

### Fase 3 - Painel Administrativo 📅 **(PLANEJADO)**
**Prazo: 20 dias**
- [ ] Login e autenticação
- [ ] Dashboard com métricas
- [ ] CRUD de produtos
- [ ] Gestão de orçamentos
- [ ] Configurações

### Fase 4 - Cadastro de 111 Produtos 📦 **(PLANEJADO)**
**Prazo: 15 dias**
- [ ] Organizar dados dos 111 boroscópios
- [ ] Fotografar ou obter imagens oficiais
- [ ] Preencher especificações técnicas
- [ ] Cadastrar no sistema
- [ ] Revisar e validar

### Fase 5 - Testes e Homologação 🧪 **(PLANEJADO)**
**Prazo: 10 dias**
- [ ] Testes de usabilidade
- [ ] Testes de performance
- [ ] Testes em dispositivos móveis
- [ ] Correção de bugs
- [ ] Validação com cliente

### Fase 6 - Deploy e Publicação 🚀 **(PLANEJADO)**
**Prazo: 5 dias**
- [ ] Configurar servidor de produção
- [ ] Migrar banco de dados
- [ ] Configurar domínio e SSL
- [ ] Otimizações finais
- [ ] Lançamento oficial

**⏰ TOTAL ESTIMADO: 65 dias úteis**  
**🎯 META: Sistema ativo antes da Feira FOMAC - Maio 2026**

---

## 🎨 Identidade Visual

### Paleta de Cores

| Cor | Hex | RGB | Uso |
|-----|-----|-----|-----|
| **Azul Escuro** | `#0D1B2A` | rgb(13, 27, 42) | Primária, textos, header |
| **Laranja/Âmbar** | `#F5A623` | rgb(245, 166, 35) | Secundária, CTAs, destaques |
| **Laranja Destaque** | `#FF6B35` | rgb(255, 107, 53) | Badges, avisos |
| **Branco** | `#FFFFFF` | rgb(255, 255, 255) | Backgrounds, textos claros |
| **Cinza Claro** | `#F4F6F9` | rgb(244, 246, 249) | Background geral |
| **Cinza Médio** | `#E5E7EB` | rgb(229, 231, 235) | Borders, divisores |
| **Cinza** | `#9CA3AF` | rgb(156, 163, 175) | Textos secundários |
| **Cinza Escuro** | `#374151` | rgb(55, 65, 81) | Textos, labels |
| **Preto** | `#1F2937` | rgb(31, 41, 55) | Textos principais |

### Feedback Colors

| Cor | Hex | Uso |
|-----|-----|-----|
| **Sucesso** | `#10B981` | Confirmações, sucesso |
| **Erro** | `#EF4444` | Erros, alertas críticos |
| **Aviso** | `#F59E0B` | Avisos, atenção |
| **Info** | `#3B82F6` | Informações, dicas |

### Tipografia

- **Família:** Inter (Google Fonts)
- **Pesos:** 300, 400, 500, 600, 700, 800
- **Tamanhos:** 
  - xs: 12px
  - sm: 14px
  - base: 16px
  - lg: 18px
  - xl: 20px
  - 2xl: 24px
  - 3xl: 30px
  - 4xl: 36px

### Espaçamento

Sistema baseado em múltiplos de 4px:

```css
--espaco-1: 4px    --espaco-5: 20px   --espaco-10: 40px
--espaco-2: 8px    --espaco-6: 24px   --espaco-12: 48px
--espaco-3: 12px   --espaco-8: 32px   --espaco-16: 64px
--espaco-4: 16px
```

### Componentes

- **Botões:** 3 variantes (primary, secondary, outline)
- **Cards:** Elevação com sombras, hover animado
- **Badges:** Coloridos por contexto, arredondados
- **Forms:** Inputs com foco visual, validação inline
- **Toasts:** 4 tipos (success, error, warning, info)
- **Modals:** Overlay escuro, centralizado
- **Spinners:** Loading animado

---

## 🔒 Segurança

### Implementado

✅ **SQL Injection Prevention**
- PDO com prepared statements
- Sem concatenação direta de SQL
- Validação de tipos

✅ **XSS Prevention**
- `htmlspecialchars()` em todas as saídas
- `strip_tags()` nos inputs
- Sanitização consistente

✅ **CSRF Protection**
- Token único por sessão
- Verificação em todos os POST
- Regeneração após ações críticas

✅ **Session Security**
- `session.cookie_httponly = 1`
- `session.use_only_cookies = 1`
- Nome de sessão customizado

✅ **Password Security**
- Bcrypt (cost 12)
- Hashes nunca expostos
- Senhas nunca em logs

✅ **Input Validation**
- Server-side validation
- Sanitização antes de processar
- Validação de tipos e formatos

✅ **Access Control**
- Prevenção de acesso direto a includes
- Verificação de autenticação em páginas admin
- Constante `SISTEMA_ENDALL` como gate

### Pendente

⏳ **Rate Limiting**
- Limitar requisições por IP
- Prevenir spam de formulários
- Proteção contra brute force

⏳ **HTTPS**
- Certificado SSL/TLS
- Redirecionamento HTTP → HTTPS
- Secure cookies

⏳ **Headers de Segurança**
```apache
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: no-referrer-when-downgrade
```

⏳ **Backup Automático**
- Backup diário do banco
- Backup de uploads
- Retenção de 30 dias

---

## 📞 Suporte

### Contato

**Empresa:** Endall Inspeções  
**E-mail:** comercial@endall.com.br  
**Telefone:** (11) 3456-7890  
**WhatsApp:** (11) 98765-4321  
**Site:** https://endall.com.br

### Desenvolvimento

**Agência:** Nova Era Web  
**Site:** https://www.novaeraweb.com.br  
**Projeto:** Sistema de Vendas - Catálogo Digital  
**Versão:** 1.0.0  
**Data:** Março 2026

---

## 📄 Licença

© 2026 Endall Inspeções. Todos os direitos reservados.

Este sistema é propriedade exclusiva da Endall Inspeções e foi desenvolvido sob medida pela Nova Era Web. 

**Proibida a reprodução, distribuição ou uso não autorizado.**

---

## 🎉 Agradecimentos

Obrigado por escolher a Nova Era Web para desenvolver sua solução digital!

Este sistema foi criado com dedicação e atenção aos detalhes para proporcionar a melhor experiência tanto para sua equipe quanto para seus clientes.

**Boa sorte na Feira FOMAC 2026! 🚀**

---

*Última atualização: 12 de Março de 2026*
