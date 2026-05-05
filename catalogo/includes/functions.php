<?php
/**
 * ENDALL INSPEÇÕES - Sistema de Vendas
 * Funções Auxiliares Globais
 * 
 * @package EndallVendas
 * @version 1.0.0
 */

// Prevenir acesso direto
if (!defined('SISTEMA_ENDALL') && !defined('ENDALL_APP')) {
    die('Acesso negado');
}

// =============================================
// FUNÇÕES DE SEGURANÇA
// =============================================

/**
 * Sanitizar string removendo tags HTML e caracteres especiais
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Escapa HTML para safe output (atalho de htmlspecialchars)
 */
if (!function_exists('htmlEsc')) {
    function htmlEsc($value) {
        if (is_null($value)) return '';
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Validar e-mail
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar telefone brasileiro
 */
function validarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    return strlen($telefone) >= 10 && strlen($telefone) <= 11;
}

/**
 * Validar campo obrigatório
 */
function validarObrigatorio($valor) {
    return !empty($valor) && trim($valor) !== '';
}

/**
 * Gerar token CSRF
 */
function gerarCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verificarCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Gerar hash seguro para senhas
 */
function hashSenha($senha) {
    return password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verificar senha com hash
 */
function verificarSenha($senha, $hash) {
    return password_verify($senha, $hash);
}

// =============================================
// FUNÇÕES DE FORMATAÇÃO
// =============================================

/**
 * Formatar valor monetário
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formatar número com decimais
 */
function formatarNumero($numero, $decimais = 1) {
    return number_format($numero, $decimais, ',', '.');
}

/**
 * Formatar telefone
 */
function formatarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    
    if (strlen($telefone) == 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
    } elseif (strlen($telefone) == 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
    }
    
    return $telefone;
}

/**
 * Formatar data brasileira
 */
function formatarData($data, $incluirHora = false) {
    if (empty($data)) return '-';
    
    $timestamp = is_numeric($data) ? $data : strtotime($data);
    
    if ($incluirHora) {
        return date('d/m/Y H:i', $timestamp);
    }
    return date('d/m/Y', $timestamp);
}

/**
 * Formatar data para MySQL
 */
function dataParaMySQL($data) {
    $partes = explode('/', $data);
    if (count($partes) == 3) {
        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }
    return date('Y-m-d');
}

/**
 * Formatar CPF
 */
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) == 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf;
}

/**
 * Formatar CNPJ
 */
function formatarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) == 14) {
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }
    return $cnpj;
}

// =============================================
// FUNÇÕES DE URL E NAVEGAÇÃO
// =============================================

/**
 * Redirecionar para outra página
 */
function redirecionar($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        echo "<script>window.location.href='{$url}';</script>";
        exit;
    }
}

/**
 * Obter URL base
 */
function urlBase($caminho = '') {
    return SITE_URL . ($caminho ? '/' . ltrim($caminho, '/') : '');
}

/**
 * Gerar slug amigável
 */
function gerarSlug($texto) {
    $texto = strtolower($texto);
    $texto = preg_replace('/[áàâãäå]/u', 'a', $texto);
    $texto = preg_replace('/[éèêë]/u', 'e', $texto);
    $texto = preg_replace('/[íìîï]/u', 'i', $texto);
    $texto = preg_replace('/[óòôõö]/u', 'o', $texto);
    $texto = preg_replace('/[úùûü]/u', 'u', $texto);
    $texto = preg_replace('/[ç]/u', 'c', $texto);
    $texto = preg_replace('/[^a-z0-9\s-]/', '', $texto);
    $texto = preg_replace('/[\s-]+/', '-', $texto);
    return trim($texto, '-');
}

/**
 * Obter parâmetro GET
 */
function getParam($key, $default = null) {
    return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
}

/**
 * Obter parâmetro POST
 */
function postParam($key, $default = null) {
    return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
}

// =============================================
// FUNÇÕES DE PRODUTOS
// =============================================

/**
 * Buscar produto por ID
 */
function buscarProduto($id) {
    $sql = "SELECT p.*, s.nome as serie_nome, s.slug as serie_slug, s.cor as serie_cor 
            FROM produtos p 
            INNER JOIN series s ON p.serie_id = s.id 
            WHERE p.id = ? AND p.ativo = 1";
    return db()->queryRow($sql, [$id]);
}

/**
 * Buscar produto por SKU
 */
function buscarProdutoPorSKU($sku) {
    $sql = "SELECT p.*, s.nome as serie_nome, s.slug as serie_slug, s.cor as serie_cor 
            FROM produtos p 
            INNER JOIN series s ON p.serie_id = s.id 
            WHERE p.sku = ? AND p.ativo = 1";
    return db()->queryRow($sql, [$sku]);
}

/**
 * Listar todas as séries
 */
function listarSeries() {
    $sql = "SELECT * FROM series WHERE ativo = 1 ORDER BY ordem ASC";
    return db()->query($sql);
}

/**
 * Buscar série por slug
 */
function buscarSerie($slug) {
    $sql = "SELECT * FROM series WHERE slug = ? AND ativo = 1";
    return db()->queryRow($sql, [$slug]);
}

/**
 * Contar produtos por série
 */
function contarProdutosPorSerie($serie_id) {
    return db()->count('produtos', 'serie_id = ? AND ativo = 1', [$serie_id]);
}

/**
 * Decodificar JSON de recursos especiais
 */
function decodificarRecursos($recursos_json) {
    if (empty($recursos_json)) return [];
    $recursos = json_decode($recursos_json, true);
    return is_array($recursos) ? $recursos : [];
}

/**
 * Decodificar JSON de imagens
 */
