/**
 * ENDALL INSPEÇÕES - JavaScript Principal
 * Funções utilitárias e componentes globais
 * 
 * @version 1.0.0
 */

(function() {
    'use strict';
    
    // =============================================
    // TOAST NOTIFICATIONS
    // =============================================
    
    /**
     * Mostrar notificação toast
     * @param {string} mensagem - Mensagem a ser exibida
     * @param {string} tipo - Tipo: success, error, warning, info
     * @param {number} duracao - Duração em ms (padrão: 3000)
     */
    window.mostrarToast = function(mensagem, tipo = 'info', duracao = 3000) {
        // Remover toasts existentes
        const toastsAntigos = document.querySelectorAll('.toast');
        toastsAntigos.forEach(toast => toast.remove());
        
        // Criar novo toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${tipo}`;
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-${getTipoIcon(tipo)}" style="font-size: 1.25rem;"></i>
                <span style="flex: 1;">${mensagem}</span>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1.25rem; padding: 0; margin-left: 0.5rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Remover automaticamente
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-out forwards';
            setTimeout(() => toast.remove(), 300);
        }, duracao);
    };
    
    function getTipoIcon(tipo) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[tipo] || 'info-circle';
    }
    
    // Adicionar animação slideOutRight
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // =============================================
    // CSRF TOKEN
    // =============================================
    
    /**
     * Obter CSRF token do meta tag
     */
    window.obterCSRFToken = function() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };
    
    // =============================================
    // AJAX HELPERS
    // =============================================
    
    /**
     * Requisição AJAX simplificada
     * @param {string} url - URL da requisição
     * @param {object} options - Opções (method, data, headers)
     */
    window.ajax = function(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': obterCSRFToken()
            }
        };
        
        const config = { ...defaults, ...options };
        
        if (config.data && config.method !== 'GET') {
            config.body = JSON.stringify(config.data);
        }
        
        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                throw error;
            });
    };
    
    // =============================================
    // LOADING SPINNER
    // =============================================
    
    /**
     * Mostrar loading spinner
     * @param {string} containerId - ID do container
     */
    window.mostrarLoading = function(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = '<div class="spinner"></div>';
        }
    };
    
    /**
     * Esconder loading spinner
     * @param {string} containerId - ID do container
     */
    window.esconderLoading = function(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            const spinner = container.querySelector('.spinner');
            if (spinner) spinner.remove();
        }
    };
    
    // =============================================
    // MODAL
    // =============================================
    
    /**
     * Abrir modal
     * @param {string} modalId - ID do modal
     */
    window.abrirModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };
    
    /**
     * Fechar modal
     * @param {string} modalId - ID do modal
     */
    window.fecharModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    };
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // =============================================
    // FORMATAÇÃO
    // =============================================
    
    /**
     * Formatar valor monetário
     */
    window.formatarMoeda = function(valor) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor);
    };
    
    /**
     * Formatar número
     */
    window.formatarNumero = function(numero, decimais = 1) {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: decimais,
            maximumFractionDigits: decimais
        }).format(numero);
    };
    
    /**
     * Formatar telefone
     */
    window.formatarTelefone = function(telefone) {
        const numeros = telefone.replace(/\D/g, '');
        if (numeros.length === 11) {
            return numeros.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (numeros.length === 10) {
            return numeros.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        }
        return telefone;
    };
    
    // =============================================
    // VALIDAÇÃO DE FORMULÁRIOS
    // =============================================
    
    /**
     * Validar e-mail
     */
    window.validarEmail = function(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    };
    
    /**
     * Validar telefone
     */
    window.validarTelefone = function(telefone) {
        const numeros = telefone.replace(/\D/g, '');
        return numeros.length >= 10 && numeros.length <= 11;
    };
    
    /**
     * Validar campo obrigatório
     */
    window.validarObrigatorio = function(valor) {
        return valor && valor.trim().length > 0;
    };
    
    // =============================================
    // SMOOTH SCROLL
    // =============================================
    
    /**
     * Scroll suave para elemento
     */
    window.scrollPara = function(elementoId) {
        const elemento = document.getElementById(elementoId);
        if (elemento) {
            elemento.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    };
    
    // =============================================
    // DEBOUNCE
    // =============================================
    
    /**
     * Debounce para limitar chamadas de função
     */
    window.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };
    
    // =============================================
    // LAZY LOADING DE IMAGENS
    // =============================================
    
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // =============================================
    // COPIAR PARA CLIPBOARD
    // =============================================
    
    /**
     * Copiar texto para clipboard
     */
    window.copiarTexto = function(texto) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(texto).then(() => {
                mostrarToast('Texto copiado!', 'success');
            }).catch(() => {
                mostrarToast('Erro ao copiar texto', 'error');
            });
        } else {
            // Fallback para navegadores antigos
            const textarea = document.createElement('textarea');
            textarea.value = texto;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                mostrarToast('Texto copiado!', 'success');
            } catch (err) {
                mostrarToast('Erro ao copiar texto', 'error');
            }
            document.body.removeChild(textarea);
        }
    };
    
    // =============================================
    // MÁSCARAS DE INPUT
    // =============================================
    
    /**
     * Aplicar máscara de telefone
     */
    window.mascaraTelefone = function(input) {
        let valor = input.value.replace(/\D/g, '');
        
        if (valor.length <= 10) {
            valor = valor.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else {
            valor = valor.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        }
        
        input.value = valor;
    };
    
    /**
     * Aplicar máscara de CEP
     */
    window.mascaraCEP = function(input) {
        let valor = input.value.replace(/\D/g, '');
        valor = valor.replace(/(\d{5})(\d{0,3})/, '$1-$2');
        input.value = valor;
    };
    
    // =============================================
    // MENU MOBILE
    // =============================================
    
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            const navMenu = document.querySelector('.nav-menu');
            if (navMenu) {
                navMenu.classList.toggle('active');
            }
        });
    }
    
    // =============================================
    // BACK TO TOP
    // =============================================
    
    // Criar botão "Voltar ao topo"
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        background-color: var(--cor-secundaria);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: var(--shadow-lg);
        z-index: 999;
        transition: all 0.3s;
    `;
    
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    document.body.appendChild(backToTopBtn);
    
    // Mostrar/ocultar botão baseado no scroll
    window.addEventListener('scroll', debounce(() => {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = 'flex';
        } else {
            backToTopBtn.style.display = 'none';
        }
    }, 100));
    
    // =============================================
    // INICIALIZAÇÃO
    // =============================================
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Endall Inspeções - Sistema carregado');
        
        // Adicionar classe de animação aos cards de produto
        const produtoCards = document.querySelectorAll('.produto-card');
        produtoCards.forEach((card, index) => {
            card.style.animation = `fadeInUp 0.5s ease-out ${index * 0.05}s both`;
        });
        
        // Tooltip para elementos com atributo data-tooltip
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            el.style.position = 'relative';
            el.style.cursor = 'help';
            
            el.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = this.dataset.tooltip;
                tooltip.style.cssText = `
                    position: absolute;
                    bottom: 100%;
                    left: 50%;
                    transform: translateX(-50%);
                    background: var(--cor-primaria);
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: var(--border-radius-md);
                    font-size: 0.875rem;
                    white-space: nowrap;
                    margin-bottom: 0.5rem;
                    z-index: 1000;
                    box-shadow: var(--shadow-lg);
                `;
                this.appendChild(tooltip);
            });
            
            el.addEventListener('mouseleave', function() {
                const tooltip = this.querySelector('.tooltip');
                if (tooltip) tooltip.remove();
            });
        });
    });
    
    // Adicionar animação fadeInUp
    const animStyle = document.createElement('style');
    animStyle.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(animStyle);
    
})();

/* === Mobile filter collapse === */
// Adiciona collapse mobile no sidebar de filtros
(function() {
  if (window.matchMedia && window.matchMedia('(max-width: 600px)').matches) {
    document.addEventListener('DOMContentLoaded', function() {
      var sidebar = document.querySelector('.sidebar');
      var title = sidebar && sidebar.querySelector('.sidebar-title');
      if (sidebar && title) {
        // Inicia colapsado em mobile
        sidebar.classList.add('collapsed');
        title.addEventListener('click', function() {
          sidebar.classList.toggle('collapsed');
        });
      }
    });
  }
})();
