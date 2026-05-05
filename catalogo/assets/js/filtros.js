/**
 * ENDALL INSPEÇÕES - Sistema de Filtros
 * Filtros dinâmicos em tempo real com AJAX
 * 
 * @version 1.0.0
 */

(function() {
    'use strict';
    
    // Estado dos filtros
    const filtrosAtivos = {
        busca: '',
        series: [],
        diametro: null,
        cabo: null,
        recursos: [],
        direcao: '',
        ordenacao: 'relevancia'
    };
    
    let timeoutBusca = null;
    
    /**
     * Inicializar event listeners
     */
    function inicializar() {
        // Busca
        const inputBusca = document.getElementById('busca');
        if (inputBusca) {
            inputBusca.addEventListener('input', debounce(function() {
                filtrosAtivos.busca = this.value.trim();
                aplicarFiltros();
            }, 500));
        }
        
        // Checkboxes de séries
        document.querySelectorAll('input[name="serie[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                atualizarFiltroSeries();
                aplicarFiltros();
            });
        });
        
        // Range de diâmetro
        const diametroRange = document.getElementById('diametroRange');
        if (diametroRange) {
            diametroRange.addEventListener('input', function() {
                document.getElementById('diametroMax').textContent = 
                    formatarNumero(parseFloat(this.value)) + 'mm';
                filtrosAtivos.diametro = parseFloat(this.value);
            });
            
            diametroRange.addEventListener('change', function() {
                aplicarFiltros();
            });
        }
        
        // Range de cabo
        const caboRange = document.getElementById('caboRange');
        if (caboRange) {
            caboRange.addEventListener('input', function() {
                document.getElementById('caboMax').textContent = 
                    formatarNumero(parseFloat(this.value)) + 'm';
                filtrosAtivos.cabo = parseFloat(this.value);
            });
            
            caboRange.addEventListener('change', function() {
                aplicarFiltros();
            });
        }
        
        // Checkboxes de recursos
        document.querySelectorAll('input[name="recursos[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                atualizarFiltroRecursos();
                aplicarFiltros();
            });
        });
        
        // Radio buttons de direção
        document.querySelectorAll('input[name="direcao"]').forEach(radio => {
            radio.addEventListener('change', function() {
                filtrosAtivos.direcao = this.value;
                aplicarFiltros();
            });
        });
        
        // Select de ordenação
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                filtrosAtivos.ordenacao = this.value;
                aplicarFiltros();
            });
        }
    }
    
    /**
     * Atualizar filtro de séries
     */
    function atualizarFiltroSeries() {
        filtrosAtivos.series = [];
        document.querySelectorAll('input[name="serie[]"]:checked').forEach(checkbox => {
            filtrosAtivos.series.push(parseInt(checkbox.value));
        });
    }
    
    /**
     * Atualizar filtro de recursos
     */
    function atualizarFiltroRecursos() {
        filtrosAtivos.recursos = [];
        document.querySelectorAll('input[name="recursos[]"]:checked').forEach(checkbox => {
            filtrosAtivos.recursos.push(checkbox.value);
        });
    }
    
    /**
     * Aplicar filtros via AJAX
     */
    function aplicarFiltros() {
        const grid = document.getElementById('produtosGrid');
        const loading = document.getElementById('loadingSpinner');
        const semResultados = document.getElementById('semResultados');
        
        // Mostrar loading
        grid.style.opacity = '0.5';
        loading.style.display = 'block';
        semResultados.style.display = 'none';
        
        // Fazer requisição AJAX
        fetch('ajax/filtrar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(filtrosAtivos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                renderizarProdutos(data.produtos);
                atualizarContador(data.total);
            } else {
                mostrarToast('Erro ao filtrar produtos', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarToast('Erro na comunicação com o servidor', 'error');
        })
        .finally(() => {
            grid.style.opacity = '1';
            loading.style.display = 'none';
        });
    }
    
    /**
     * Renderizar produtos no grid
     */
    function renderizarProdutos(produtos) {
        const grid = document.getElementById('produtosGrid');
        const semResultados = document.getElementById('semResultados');
        
        if (produtos.length === 0) {
            grid.innerHTML = '';
            semResultados.style.display = 'block';
            return;
        }
        
        semResultados.style.display = 'none';
        
        let html = '';
        produtos.forEach(produto => {
            const imagens = produto.imagens ? JSON.parse(produto.imagens) : [];
            const recursos = produto.recursos_especiais ? JSON.parse(produto.recursos_especiais) : [];
            const primeiraImagem = imagens.length > 0 ? imagens[0] : '';
            
            html += `
                <div class="produto-card" data-id="${produto.id}">
                    <div class="produto-image-container">
                        <img src="${primeiraImagem || 'assets/images/produto-sem-foto.svg'}" 
                             alt="${produto.nome}" 
                             class="produto-image"
                             onerror="this.src='assets/images/produto-sem-foto.svg'">
                        
                        <span class="serie-badge" style="background-color: ${produto.serie_cor}">
                            ${produto.serie_nome}
                        </span>
                        
                        ${produto.destaque ? '<span class="destaque-badge"><i class="fas fa-star"></i> DESTAQUE</span>' : ''}
                    </div>
                    
                    <div class="produto-info">
                        <div class="produto-sku">${produto.sku}</div>
                        <h3 class="produto-nome">${produto.nome}</h3>
                        
                        <div class="produto-specs">
                            <span class="spec-tag">
                                <i class="fas fa-circle"></i> Ø ${formatarNumero(produto.diametro_camera)}mm
                            </span>
                            <span class="spec-tag">
                                <i class="fas fa-ruler"></i> ${formatarNumero(produto.comprimento_cabo)}m
                            </span>
                            ${produto.resolucao ? `
                                <span class="spec-tag">
                                    <i class="fas fa-tv"></i> ${produto.resolucao}
                                </span>
                            ` : ''}
                        </div>
                        
                        ${recursos.length > 0 ? `
                            <div class="recursos-badges">
                                ${recursos.slice(0, 3).map(r => `<span class="recurso-badge">${r}</span>`).join('')}
                            </div>
                        ` : ''}
                        
                        <div class="produto-actions">
                            <a href="produto.php?sku=${encodeURIComponent(produto.sku)}" class="btn btn-outline btn-sm">
                                <i class="fas fa-info-circle"></i> Detalhes
                            </a>
                            <button class="btn btn-primary btn-sm btn-add-carrinho" 
                                    data-produto='${JSON.stringify({
                                        id: produto.id,
                                        sku: produto.sku,
                                        nome: produto.nome,
                                        serie_nome: produto.serie_nome,
                                        serie_cor: produto.serie_cor,
                                        imagem: primeiraImagem,
                                        diametro_camera: produto.diametro_camera,
                                        comprimento_cabo: produto.comprimento_cabo
                                    })}'>
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        grid.innerHTML = html;
        
        // Reativar event listeners dos botões
        document.querySelectorAll('.btn-add-carrinho').forEach(btn => {
            btn.addEventListener('click', function() {
                const produto = JSON.parse(this.getAttribute('data-produto'));
                
                if (Carrinho.temProduto(produto.id)) {
                    mostrarToast('Este produto já está no orçamento', 'warning');
                } else {
                    Carrinho.adicionar(produto);
                }
            });
        });
        
        // Animar cards
        const cards = grid.querySelectorAll('.produto-card');
        cards.forEach((card, index) => {
            card.style.animation = `fadeInUp 0.5s ease-out ${index * 0.05}s both`;
        });
    }
    
    /**
     * Atualizar contador de resultados
     */
    function atualizarContador(total) {
        const countAtual = document.querySelectorAll('.produto-card').length;
        document.getElementById('countAtual').textContent = countAtual;
        document.getElementById('countTotal').textContent = total;
    }
    
    /**
     * Limpar todos os filtros
     */
    window.limparFiltros = function() {
        // Resetar estado
        filtrosAtivos.busca = '';
        filtrosAtivos.series = [];
        filtrosAtivos.diametro = null;
        filtrosAtivos.cabo = null;
        filtrosAtivos.recursos = [];
        filtrosAtivos.direcao = '';
        filtrosAtivos.ordenacao = 'relevancia';
        
        // Resetar inputs
        document.getElementById('busca').value = '';
        
        document.querySelectorAll('input[name="serie[]"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('input[name="recursos[]"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('input[name="direcao"]')[0].checked = true;
        
        const diametroRange = document.getElementById('diametroRange');
        if (diametroRange) {
            diametroRange.value = diametroRange.max;
            document.getElementById('diametroMax').textContent = 
                formatarNumero(parseFloat(diametroRange.max)) + 'mm';
        }
        
        const caboRange = document.getElementById('caboRange');
        if (caboRange) {
            caboRange.value = caboRange.max;
            document.getElementById('caboMax').textContent = 
                formatarNumero(parseFloat(caboRange.max)) + 'm';
        }
        
        document.getElementById('sortSelect').value = 'relevancia';
        
        // Reaplicar filtros (vai mostrar todos)
        aplicarFiltros();
        
        mostrarToast('Filtros limpos', 'info');
    };
    
    // Inicializar ao carregar página
    document.addEventListener('DOMContentLoaded', inicializar);
    
})();
