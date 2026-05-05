<?php
/**
 * ENDALL INSPEÇÕES - Página Principal do Catálogo
 * Lista produtos com filtros avançados
 */

// Definir constante do sistema
define('SISTEMA_ENDALL', true);

// Carregar configurações
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Configurações da página
$page_title = 'Catálogo de Boroscópios';
$page_description = 'Explore nosso catálogo completo com 111 boroscópios Yateks. Filtros avançados por diâmetro, comprimento, recursos especiais e muito mais.';
$additional_js = ['assets/js/filtros.js'];

// Buscar todas as séries para os filtros
$series = listarSeries();

// Buscar estatísticas para os filtros
$sql_stats = "SELECT 
    MIN(diametro_camera) as diametro_min,
    MAX(diametro_camera) as diametro_max,
    MIN(comprimento_cabo) as cabo_min,
    MAX(comprimento_cabo) as cabo_max
FROM produtos WHERE ativo = 1";
$stats = db()->queryRow($sql_stats);

// Buscar produtos (inicial - sem filtros)
$sql = "SELECT p.*, s.nome as serie_nome, s.slug as serie_slug, s.cor as serie_cor
        FROM produtos p
        INNER JOIN series s ON p.serie_id = s.id
        WHERE p.ativo = 1
        ORDER BY p.destaque DESC, p.criado_em DESC
        LIMIT " . PRODUTOS_POR_PAGINA;
$produtos = db()->query($sql);

// Incluir header
include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<nav class="catalogo-breadcrumb" aria-label="breadcrumb">
    <ol>
        <li><a href="<?= EMPRESA_SITE ?>">Início</a></li>
        <li><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
        <li><a href="<?= urlBase() ?>">Vendas</a></li>
        <li><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
        <li class="breadcrumb-current">Catálogo</li>
    </ol>
</nav>