function decodificarImagens($imagens_json) {
    if (empty($imagens_json)) return [];
    $imagens = json_decode($imagens_json, true);
    return is_array($imagens) ? $imagens : [];
}

// =============================================
// FUNÇÕES DE CARRINHO
// =============================================

/**
 * Validar estrutura do carrinho
 */
function validarCarrinho($carrinho) {
    if (!is_array($carrinho)) return false;
    if (count($carrinho) > LIMITE_CARRINHO) return false;
    return true;
}

// =============================================
// FUNÇÕES DE ORÇAMENTO
// =============================================

/**
 * Gerar número único de orçamento
 */
function gerarNumeroOrcamento() {
    $data = date('Ymd');
    $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return "ORC{$data}-{$random}";
}

/**
 * Buscar orçamento por número
 */
function buscarOrcamento($numero) {
    $sql = "SELECT * FROM orcamentos WHERE numero = ?";
    return db()->queryRow($sql, [$numero]);
}

/**
 * Atualizar status do orçamento
 */
function atualizarStatusOrcamento($id, $status) {
    $sql = "UPDATE orcamentos SET status = ?, atualizado_em = NOW() WHERE id = ?";
    return db()->execute($sql, [$status, $id]);
}

// =============================================
// FUNÇÕES DE CONFIGURAÇÃO
// =============================================

/**
 * Obter valor de configuração
 */
function getConfig($chave, $default = null) {
    static $cache = [];
    
    if (isset($cache[$chave])) {
        return $cache[$chave];
    }
    
    $sql = "SELECT valor FROM configuracoes WHERE chave = ?";
    $result = db()->queryRow($sql, [$chave]);
    
    $valor = $result ? $result['valor'] : $default;
    $cache[$chave] = $valor;
    
    return $valor;
}

/**
 * Definir valor de configuração
 */
function setConfig($chave, $valor, $descricao = '') {
    $sql = "INSERT INTO configuracoes (chave, valor, descricao) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE valor = ?, descricao = ?";
    return db()->execute($sql, [$chave, $valor, $descricao, $valor, $descricao]);
}

// =============================================
// FUNÇÕES DE UPLOAD
// =============================================

/**
 * Validar upload de imagem
 */
function validarUploadImagem($arquivo) {
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $tamanhoMaximo = 5 * 1024 * 1024; // 5MB
    
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return ['sucesso' => false, 'erro' => 'Erro no upload do arquivo'];
    }
    
    if ($arquivo['size'] > $tamanhoMaximo) {
        return ['sucesso' => false, 'erro' => 'Arquivo muito grande (máximo 5MB)'];
    }
    
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, $extensoesPermitidas)) {
        return ['sucesso' => false, 'erro' => 'Formato de arquivo não permitido'];
    }
    
    return ['sucesso' => true];
}

// =============================================
// FUNÇÕES DE RESPOSTA JSON
// =============================================

/**
 * Retornar resposta JSON de sucesso
 */
function jsonSuccess($data = [], $message = 'Sucesso') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'sucesso' => true,
        'mensagem' => $message,
        'dados' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Retornar resposta JSON de erro
 */
function jsonError($message = 'Erro', $code = 400) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// =============================================
// FUNÇÕES DE LOG
// =============================================

/**
 * Registrar log no sistema
 */
function registrarLog($acao, $descricao = '', $usuario_id = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $sql = "INSERT INTO logs_sistema (usuario_id, acao, descricao, ip) VALUES (?, ?, ?, ?)";
    return db()->execute($sql, [$usuario_id, $acao, $descricao, $ip]);
}

// =============================================
// FUNÇÕES DE SESSÃO ADMIN
// =============================================

/**
 * Verificar se usuário está logado no admin
 */
function adminLogado() {
    return isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true;
}

/**
 * Obter dados do admin logado
 */
function adminDados() {
    return $_SESSION['admin_dados'] ?? null;
}

/**
 * Verificar se admin é super admin
 */
function isSuperAdmin() {
    $dados = adminDados();
    return $dados && $dados['nivel'] === 'admin';
}

/**
 * Fazer logout do admin
 */
function adminLogout() {
    unset($_SESSION['admin_logado']);
    unset($_SESSION['admin_dados']);
    session_destroy();
}

// =============================================
// FUNÇÕES DIVERSAS
// =============================================

/**
 * Truncar texto
 */
function truncarTexto($texto, $limite = 100, $sufixo = '...') {
    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }
    return mb_substr($texto, 0, $limite) . $sufixo;
}

/**
 * Calcular tempo decorrido
 */
function tempoDecorrido($timestamp) {
    $diferenca = time() - strtotime($timestamp);
    
    if ($diferenca < 60) {
        return 'há ' . $diferenca . ' segundos';
    } elseif ($diferenca < 3600) {
        return 'há ' . floor($diferenca / 60) . ' minutos';
    } elseif ($diferenca < 86400) {
        return 'há ' . floor($diferenca / 3600) . ' horas';
    } elseif ($diferenca < 2592000) {
        return 'há ' . floor($diferenca / 86400) . ' dias';
    } else {
        return formatarData($timestamp);
    }
}

/**
 * Obter IP do visitante
 */
function obterIP() {
    $ip = 'UNKNOWN';
    
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return $ip;
}

/**
 * Verificar se é requisição AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Debug (apenas se DEBUG_MODE ativo)
 */
function debug($var, $exit = false) {
    if (!DEBUG_MODE) return;
    
    echo '<pre style="background:#2d2d2d;color:#f8f8f2;padding:15px;margin:10px;border-radius:5px;font-family:monospace;font-size:14px;">';
    print_r($var);
    echo '</pre>';
    
    if ($exit) exit;
}

?>
