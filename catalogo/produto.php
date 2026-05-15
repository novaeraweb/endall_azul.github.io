<?php
/**
 * Página de detalhes do produto - Endall Inspeções
 * Exibe informações detalhadas, galeria de imagens, especificações e CTA
 */

define('ENDALL_APP', true);

// Carrega arquivos de configuração
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Inicia sessão
session_name(SESSION_NAME);
session_start();

// Pega o SKU da URL
$sku = getParam('sku', '');

if (empty($sku)) {
    header('Location: index.php');
    exit;
}

// Busca o produto no banco de dados
try {
    $pdo = getDB();

    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            s.nome as serie_nome,
            s.slug as serie_slug,
            s.descricao as serie_descricao,
            s.cor as serie_cor
        FROM produtos p
        INNER JOIN series s ON p.serie_id = s.id
        WHERE p.sku = :sku AND p.ativo = 1
    ");
    $stmt->execute(['sku' => $sku]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        header('Location: index.php');
        exit;
    }
    
    // Decodifica campos JSON
    $produto['imagens'] = json_decode($produto['imagens'] ?? '[]', true) ?: [];
    $produto['recursos_especiais'] = json_decode($produto['recursos_especiais'] ?? '[]', true) ?: [];

    // ficha_tecnica agora pode ter estrutura nova: { specs: {}, itens_inclusos: [], downloads: [] }
    // ou estrutura antiga (dict simples). Normaliza para sempre ter as 3 chaves.
    $ficha_raw = json_decode($produto['ficha_tecnica'] ?? '{}', true) ?: [];
    $produto['especificacoes_tecnicas'] = $ficha_raw['specs'] ?? (
        // Se ficha_raw NÃO tem 'specs' nem 'itens_inclusos', assume formato antigo (dict de specs)
        (!isset($ficha_raw['itens_inclusos']) && !isset($ficha_raw['downloads']) && !empty($ficha_raw)) ? $ficha_raw : []
    );
    $produto['itens_inclusos'] = $ficha_raw['itens_inclusos'] ?? [];
    $produto['downloads'] = $ficha_raw['downloads'] ?? [];
    $produto['aplicacoes'] = $ficha_raw['aplicacoes'] ?? [];

    // Garante que chaves opcionais existam para evitar warnings
    $produto_defaults = [
        'pdf_ficha' => '',
        'video_url' => '',
        'destaque' => 0,
        'diametro_camera' => '',
        'comprimento_cabo' => '',
        'resolucao' => '',
        'linha_produto' => '',
        'direcao_visao' => '',
        'angulo_visao' => '',
        'preco_referencia' => null,
        'pdf_url' => '',
    ];
    $produto = array_merge($produto_defaults, $produto);
    
    // Incrementa contador de visualizações
    $stmt = $pdo->prepare("UPDATE produtos SET visualizacoes = visualizacoes + 1 WHERE id = :id");
    $stmt->execute(['id' => $produto['id']]);
    
    // Busca produtos relacionados (mesma série)
    $stmt = $pdo->prepare("
        SELECT p.*, s.nome as serie_nome, s.slug as serie_slug, s.cor as serie_cor
        FROM produtos p
        INNER JOIN series s ON p.serie_id = s.id
        WHERE p.serie_id = :serie_id AND p.id != :id AND p.ativo = 1
        ORDER BY p.destaque DESC, RAND()
        LIMIT 4
    ");
    $stmt->execute([
        'serie_id' => $produto['serie_id'],
        'id' => $produto['id']
    ]);
    $relacionados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar produto: " . $e->getMessage());
    header('Location: index.php');
    exit;
}

// Define meta tags
$pageTitle = $produto['nome'] . ' - ' . $produto['serie_nome'] . ' | ' . SITE_NAME;
$pageDescription = mb_substr(strip_tags($produto['descricao']), 0, 160);

include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<nav class="catalogo-breadcrumb" aria-label="breadcrumb">
    <ol>
        <li><a href="<?= EMPRESA_SITE ?>"><i class="fas fa-home"></i> Início</a></li>
        <li><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
        <li><a href="index.php">Vendas</a></li>
        <li><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
        <li><a href="index.php?serie=<?= urlencode($produto['serie_slug']) ?>"><?= htmlEsc($produto['serie_nome']) ?></a></li>
        <li><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
        <li class="breadcrumb-current"><?= htmlEsc($produto['nome']) ?></li>
    </ol>
</nav>

<!-- Conteúdo do Produto -->
<main class="produto-page">
    <div class="container">
        <div class="produto-container">
            
            <!-- Coluna Esquerda: Galeria de Imagens -->
            <div class="produto-galeria">
                <div class="galeria-principal">
                    <?php if (!empty($produto['imagens'])): ?>
                        <img src="<?= htmlEsc($produto['imagens'][0]) ?>" 
                             alt="<?= htmlEsc($produto['nome']) ?>"
                             class="img-principal"
                             id="imgPrincipal"
                             onerror="this.src='assets/images/produto-sem-foto.svg'">
                        <button class="zoom-btn" id="btnZoom" title="Ampliar imagem">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    <?php else: ?>
                        <img src="assets/images/produto-sem-foto.svg" 
                             alt="Sem imagem"
                             class="img-principal"
                             id="imgPrincipal">
                    <?php endif; ?>
                </div>
                
                <?php if (count($produto['imagens']) > 1): ?>
                <div class="galeria-miniaturas">
                    <?php foreach ($produto['imagens'] as $index => $imagem): ?>
                        <div class="miniatura <?= $index === 0 ? 'active' : '' ?>" 
                             data-index="<?= $index ?>">
                            <img src="<?= htmlEsc($imagem) ?>" 
                                 alt="Miniatura <?= $index + 1 ?>"
                                 onerror="this.src='assets/images/produto-sem-foto.svg'">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Coluna Direita: Informações do Produto -->
            <div class="produto-info">
                
                <!-- Badge da Série -->
                <div class="produto-serie-badge" style="background-color:#1565c0;">
                    <?= htmlEsc($produto['serie_nome']) ?>
                </div>
                
                <!-- Título e SKU -->
                <h1 class="produto-titulo"><?= htmlEsc($produto['nome']) ?></h1>
                <p class="produto-sku">SKU: <strong><?= htmlEsc($produto['sku']) ?></strong></p>
                
                <!-- Destaque (se houver) -->
                <?php if ($produto['destaque']): ?>
                    <div class="produto-destaque-badge">
                        <i class="fas fa-star"></i> Produto em Destaque
                    </div>
                <?php endif; ?>
                
                <!-- Descrição Curta -->
                <div class="produto-descricao">
                    <?= nl2br(htmlEsc($produto['descricao'])) ?>
                </div>
                
                <!-- Especificações Rápidas -->
                <div class="produto-specs-rapidas">
                    <h3>Especificações Principais</h3>
                    <div class="specs-grid">
                        <?php if ($produto['diametro_camera']): ?>
                        <div class="spec-item">
                            <i class="fas fa-circle"></i>
                            <div>
                                <span class="spec-label">Diâmetro da Câmera</span>
                                <span class="spec-value"><?= htmlEsc($produto['diametro_camera']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($produto['comprimento_cabo']): ?>
                        <div class="spec-item">
                            <i class="fas fa-ruler"></i>
                            <div>
                                <span class="spec-label">Comprimento do Cabo</span>
                                <span class="spec-value"><?= htmlEsc($produto['comprimento_cabo']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($produto['resolucao']): ?>
                        <div class="spec-item">
                            <i class="fas fa-image"></i>
                            <div>
                                <span class="spec-label">Resolução</span>
                                <span class="spec-value"><?= htmlEsc($produto['resolucao']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($produto['linha_produto']): ?>
                        <div class="spec-item">
                            <i class="fas fa-tag"></i>
                            <div>
                                <span class="spec-label">Linha</span>
                                <span class="spec-value"><?= htmlEsc($produto['linha_produto']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recursos Especiais -->
                <?php if (!empty($produto['recursos_especiais'])): ?>
                <div class="produto-recursos">
                    <h3>Recursos Especiais</h3>
                    <ul class="recursos-lista">
                        <?php foreach ($produto['recursos_especiais'] as $recurso): ?>
                            <li><i class="fas fa-check-circle"></i> <?= htmlEsc($recurso) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- CTAs -->
                <div class="produto-acoes">
                    <button class="btn btn-primary btn-lg btn-adicionar" 
                            data-produto-id="<?= $produto['id'] ?>"
                            data-produto-sku="<?= htmlEsc($produto['sku']) ?>"
                            data-produto-nome="<?= htmlEsc($produto['nome']) ?>"
                            data-produto-serie="<?= htmlEsc($produto['serie_nome']) ?>"
                            data-produto-serie-cor="<?= htmlEsc($produto['serie_cor'] ?? '#2F81DF') ?>"
                            data-produto-imagem="<?= htmlEsc($produto['imagens'][0] ?? '') ?>"
                            data-produto-diametro="<?= htmlEsc($produto['diametro_camera'] ?? '') ?>"
                            data-produto-cabo="<?= htmlEsc($produto['comprimento_cabo'] ?? '') ?>">
                        <i class="fas fa-shopping-cart"></i>
                        Adicionar ao Orçamento
                    </button>
                    
                    <?php if ($produto['pdf_ficha']): ?>
                    <a href="<?= htmlEsc($produto['pdf_ficha']) ?>" 
                       class="btn btn-outline btn-lg"
                       download
                       target="_blank">
                        <i class="fas fa-file-pdf"></i>
                        Download PDF
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?= WHATSAPP ?>?text=Olá! Gostaria de mais informações sobre o produto <?= urlencode($produto['nome']) ?> (SKU: <?= urlencode($produto['sku']) ?>)" 
                       class="btn btn-whatsapp btn-lg"
                       target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        Falar no WhatsApp
                    </a>
                </div>
                
            </div>
            
        </div>
        
        <!-- Tabs de Conteúdo -->
        <?php
            // Decide quais abas serão exibidas (primeira disponível = ativa)
            $abas = [];
            if (!empty($produto['descricao'])) {
                $abas[] = ['id' => 'detalhes', 'icon' => 'fa-info-circle', 'label' => 'Detalhes'];
            }
            if (!empty($produto['especificacoes_tecnicas'])) {
                $abas[] = ['id' => 'especificacoes', 'icon' => 'fa-clipboard-list', 'label' => 'Especificações'];
            }
            if (!empty($produto['itens_inclusos'])) {
                $abas[] = ['id' => 'itens', 'icon' => 'fa-box-open', 'label' => 'Itens Incluídos'];
            }
            $tem_downloads = !empty($produto['downloads']) || !empty($produto['video_url']) || !empty($produto['pdf_url']);
            if ($tem_downloads) {
                $abas[] = ['id' => 'downloads', 'icon' => 'fa-download', 'label' => 'Downloads'];
            }
            // Aplicações como aba opcional (mantém se houver dado antigo)
            if (!empty($produto['aplicacoes'])) {
                $abas[] = ['id' => 'aplicacoes', 'icon' => 'fa-tasks', 'label' => 'Aplicações'];
            }
        ?>
        
        <?php if (!empty($abas)): ?>
        <div class="produto-tabs">
            <div class="tabs-nav" role="tablist">
                <?php foreach ($abas as $idx => $aba): ?>
                    <button class="tab-btn <?= $idx === 0 ? 'active' : '' ?>"
                            data-tab="<?= htmlEsc($aba['id']) ?>"
                            role="tab"
                            aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>">
                        <i class="fas <?= htmlEsc($aba['icon']) ?>"></i>
                        <span><?= htmlEsc($aba['label']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div class="tabs-content">
                
                <!-- Tab: Detalhes -->
                <?php if (!empty($produto['descricao'])): ?>
                <div class="tab-pane <?= ($abas[0]['id'] === 'detalhes') ? 'active' : '' ?>" id="tab-detalhes" role="tabpanel">
                    <div class="detalhes-conteudo">
                        <?= nl2br(htmlEsc($produto['descricao'])) ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Tab: Especificações -->
                <?php if (!empty($produto['especificacoes_tecnicas'])): ?>
                <div class="tab-pane <?= ($abas[0]['id'] === 'especificacoes') ? 'active' : '' ?>" id="tab-especificacoes" role="tabpanel">
                    <table class="specs-table">
                        <tbody>
                            <?php foreach ($produto['especificacoes_tecnicas'] as $chave => $valor): ?>
                            <tr>
                                <th><?= htmlEsc($chave) ?></th>
                                <td><?= htmlEsc(is_array($valor) ? implode(', ', $valor) : $valor) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <!-- Tab: Itens Incluídos -->
                <?php if (!empty($produto['itens_inclusos'])): ?>
                <div class="tab-pane <?= ($abas[0]['id'] === 'itens') ? 'active' : '' ?>" id="tab-itens" role="tabpanel">
                    <p class="itens-intro">
                        <i class="fas fa-info-circle"></i>
                        Os seguintes itens acompanham este produto:
                    </p>
                    <ul class="itens-inclusos-lista">
                        <?php foreach ($produto['itens_inclusos'] as $item): ?>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span><?= htmlEsc($item) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Tab: Downloads (PDFs + Vídeo) -->
                <?php if ($tem_downloads): ?>
                <div class="tab-pane <?= ($abas[0]['id'] === 'downloads') ? 'active' : '' ?>" id="tab-downloads" role="tabpanel">
                    
                    <?php if (!empty($produto['video_url'])): ?>
                    <div class="produto-video">
                        <h3><i class="fas fa-play-circle"></i> Vídeo Demonstrativo</h3>
                        <div class="video-container">
                            <?php
                            if (strpos($produto['video_url'], 'youtube.com') !== false || strpos($produto['video_url'], 'youtu.be') !== false) {
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $produto['video_url'], $matches);
                                $videoId = $matches[1] ?? '';
                                if ($videoId) {
                                    echo '<iframe src="https://www.youtube.com/embed/' . htmlEsc($videoId) . '" frameborder="0" allowfullscreen loading="lazy"></iframe>';
                                }
                            } else {
                                echo '<video controls playsinline webkit-playsinline>';
                                echo '<source src="' . htmlEsc($produto['video_url']) . '" type="video/mp4">';
                                echo 'Seu navegador não suporta vídeos HTML5.';
                                echo '</video>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($produto['downloads']) || !empty($produto['pdf_url'])): ?>
                    <div class="produto-downloads">
                        <h3><i class="fas fa-file-pdf"></i> Documentação e Manuais</h3>
                        <ul class="downloads-lista">
                            <?php
                            // Suporta dois formatos:
                            // 1) Array de strings (URLs diretas) - novo formato Intertest
                            // 2) Array de objetos {url, titulo, tamanho} - formato antigo
                            $downloads_normalizados = [];
                            foreach (($produto['downloads'] ?? []) as $dl) {
                                if (is_string($dl)) {
                                    // String = URL direta
                                    $titulo = basename(parse_url($dl, PHP_URL_PATH) ?? 'documento.pdf');
                                    $titulo = urldecode($titulo);
                                    $titulo = preg_replace('/\.pdf$/i', '', $titulo);
                                    $titulo = str_replace(['_', '-'], ' ', $titulo);
                                    $titulo = ucwords(trim($titulo));
                                    $downloads_normalizados[] = ['url' => $dl, 'titulo' => $titulo ?: 'Documento PDF'];
                                } elseif (is_array($dl) && isset($dl['url'])) {
                                    $downloads_normalizados[] = $dl;
                                }
                            }
                            // Adiciona pdf_url principal se existir
                            if (!empty($produto['pdf_url'])) {
                                $downloads_normalizados[] = ['url' => $produto['pdf_url'], 'titulo' => 'Ficha Técnica do Produto'];
                            }
                            ?>
                            <?php foreach ($downloads_normalizados as $download): ?>
                                <li>
                                    <a href="<?= htmlEsc($download['url']) ?>" target="_blank" rel="noopener" download>
                                        <i class="fas fa-file-download"></i>
                                        <span class="download-titulo"><?= htmlEsc($download['titulo']) ?></span>
                                        <?php if (isset($download['tamanho'])): ?>
                                            <span class="download-size">(<?= htmlEsc($download['tamanho']) ?>)</span>
                                        <?php endif; ?>
                                        <i class="fas fa-external-link-alt download-icon"></i>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                </div>
                <?php endif; ?>
                
                <!-- Tab: Aplicações -->
                <?php if (!empty($produto['aplicacoes'])): ?>
                <div class="tab-pane <?= ($abas[0]['id'] === 'aplicacoes') ? 'active' : '' ?>" id="tab-aplicacoes" role="tabpanel">
                    <ul class="aplicacoes-lista">
                        <?php foreach ($produto['aplicacoes'] as $aplicacao): ?>
                            <li><i class="fas fa-chevron-right"></i> <?= htmlEsc($aplicacao) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Produtos Relacionados -->
        <?php if (!empty($relacionados)): ?>
        <section class="produtos-relacionados">
            <h2>Produtos Relacionados</h2>
            <p class="section-subtitle">Outros produtos da série <?= htmlEsc($produto['serie_nome']) ?></p>
            
            <div class="produtos-grid grid-4">
                <?php foreach ($relacionados as $rel): ?>
                    <?php
                    $relImagens = json_decode($rel['imagens'], true) ?: [];
                    $relImagem = $relImagens[0] ?? URL_ASSETS . '/images/produto-sem-foto.jpg';
                    ?>
                    <article class="produto-card">
                        <a href="produto.php?sku=<?= urlencode($rel['sku']) ?>" class="produto-link">
                            <div class="produto-imagem">
                                <img src="<?= htmlEsc($relImagem) ?>" 
                                     alt="<?= htmlEsc($rel['nome']) ?>"
                                     loading="lazy">
                                <div class="produto-overlay">
                                    <span class="ver-detalhes">Ver Detalhes</span>
                                </div>
                            </div>
                            <div class="produto-badge" style="background-color: <?= htmlEsc($rel['serie_cor'] ?? '#0D1B2A') ?>;">
                                <?= htmlEsc($rel['serie_nome']) ?>
                            </div>
                            <div class="produto-info-card">
                                <h3 class="produto-nome"><?= htmlEsc($rel['nome']) ?></h3>
                                <p class="produto-sku">SKU: <?= htmlEsc($rel['sku']) ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
    </div>
</main>

<!-- Modal de Zoom -->
<div class="modal" id="modalZoom">
    <div class="modal-overlay" id="modalZoomOverlay"></div>
    <div class="modal-zoom-content">
        <button class="modal-close" id="btnFecharZoom">
            <i class="fas fa-times"></i>
        </button>
        <img src="" alt="Zoom" id="imgZoom">
    </div>
</div>

<style>
/* Estilos específicos da página de produto */
.produto-page {
    padding: 2rem 0 4rem;
}

.produto-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
}

/* Galeria */
.produto-galeria {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.galeria-principal {
    position: relative;
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.img-principal {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}

.zoom-btn {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.zoom-btn:hover {
    background: #2F81DF;
    color: white;
    transform: scale(1.1);
}

.galeria-miniaturas {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 0.5rem;
}

.miniatura {
    border: 2px solid transparent;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
}

.miniatura:hover,
.miniatura.active {
    border-color: #2F81DF;
}

.miniatura img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    display: block;
}

/* Informações do Produto */
.produto-serie-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.produto-titulo {
    font-size: 1.85rem;
    color: var(--cor-primaria);
    margin-bottom: 0.5rem;
    line-height: 1.25;
}

.produto-sku {
    color: #6B7280;
    margin-bottom: 1.5rem;
}

.produto-destaque-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #2F81DF, #1565C0);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.produto-descricao {
    font-size: 1.125rem;
    line-height: 1.7;
    color: #374151;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #E5E7EB;
}

.produto-specs-rapidas h3 {
    font-size: 1.25rem;
    color: #0D1B2A;
    margin-bottom: 1rem;
}

.specs-grid {
    display: grid;
    gap: 1rem;
    margin-bottom: 2rem;
}

.spec-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #F4F6F9;
    border-radius: 6px;
}

.spec-item i {
    color: #2F81DF;
    font-size: 1.5rem;
}

.spec-item > div {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.spec-label {
    color: #6B7280;
    font-size: 0.875rem;
}

.spec-value {
    color: #0D1B2A;
    font-weight: 600;
}

.produto-recursos h3 {
    font-size: 1.25rem;
    color: #0D1B2A;
    margin-bottom: 1rem;
}

.recursos-lista {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
}

.recursos-lista li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    color: #374151;
}

.recursos-lista i {
    color: #10B981;
    font-size: 1.125rem;
}

.produto-acoes {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-whatsapp {
    background: #25D366;
    color: white;
    border: none;
}

.btn-whatsapp:hover {
    background: #20BA5A;
}

/* Tabs */
.produto-tabs {
    margin-top: 4rem;
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    overflow: hidden;
}

.tabs-nav {
    display: flex;
    background: linear-gradient(135deg, var(--cor-primaria), var(--cor-secundaria));
    border-bottom: 1px solid var(--cor-secundaria);
}

.tab-btn {
    flex: 1;
    padding: 1.25rem;
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.82);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.tab-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
}

.tab-btn.active {
    background: #fff;
    color: var(--cor-secundaria);
    border-bottom: 3px solid var(--cor-secundaria);
    box-shadow: inset 0 3px 0 var(--cor-secundaria);
}

.tabs-content {
    padding: 2rem;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
    animation: fadeIn 0.3s;
}

.specs-table {
    width: 100%;
    border-collapse: collapse;
}

.specs-table tr {
    border-bottom: 1px solid #E5E7EB;
}

.specs-table th,
.specs-table td {
    padding: 1rem;
    text-align: left;
}

.specs-table th {
    background: #F4F6F9;
    color: #0D1B2A;
    font-weight: 600;
    width: 30%;
}

.specs-table td {
    color: #374151;
}

.aplicacoes-lista {
    list-style: none;
    padding: 0;
    display: grid;
    gap: 0.75rem;
}

.aplicacoes-lista li {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #F4F6F9;
    border-radius: 6px;
    color: #374151;
}

.aplicacoes-lista i {
    color: #2F81DF;
}

.produto-video {
    margin-bottom: 2rem;
}

.produto-video h3 {
    font-size: 1.25rem;
    color: #0D1B2A;
    margin-bottom: 1rem;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
    overflow: hidden;
    border-radius: 8px;
}

.video-container iframe,
.video-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 8px;
}

.produto-downloads h3 {
    font-size: 1.25rem;
    color: #0D1B2A;
    margin-bottom: 1rem;
}

.downloads-lista {
    list-style: none;
    padding: 0;
    display: grid;
    gap: 0.75rem;
}

.downloads-lista li a {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #F4F6F9;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    transition: all 0.3s;
}

.downloads-lista li a:hover {
    background: #E5E7EB;
    color: #2F81DF;
}

.downloads-lista i {
    color: #2F81DF;
    font-size: 1.5rem;
}

.download-size {
    margin-left: auto;
    color: #9CA3AF;
    font-size: 0.875rem;
}

/* Produtos Relacionados */
.produtos-relacionados {
    margin-top: 4rem;
    padding-top: 4rem;
    border-top: 1px solid #E5E7EB;
}

.produtos-relacionados h2 {
    text-align: center;
    font-size: 2rem;
    color: #0D1B2A;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    text-align: center;
    color: #6B7280;
    margin-bottom: 2rem;
}

.grid-4 {
    grid-template-columns: repeat(4, 1fr);
}

/* Modal de Zoom */
#modalZoom.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

#modalZoom .modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(13, 27, 42, 0.86);
}

.modal-zoom-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    z-index: 1001;
}

.modal-zoom-content img {
    max-width: 100%;
    max-height: 90vh;
    display: block;
    border-radius: 8px;
    background: #fff;
}

#modalZoom .modal-close {
    position: absolute;
    top: -14px;
    right: -14px;
    width: 42px;
    height: 42px;
    border: 0;
    border-radius: 50%;
    background: var(--cor-secundaria);
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 18px rgba(0,0,0,0.22);
    z-index: 1002;
}

#modalZoom .modal-close:hover {
    background: var(--cor-destaque);
}