<!-- Layout com Sidebar -->
<div class="layout-with-sidebar">
    
    <!-- SIDEBAR DE FILTROS -->
    <aside class="sidebar">
        <h2 class="sidebar-title">
            <i class="fas fa-filter"></i> Filtros
        </h2>
        
        <!-- Busca Rápida -->
        <div class="filter-group">
            <label class="filter-label" for="busca">
                <i class="fas fa-search"></i> Busca Rápida
            </label>
            <input type="text" 
                   id="busca" 
                   class="search-input" 
                   placeholder="Digite SKU ou nome do produto...">
        </div>
        
        <!-- Filtro por Série -->
        <div class="filter-group">
            <label class="filter-label">
                <i class="fas fa-layer-group"></i> Série
            </label>
            <?php foreach ($series as $serie): ?>
                <?php $count = contarProdutosPorSerie($serie['id']); ?>
                <div class="checkbox-item">
                    <input type="checkbox" 
                           id="serie_<?= $serie['id'] ?>" 
                           name="serie[]" 
                           value="<?= $serie['id'] ?>"
                           data-slug="<?= $serie['slug'] ?>">
                    <label for="serie_<?= $serie['id'] ?>">
                        <span><?= htmlspecialchars($serie['nome']) ?></span>
                        <span class="checkbox-count"><?= $count ?></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Filtro Diâmetro da Câmera -->
        <div class="filter-group">
            <label class="filter-label">
                <i class="fas fa-circle"></i> Diâmetro da Câmera
            </label>
            <div class="range-slider">
                <div class="range-values">
                    <span id="diametroMin"><?= formatarNumero($stats['diametro_min']) ?>mm</span>
                    <span id="diametroMax"><?= formatarNumero($stats['diametro_max']) ?>mm</span>
                </div>
                <input type="range" 
                       id="diametroRange" 
                       min="<?= $stats['diametro_min'] ?>" 
                       max="<?= $stats['diametro_max'] ?>" 
                       value="<?= $stats['diametro_max'] ?>"
                       step="0.1">
            </div>
        </div>
        
        <!-- Filtro Comprimento do Cabo -->
        <div class="filter-group">
            <label class="filter-label">
                <i class="fas fa-ruler"></i> Comprimento do Cabo
            </label>
            <div class="range-slider">
                <div class="range-values">
                    <span id="caboMin"><?= formatarNumero($stats['cabo_min']) ?>m</span>
                    <span id="caboMax"><?= formatarNumero($stats['cabo_max']) ?>m</span>
                </div>
                <input type="range" 
                       id="caboRange" 
                       min="<?= $stats['cabo_min'] ?>" 
                       max="<?= $stats['cabo_max'] ?>" 
                       value="<?= $stats['cabo_max'] ?>"
                       step="0.5">
            </div>
        </div>
        
        <!-- Filtro Recursos Especiais -->
        <div class="filter-group">
            <label class="filter-label">
                <i class="fas fa-star"></i> Recursos Especiais
            </label>
            <div class="checkbox-item">
                <input type="checkbox" id="recurso_hd" name="recursos[]" value="HD">
                <label for="recurso_hd">
                    <span>Alta Definição (HD)</span>
                </label>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="recurso_wifi" name="recursos[]" value="Wi-Fi">
                <label for="recurso_wifi">
                    <span>Conectividade Wi-Fi</span>
                </label>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="recurso_uv" name="recursos[]" value="UV">
                <label for="recurso_uv">
                    <span>Iluminação UV</span>
                </label>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="recurso_3d" name="recursos[]" value="3D">
                <label for="recurso_3d">
                    <span>Visão 3D Estereoscópica</span>
                </label>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="recurso_4vias" name="recursos[]" value="4-vias">
                <label for="recurso_4vias">
                    <span>Articulação 4 Vias</span>
                </label>
            </div>
        </div>
        
        <!-- Filtro Direção de Visão -->
        <div class="filter-group">
            <label class="filter-label">
                <i class="fas fa-eye"></i> Direção de Visão
            </label>
            <div class="radio-item">
                <input type="radio" id="direcao_todas" name="direcao" value="" checked>
                <label for="direcao_todas">
                    <span>Todas as Direções</span>
                </label>
            </div>
            <div class="radio-item">
                <input type="radio" id="direcao_direta" name="direcao" value="Direta">
                <label for="direcao_direta">
                    <span>Direta (0°)</span>
                </label>
            </div>
            <div class="radio-item">
                <input type="radio" id="direcao_90" name="direcao" value="90">
                <label for="direcao_90">
                    <span>Lateral (90°)</span>
                </label>
            </div>
            <div class="radio-item">
                <input type="radio" id="direcao_45" name="direcao" value="45">
                <label for="direcao_45">
                    <span>Oblíqua (45°)</span>
                </label>
            </div>
        </div>
        
        <!-- Botão Limpar Filtros -->
        <button type="button" class="btn-clear-filters" onclick="limparFiltros()">
            <i class="fas fa-redo"></i> Limpar Filtros
        </button>
        
        <!-- Contador de Resultados -->
        <div class="results-count" id="resultsCount">
            <i class="fas fa-box"></i> Mostrando <strong id="countAtual">0</strong> de <strong id="countTotal">0</strong> produtos
        </div>
    </aside>
    
    <!-- GRID DE PRODUTOS -->
    <div class="produtos-container">
        
        <!-- Header do Catálogo -->
        <div class="produtos-header">
            <h1 class="produtos-title">
                <i class="fas fa-th-large"></i> Catálogo de Produtos
            </h1>
            <select class="sort-select" id="sortSelect">
                <option value="relevancia">Mais Relevantes</option>
                <option value="nome_asc">Nome (A-Z)</option>
                <option value="nome_desc">Nome (Z-A)</option>
                <option value="diametro_asc">Menor Diâmetro</option>
                <option value="diametro_desc">Maior Diâmetro</option>
                <option value="cabo_asc">Menor Cabo</option>
                <option value="cabo_desc">Maior Cabo</option>
            </select>
        </div>
        
        <!-- Grid de Produtos -->
        <div class="produtos-grid" id="produtosGrid">
            <?php foreach ($produtos as $produto): 
                $imagens = decodificarImagens($produto['imagens']);
                $recursos = decodificarRecursos($produto['recursos_especiais']);
                $primeira_imagem = !empty($imagens) ? $imagens[0] : 'assets/images/produto-sem-foto.svg';
                // Se a imagem estiver vazia ou for inválida, usar placeholder local
                if (empty($primeira_imagem)) {
                    $primeira_imagem = 'assets/images/produto-sem-foto.svg';
                }
            ?>
                <div class="produto-card" data-id="<?= $produto['id'] ?>">
                    <!-- Imagem -->
                    <div class="produto-image-container">
                        <img src="<?= $primeira_imagem ?>" 
                             alt="<?= htmlspecialchars($produto['nome']) ?>" 
                             class="produto-image"
                             onerror="this.src='assets/images/produto-sem-foto.svg'">
                        
                        <!-- Badge da Série -->
                        <span class="serie-badge" style="background-color: <?= $produto['serie_cor'] ?>">
                            <?= htmlspecialchars($produto['serie_nome']) ?>
                        </span>
                        
                        <!-- Badge Destaque -->
                        <?php if ($produto['destaque']): ?>
                            <span class="destaque-badge">
                                <i class="fas fa-star"></i> DESTAQUE
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Informações -->
                    <div class="produto-info">
                        <div class="produto-sku"><?= htmlspecialchars($produto['sku']) ?></div>
                        <h3 class="produto-nome"><?= htmlspecialchars($produto['nome']) ?></h3>
                        
                        <!-- Specs Principais -->
                        <div class="produto-specs">
                            <span class="spec-tag">
                                <i class="fas fa-circle"></i> Ø <?= formatarNumero($produto['diametro_camera']) ?>mm
                            </span>
                            <span class="spec-tag">
                                <i class="fas fa-ruler"></i> <?= formatarNumero($produto['comprimento_cabo']) ?>m
                            </span>
                            <?php if ($produto['resolucao']): ?>
                                <span class="spec-tag">
                                    <i class="fas fa-tv"></i> <?= htmlspecialchars($produto['resolucao']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Recursos Especiais -->
                        <?php if (!empty($recursos)): ?>
                            <div class="recursos-badges">
                                <?php foreach (array_slice($recursos, 0, 3) as $recurso): ?>
                                    <span class="recurso-badge"><?= htmlspecialchars($recurso) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Ações -->
                        <div class="produto-actions">
                            <a href="produto.php?sku=<?= urlencode($produto['sku']) ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-info-circle"></i> Detalhes
                            </a>
                            <button class="btn btn-primary btn-sm btn-add-carrinho" 
                                    data-produto='<?= json_encode([
                                        'id' => $produto['id'],
                                        'sku' => $produto['sku'],
                                        'nome' => $produto['nome'],
                                        'serie_nome' => $produto['serie_nome'],
                                        'serie_cor' => $produto['serie_cor'],
                                        'imagem' => $primeira_imagem,
                                        'diametro_camera' => $produto['diametro_camera'],
                                        'comprimento_cabo' => $produto['comprimento_cabo']
                                    ]) ?>'>
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Loading Spinner (oculto inicialmente) -->
        <div id="loadingSpinner" style="display: none; text-align: center; padding: 3rem;">
            <div class="spinner"></div>
            <p style="margin-top: 1rem; color: var(--cor-cinza);">Carregando produtos...</p>
        </div>
        
        <!-- Mensagem sem resultados -->
        <div id="semResultados" style="display: none; text-align: center; padding: 3rem;">
            <i class="fas fa-search" style="font-size: 4rem; color: var(--cor-cinza); margin-bottom: 1rem;"></i>
            <h3 style="color: var(--cor-cinza-escuro);">Nenhum produto encontrado</h3>
            <p style="color: var(--cor-cinza); margin-bottom: 1.5rem;">Tente ajustar os filtros ou limpar para ver todos os produtos</p>
            <button class="btn btn-primary" onclick="limparFiltros()">
                <i class="fas fa-redo"></i> Limpar Filtros
            </button>
        </div>
        
    </div>
    
</div>

<script>
// Event listener para botões de adicionar ao carrinho
document.addEventListener('DOMContentLoaded', function() {
    const botoesAdd = document.querySelectorAll('.btn-add-carrinho');
    
    botoesAdd.forEach(btn => {
        btn.addEventListener('click', function() {
            const produto = JSON.parse(this.getAttribute('data-produto'));
            
            if (Carrinho.temProduto(produto.id)) {
                mostrarToast('Este produto já está no orçamento', 'warning');
            } else {
                Carrinho.adicionar(produto);
            }
        });
    });
    
    // Atualizar contador inicial
    atualizarContadores();
});

function atualizarContadores() {
    const total = <?= db()->count('produtos', 'ativo = 1') ?>;
    const atual = document.querySelectorAll('.produto-card').length;
    
    document.getElementById('countTotal').textContent = total;
    document.getElementById('countAtual').textContent = atual;
}
</script>

<?php
// Incluir footer
include __DIR__ . '/includes/footer.php';
?>
