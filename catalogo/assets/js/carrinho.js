/**
 * ENDALL INSPEÇÕES - Sistema de Carrinho de Orçamento
 * Gerenciamento do carrinho usando LocalStorage
 * 
 * @version 1.0.0
 */

const Carrinho = (function() {
    'use strict';
    
    // Configurações
    const CONFIG = {
        storageKey: 'endall_carrinho',
        limiteItens: 20
    };
    
    /**
     * Obter carrinho do localStorage
     */
    function obterCarrinho() {
        try {
            const data = localStorage.getItem(CONFIG.storageKey);
            return data ? JSON.parse(data) : [];
        } catch (e) {
            console.error('Erro ao obter carrinho:', e);
            return [];
        }
    }
    
    /**
     * Salvar carrinho no localStorage
     */
    function salvarCarrinho(carrinho) {
        try {
            localStorage.setItem(CONFIG.storageKey, JSON.stringify(carrinho));
            atualizarContador();
            dispararEvento('carrinhoAtualizado', { carrinho });
            return true;
        } catch (e) {
            console.error('Erro ao salvar carrinho:', e);
            return false;
        }
    }
    
    /**
     * Adicionar produto ao carrinho
     */
    function normalizarId(id) {
        const numero = parseInt(id, 10);
        return Number.isNaN(numero) ? String(id) : numero;
    }

    function mesmoId(a, b) {
        return String(a) === String(b);
    }

    function adicionar(produto) {
        let carrinho = obterCarrinho();
        
        // Validar dados do produto
        if (!produto || !produto.id || !produto.sku || !produto.nome) {
            mostrarToast('Dados do produto inválidos', 'error');
            return false;
        }
        
        produto.id = normalizarId(produto.id);

        // Verificar se produto já está no carrinho
        const existe = carrinho.find(item => mesmoId(item.id, produto.id));
        if (existe) {
            mostrarToast('Este produto já está no orçamento', 'warning');
            return false;
        }
        
        // Verificar limite de itens
        if (carrinho.length >= CONFIG.limiteItens) {
            mostrarToast(`Limite de ${CONFIG.limiteItens} produtos atingido`, 'warning');
            return false;
        }
        
        // Adicionar produto
        carrinho.push({
            id: produto.id,
            sku: produto.sku,
            nome: produto.nome,
            serie_nome: produto.serie_nome || '',
            serie_cor: produto.serie_cor || '#0D1B2A',
            imagem: produto.imagem || '',
            diametro_camera: produto.diametro_camera || '',
            comprimento_cabo: produto.comprimento_cabo || '',
            quantidade: 1,
            observacoes: '',
            adicionado_em: new Date().toISOString()
        });
        
        salvarCarrinho(carrinho);
        dispararEvento('produtoAdicionado', { produto });
        mostrarToast('Produto adicionado ao orçamento!', 'success');
        
        // Animar botão do carrinho
        animarCarrinho();
        
        return true;
    }
    
    /**
     * Remover produto do carrinho
     */
    function remover(produtoId) {
        let carrinho = obterCarrinho();
        carrinho = carrinho.filter(item => !mesmoId(item.id, produtoId));
        salvarCarrinho(carrinho);
        dispararEvento('produtoRemovido', { produtoId });
        mostrarToast('Produto removido do orçamento', 'info');
        return true;
    }
    
    /**
     * Limpar carrinho
     */
    function limpar() {
        localStorage.removeItem(CONFIG.storageKey);
        atualizarContador();
        dispararEvento('carrinhoLimpo');
        return true;
    }
    
    /**
     * Atualizar quantidade de um produto
     */
    function atualizarQuantidade(produtoId, quantidade) {
        let carrinho = obterCarrinho();
        const item = carrinho.find(item => mesmoId(item.id, produtoId));
        
        if (item) {
            item.quantidade = Math.max(1, parseInt(quantidade) || 1);
            salvarCarrinho(carrinho);
            return true;
        }
        return false;
    }
    
    /**
     * Atualizar observações de um produto
     */
    function atualizarObservacoes(produtoId, observacoes) {
        let carrinho = obterCarrinho();
        const item = carrinho.find(item => mesmoId(item.id, produtoId));
        
        if (item) {
            item.observacoes = observacoes;
            salvarCarrinho(carrinho);
            return true;
        }
        return false;
    }
    
    /**
     * Verificar se produto está no carrinho
     */
    function temProduto(produtoId) {
        const carrinho = obterCarrinho();
        return carrinho.some(item => mesmoId(item.id, produtoId));
    }
    
    /**
     * Contar itens no carrinho
     */
    function contar() {
        return obterCarrinho().length;
    }
    
    /**
     * Atualizar contador visual no header
     */
    function atualizarContador() {
        const contador = document.getElementById('carrinhoCount');
        if (contador) {
            const total = contar();
            contador.textContent = total;
            contador.style.display = total > 0 ? 'inline-block' : 'none';
        }
    }
    
    /**
     * Animar ícone do carrinho
     */
    function animarCarrinho() {
        const badge = document.getElementById('carrinhoBadge');
        if (badge) {
            badge.style.animation = 'none';
            setTimeout(() => {
                badge.style.animation = 'pulse 0.5s ease-in-out';
            }, 10);
        }
    }
    
    /**
     * Disparar evento customizado
     */
    function dispararEvento(nome, dados = {}) {
        const evento = new CustomEvent(nome, { detail: dados });
        window.dispatchEvent(evento);
    }
    
    /**
     * Renderizar carrinho na página de orçamento
     */
    function renderizar(containerId = 'carrinhoItens') {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const carrinho = obterCarrinho();
        
        if (carrinho.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 3rem; background: var(--cor-cinza-claro); border-radius: var(--border-radius-lg);">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: var(--cor-cinza); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--cor-cinza-escuro); margin-bottom: 0.5rem;">Carrinho Vazio</h3>
                    <p style="color: var(--cor-cinza); margin-bottom: 1.5rem;">Adicione produtos ao orçamento para continuar</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-th-large"></i> Ver Catálogo
                    </a>
                </div>
            `;
            return;
        }
        
        let html = '<div class="carrinho-lista">';
        
        carrinho.forEach((item, index) => {
            html += `
                <div class="carrinho-item" data-id="${item.id}">
                    <div class="item-image">
                        <img src="${item.imagem || 'https://via.placeholder.com/150x150/0D1B2A/2F81DF?text=Produto'}" 
                             alt="${item.nome}" 
                             onerror="this.src='https://via.placeholder.com/150x150/0D1B2A/2F81DF?text=Produto'">
                    </div>
                    <div class="item-info">
                        <div class="item-header">
                            <span class="serie-badge" style="background-color: ${item.serie_cor}">
                                ${item.serie_nome}
                            </span>
                            <span class="item-sku">${item.sku}</span>
                        </div>
                        <h4 class="item-nome">${item.nome}</h4>
                        <div class="item-specs">
                            ${item.diametro_camera ? `<span><i class="fas fa-circle"></i> Ø ${item.diametro_camera}mm</span>` : ''}
                            ${item.comprimento_cabo ? `<span><i class="fas fa-ruler"></i> ${item.comprimento_cabo}m</span>` : ''}
                        </div>
                    </div>
                    <div class="item-controls">
                        <div class="form-group">
                            <label>Quantidade:</label>
                            <input type="number" 
                                   class="quantidade-input" 
                                   value="${item.quantidade}" 
                                   min="1" 
                                   max="99"
                                   data-id="${item.id}">
                        </div>
                        <div class="form-group">
                            <label>Observações:</label>
                            <textarea class="observacoes-input" 
                                      placeholder="Ex: Necessidade específica..."
                                      data-id="${item.id}"
                                      rows="2">${item.observacoes || ''}</textarea>
                        </div>
                        <button class="btn-remover" data-id="${item.id}" title="Remover produto">
                            <i class="fas fa-trash-alt"></i> Remover
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        // Adicionar resumo
        html += `
            <div class="carrinho-resumo">
                <div class="resumo-item">
                    <span>Total de Produtos:</span>
                    <strong>${carrinho.length}</strong>
                </div>
                <div class="resumo-item">
                    <span>Total de Unidades:</span>
                    <strong>${carrinho.reduce((acc, item) => acc + item.quantidade, 0)}</strong>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Adicionar event listeners
        adicionarEventListeners();
    }
    
    /**
     * Adicionar event listeners aos elementos do carrinho
     */
    function adicionarEventListeners() {
        // Botões de remover
        document.querySelectorAll('.btn-remover').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                if (confirm('Deseja remover este produto do orçamento?')) {
                    remover(id);
                    renderizar();
                }
            });
        });
        
        // Inputs de quantidade
        document.querySelectorAll('.quantidade-input').forEach(input => {
            input.addEventListener('change', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const quantidade = parseInt(this.value);
                atualizarQuantidade(id, quantidade);
            });
        });
        
        // Textareas de observações
        document.querySelectorAll('.observacoes-input').forEach(textarea => {
            textarea.addEventListener('blur', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const observacoes = this.value.trim();
                atualizarObservacoes(id, observacoes);
            });
        });
    }
    
    /**
     * Preparar dados para envio
     */
    function prepararParaEnvio() {
        const carrinho = obterCarrinho();
        return carrinho.map(item => ({
            produto_id: item.id,
            sku: item.sku,
            nome: item.nome,
            serie_nome: item.serie_nome,
            quantidade: item.quantidade,
            observacoes: item.observacoes,
            diametro_camera: item.diametro_camera,
            comprimento_cabo: item.comprimento_cabo
        }));
    }
    
    // API pública
    return {
        adicionar,
        remover,
        limpar,
        obter: obterCarrinho,
        temProduto,
        contar,
        atualizarContador,
        atualizarQuantidade,
        atualizarObservacoes,
        renderizar,
        prepararParaEnvio
    };
})();

// Compatibilidade com páginas antigas que chamavam adicionarAoCarrinho(produto)
window.adicionarAoCarrinho = function(produto) {
    return Carrinho.adicionar(produto);
};

// Adicionar animação CSS para pulse
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .carrinho-lista {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .carrinho-item {
        display: grid;
        grid-template-columns: 150px 1fr auto;
        gap: 1.5rem;
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.3s;
    }
    
    .carrinho-item:hover {
        box-shadow: var(--shadow-md);
    }
    
    .item-image img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: var(--border-radius-md);
    }
    
    .item-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .item-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .item-sku {
        font-size: 0.75rem;
        color: var(--cor-cinza);
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .item-nome {
        color: var(--cor-primaria);
        font-size: 1.125rem;
        margin: 0;
    }
    
    .item-specs {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: var(--cor-cinza-escuro);
    }
    
    .item-specs i {
        color: var(--cor-secundaria);
        margin-right: 0.25rem;
    }
    
    .item-controls {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-width: 200px;
    }
    
    .quantidade-input {
        width: 80px;
        padding: 0.5rem;
        border: 2px solid var(--cor-cinza-medio);
        border-radius: var(--border-radius-md);
        font-size: 1rem;
    }
    
    .observacoes-input {
        width: 100%;
        padding: 0.5rem;
        border: 2px solid var(--cor-cinza-medio);
        border-radius: var(--border-radius-md);
        font-family: inherit;
        font-size: 0.875rem;
        resize: vertical;
    }
    
    .btn-remover {
        padding: 0.5rem 1rem;
        background-color: transparent;
        border: 2px solid var(--cor-erro);
        color: var(--cor-erro);
        border-radius: var(--border-radius-md);
        cursor: pointer;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .btn-remover:hover {
        background-color: var(--cor-erro);
        color: white;
    }
    
    .carrinho-resumo {
        background: var(--cor-cinza-claro);
        padding: 1.5rem;
        border-radius: var(--border-radius-lg);
        margin-top: 1.5rem;
    }
    
    .resumo-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 1rem;
    }
    
    @media (max-width: 768px) {
        .carrinho-item {
            grid-template-columns: 1fr;
        }
        
        .item-controls {
            min-width: auto;
        }
    }
`;
document.head.appendChild(style);

// Exportar para uso global
window.Carrinho = Carrinho;