/* Responsividade */
@media (max-width: 1024px) {
    .produto-container {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .produto-galeria {
        position: static;
    }
    
    .grid-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .produto-titulo {
        font-size: 1.5rem;
    }
    
    .tabs-nav {
        flex-direction: column;
    }
    
    .tab-btn {
        justify-content: flex-start;
    }
    
    .specs-table th,
    .specs-table td {
        padding: 0.75rem;
        font-size: 0.875rem;
    }
    
    .produto-acoes .btn {
        width: 100%;
    }
    
    .grid-4 {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Galeria de imagens
    const miniaturas = document.querySelectorAll('.miniatura');
    const imgPrincipal = document.getElementById('imgPrincipal');
    
    miniaturas.forEach(miniatura => {
        miniatura.addEventListener('click', function() {
            const index = this.dataset.index;
            const imagens = <?= json_encode($produto['imagens']) ?>;
            
            // Atualiza imagem principal
            imgPrincipal.src = imagens[index];
            
            // Atualiza miniaturas ativas
            miniaturas.forEach(m => m.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Zoom da imagem
    const btnZoom = document.getElementById('btnZoom');
    const modalZoom = document.getElementById('modalZoom');
    const imgZoom = document.getElementById('imgZoom');
    const btnFecharZoom = document.getElementById('btnFecharZoom');
    const modalZoomOverlay = document.getElementById('modalZoomOverlay');
    
    if (btnZoom) {
        btnZoom.addEventListener('click', function() {
            imgZoom.src = imgPrincipal.src;
            modalZoom.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    function fecharZoom() {
        modalZoom.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (btnFecharZoom) {
        btnFecharZoom.addEventListener('click', fecharZoom);
    }
    
    if (modalZoomOverlay) {
        modalZoomOverlay.addEventListener('click', fecharZoom);
    }
    
    // Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Remove active de todos
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Ativa o clicado
            this.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        });
    });
    
    // Botão adicionar ao carrinho
    const btnAdicionar = document.querySelector('.btn-adicionar');
    if (btnAdicionar) {
        btnAdicionar.addEventListener('click', function() {
            const produto = {
                id: parseInt(this.dataset.produtoId, 10),
                sku: this.dataset.produtoSku,
                nome: this.dataset.produtoNome,
                serie_nome: this.dataset.produtoSerie,
                serie_cor: this.dataset.produtoSerieCor || '#2F81DF',
                imagem: this.dataset.produtoImagem,
                diametro_camera: this.dataset.produtoDiametro || '',
                comprimento_cabo: this.dataset.produtoCabo || '',
                quantidade: 1,
                observacoes: ''
            };
            
            // Usa o módulo Carrinho carregado no footer. Mantém fallback para instalações antigas.
            if (typeof Carrinho !== 'undefined' && typeof Carrinho.adicionar === 'function') {
                Carrinho.adicionar(produto);
            } else if (typeof adicionarAoCarrinho === 'function') {
                adicionarAoCarrinho(produto);
            } else if (typeof mostrarToast === 'function') {
                mostrarToast('Não foi possível carregar o carrinho. Atualize a página e tente novamente.', 'error');
            }
        });
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
