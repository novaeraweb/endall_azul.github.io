<?php
/**
 * ENDALL INSPEÇÕES - Sistema de Vendas
 * Arquivo de Configurações Globais
 * 
 * @package EndallVendas
 * @version 1.0.0
 */

// Prevenir acesso direto
if (!defined('SISTEMA_ENDALL') && !defined('ENDALL_APP')) {
    die('Acesso negado');
}

// =============================================
// CONFIGURAÇÕES DO BANCO DE DADOS
// =============================================
// Ambiente local mantém os dados de localhost.
// Em produção/Umbler, usa os dados do servidor.
$serverHost = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = in_array($serverHost, ['localhost', '127.0.0.1', '::1'], true)
    || strpos($serverHost, 'localhost:') === 0
    || strpos($serverHost, '127.0.0.1:') === 0
    || PHP_SAPI === 'cli';

if ($isLocal) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'endall_vendas');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
} else {
    define('DB_HOST', getenv('ENDALL_DB_HOST') ?: 'mysql669.umbler.com');
    define('DB_NAME', getenv('ENDALL_DB_NAME') ?: 'endall_azul');
    define('DB_USER', getenv('ENDALL_DB_USER') ?: 'endall_azul');
    define('DB_PASS', getenv('ENDALL_DB_PASS') ?: 'eis*010203');
}

define('DB_CHARSET', 'utf8mb4');

// =============================================
// CONFIGURAÇÕES DO SISTEMA
// =============================================
define('SITE_NAME', 'Endall Inspeções - Sistema de Vendas');

// SITE_URL é detectado dinamicamente a partir do servidor.
// Funciona em qualquer ambiente (localhost, staging, produção)
if (!defined('SITE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Detecta o caminho base do catálogo (até /catalogo)
    $script = $_SERVER['SCRIPT_NAME'] ?? '/catalogo/index.php';
    $catalogPath = '';
    if (preg_match('#^(.*?/catalogo)(/|$)#', $script, $m)) {
        $catalogPath = $m[1];
    } else {
        $catalogPath = '/catalogo';
    }
    define('SITE_URL', $proto . '://' . $host . $catalogPath);
}

define('BASE_PATH', __DIR__ . '/..');

// =============================================
// CONFIGURAÇÕES DA EMPRESA
// =============================================
define('EMPRESA_NOME', 'Endall Inspeções');
define('EMPRESA_EMAIL', 'allan@endall.com.br');
define('EMPRESA_TELEFONE', '(19) 3132-0658');
define('TELEFONE', '(19) 3132-0658'); // Alias para compatibilidade
define('EMPRESA_WHATSAPP', '5519999088253');
define('WHATSAPP', '5519999088253'); // Alias para compatibilidade
define('EMPRESA_ENDERECO', 'Rua Silvério Cressoni, 311 | Jd. Santa Efigênia - Araras/SP');
define('EMPRESA_SITE', 'https://endall.com.br');

// =============================================
// CONFIGURAÇÕES DE E-MAIL (SMTP)
// =============================================
define('SMTP_HOST', 'smtp.umbler.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // tls ou ssl
define('SMTP_USER', 'site@endall.com.br');
define('SMTP_PASS', 'UT_qZY9!y.8n2');
define('SMTP_FROM_NAME', 'Endall Inspeções');
define('SMTP_FROM_EMAIL', 'site@endall.com.br');

// =============================================
// CONFIGURAÇÕES DO SISTEMA
// =============================================
define('LIMITE_CARRINHO', 20); // Máximo de itens no carrinho
define('PRODUTOS_POR_PAGINA', 12); // Produtos exibidos por página
define('SESSAO_TEMPO', 7200); // Tempo de sessão em segundos (2 horas)

// =============================================
// DIRETÓRIOS
// =============================================
define('DIR_UPLOADS', BASE_PATH . '/uploads');
define('DIR_PDFS', BASE_PATH . '/uploads/pdfs');
define('DIR_PRODUTOS', BASE_PATH . '/uploads/produtos');
define('DIR_TEMP', BASE_PATH . '/uploads/temp');

// =============================================
// URLS
// =============================================
define('URL_ASSETS', SITE_URL . '/assets');
define('URL_UPLOADS', SITE_URL . '/uploads');
define('URL_PRODUTOS', SITE_URL . '/uploads/produtos');

// =============================================
// SEGURANÇA
// =============================================
define('SALT_KEY', 'endall_2026_seguro_xyz123'); // Alterar em produção
define('SESSION_NAME', 'endall_vendas_session');

// =============================================
// DEBUG (desativar em produção)
// =============================================
define('DEBUG_MODE', $isLocal);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// =============================================
// TIMEZONE
// =============================================
date_default_timezone_set('America/Sao_Paulo');

// =============================================
// CONFIGURAÇÕES DE SESSÃO
// =============================================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', $isLocal ? 0 : 1); // HTTPS em produção

// =============================================
// VERSÃO DO SISTEMA (para cache busting)
// =============================================
define('SYSTEM_VERSION', '1.0.0');
define('ASSETS_VERSION', $isLocal ? time() : SYSTEM_VERSION); // cache busting local / versão fixa em produção

// =============================================
// CONSTANTES ÚTEIS
// =============================================
define('MOEDA_SIMBOLO', 'R$');
define('MOEDA_SEPARADOR_DECIMAL', ',');
define('MOEDA_SEPARADOR_MILHAR', '.');

// =============================================
// MENSAGENS PADRÃO
// =============================================
define('MSG_ERRO_GENERICO', 'Ocorreu um erro. Por favor, tente novamente.');
define('MSG_SUCESSO_ORCAMENTO', 'Orçamento enviado com sucesso! Em breve entraremos em contato.');
define('MSG_CARRINHO_VAZIO', 'Seu carrinho está vazio. Adicione produtos para solicitar um orçamento.');

?>
