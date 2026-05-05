<?php
/**
 * ENDALL INSPEÇÕES - AJAX Filtrar Produtos
 * Endpoint para filtrar produtos dinamicamente
 */

// Definir constante do sistema
define('SISTEMA_ENDALL', true);

// Carregar configurações
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Verificar se é requisição AJAX
if (!isAjax()) {
    jsonError('Requisição inválida', 400);
}

// Obter dados JSON
$json = file_get_contents('php://input');
$filtros = json_decode($json, true);

if ($filtros === null) {
    jsonError('Dados inválidos', 400);
}

// Construir query SQL
$sql = "SELECT p.*, s.nome as serie_nome, s.slug as serie_slug, s.cor as serie_cor
        FROM produtos p
        INNER JOIN series s ON p.serie_id = s.id
        WHERE p.ativo = 1";

$params = [];

// Filtro de busca
if (!empty($filtros['busca'])) {
    $sql .= " AND (p.nome LIKE ? OR p.sku LIKE ? OR p.descricao LIKE ?)";
    $termoBusca = '%' . $filtros['busca'] . '%';
    $params[] = $termoBusca;
    $params[] = $termoBusca;
    $params[] = $termoBusca;
}

// Filtro de séries
if (!empty($filtros['series']) && is_array($filtros['series'])) {
    $placeholders = implode(',', array_fill(0, count($filtros['series']), '?'));
    $sql .= " AND p.serie_id IN ($placeholders)";
    $params = array_merge($params, $filtros['series']);
}

// Filtro de diâmetro
if (isset($filtros['diametro']) && $filtros['diametro'] > 0) {
    $sql .= " AND p.diametro_camera <= ?";
    $params[] = $filtros['diametro'];
}

// Filtro de comprimento de cabo
if (isset($filtros['cabo']) && $filtros['cabo'] > 0) {
    $sql .= " AND p.comprimento_cabo <= ?";
    $params[] = $filtros['cabo'];
}

// Filtro de recursos especiais
if (!empty($filtros['recursos']) && is_array($filtros['recursos'])) {
    foreach ($filtros['recursos'] as $recurso) {
        $sql .= " AND JSON_CONTAINS(p.recursos_especiais, ?)";
        $params[] = json_encode($recurso);
    }
}

// Filtro de direção de visão
if (!empty($filtros['direcao'])) {
    $sql .= " AND p.direcao_visao LIKE ?";
    $params[] = '%' . $filtros['direcao'] . '%';
}

// Ordenação
$ordenacao = isset($filtros['ordenacao']) ? $filtros['ordenacao'] : 'relevancia';
switch ($ordenacao) {
    case 'nome_asc':
        $sql .= " ORDER BY p.nome ASC";
        break;
    case 'nome_desc':
        $sql .= " ORDER BY p.nome DESC";
        break;
    case 'diametro_asc':
        $sql .= " ORDER BY p.diametro_camera ASC";
        break;
    case 'diametro_desc':
        $sql .= " ORDER BY p.diametro_camera DESC";
        break;
    case 'cabo_asc':
        $sql .= " ORDER BY p.comprimento_cabo ASC";
        break;
    case 'cabo_desc':
        $sql .= " ORDER BY p.comprimento_cabo DESC";
        break;
    default: // relevancia
        $sql .= " ORDER BY p.destaque DESC, p.criado_em DESC";
}

// Limite de resultados
$sql .= " LIMIT 100";

// Executar query
try {
    $produtos = db()->query($sql, $params);
    
    // Contar total sem limite (para estatísticas)
    $sqlCount = str_replace("SELECT p.*, s.nome as serie_nome, s.slug as serie_slug, s.cor as serie_cor", "SELECT COUNT(*) as total", $sql);
    $sqlCount = preg_replace('/ORDER BY.*/', '', $sqlCount);
    $sqlCount = preg_replace('/LIMIT.*/', '', $sqlCount);
    
    $resultCount = db()->queryRow($sqlCount, $params);
    $total = $resultCount ? $resultCount['total'] : count($produtos);
    
    // Retornar resposta no formato esperado pelo JavaScript
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'sucesso' => true,
        'produtos' => $produtos,
        'total' => $total,
        'filtros_aplicados' => $filtros
    ], JSON_UNESCAPED_UNICODE);
    exit;
    
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao filtrar produtos: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
