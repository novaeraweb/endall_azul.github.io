<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug do Carrinho - Endall Inspeções</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --cor-primaria: #0D1B2A;
            --cor-secundaria: #F5A623;
            --cor-sucesso: #28a745;
            --cor-erro: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--cor-primaria) 0%, #1a2f4a 100%);
            min-height: 100vh;
            padding: 2rem;
            color: white;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--cor-secundaria);
        }
        
        .debug-box {
            background: rgba(0, 0, 0, 0.3);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--cor-secundaria);
        }
        
        .debug-box h3 {
            margin-top: 0;
            color: var(--cor-secundaria);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .debug-box pre {
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 0.875rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--cor-secundaria);
            color: var(--cor-primaria);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            margin: 0.5rem 0.5rem 0.5rem 0;
        }
        
        .btn:hover {
            background: #e09416;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: var(--cor-erro);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-success {
            background: var(--cor-sucesso);
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-ok {
            background: var(--cor-sucesso);
            color: white;
        }
        
        .status-erro {
            background: var(--cor-erro);
            color: white;
        }
        
        .actions {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-bug"></i>
            Debug do Carrinho de Orçamento
        </h1>
        
        <div class="debug-box">
            <h3>
                <i class="fas fa-info-circle"></i>
                Status do LocalStorage
            </h3>
            <div id="statusLocalStorage"></div>
        </div>
        
        <div class="debug-box">
            <h3>
                <i class="fas fa-shopping-cart"></i>
                Dados do Carrinho
            </h3>
            <div id="dadosCarrinho"></div>
        </div>
        
        <div class="debug-box">
            <h3>
                <i class="fas fa-code"></i>
                JSON do Carrinho
            </h3>
            <pre id="jsonCarrinho"></pre>
        </div>
        
        <div class="debug-box">
            <h3>
                <i class="fas fa-boxes"></i>
                Itens no Carrinho
            </h3>
            <div id="itensCarrinho"></div>
        </div>
        
        <div class="actions">
            <button class="btn btn-success" onclick="testarCarrinho()">
                <i class="fas fa-sync"></i> Atualizar Debug
            </button>
            
            <button class="btn btn-danger" onclick="limparCarrinho()">
                <i class="fas fa-trash"></i> Limpar Carrinho
            </button>
            
            <button class="btn" onclick="adicionarProdutoTeste()">
                <i class="fas fa-plus"></i> Adicionar Produto de Teste
            </button>
            
            <a href="orcamento.php" class="btn">
                <i class="fas fa-file-invoice"></i> Ir para Orçamento
            </a>
            
            <a href="index.php" class="btn">
                <i class="fas fa-home"></i> Ir para Catálogo
            </a>
        </div>
    </div>
    
    <script>
        // Função para exibir o debug
        function testarCarrinho() {
            console.log('=== INICIANDO DEBUG DO CARRINHO ===');
            
            // 1. Verificar localStorage
            const statusLS = document.getElementById('statusLocalStorage');
            if (typeof(Storage) !== "undefined") {
                statusLS.innerHTML = '<span class="status status-ok"><i class="fas fa-check"></i> LocalStorage disponível</span>';
            } else {
                statusLS.innerHTML = '<span class="status status-erro"><i class="fas fa-times"></i> LocalStorage não disponível</span>';
                return;
            }
            
            // 2. Buscar dados do carrinho
            const carrinhoKey = 'endall_carrinho';
            const carrinhoRaw = localStorage.getItem(carrinhoKey);
            
            const dadosCarrinho = document.getElementById('dadosCarrinho');
            const jsonCarrinho = document.getElementById('jsonCarrinho');
            const itensCarrinho = document.getElementById('itensCarrinho');
            
            if (!carrinhoRaw) {
                dadosCarrinho.innerHTML = '<span class="status status-erro"><i class="fas fa-exclamation-triangle"></i> Carrinho vazio no localStorage</span>';
                jsonCarrinho.textContent = 'null';
                itensCarrinho.innerHTML = '<p style="color: var(--cor-erro);"><i class="fas fa-exclamation-circle"></i> Nenhum item encontrado</p>';
                return;
            }
            
            // 3. Exibir JSON bruto
            jsonCarrinho.textContent = carrinhoRaw;
            
            // 4. Tentar fazer parse
            let carrinho;
            try {
                carrinho = JSON.parse(carrinhoRaw);
                dadosCarrinho.innerHTML = `
                    <span class="status status-ok"><i class="fas fa-check"></i> Carrinho encontrado</span>
                    <p style="margin-top: 1rem;">
                        <strong>Total de itens:</strong> ${carrinho.length || 0}<br>
                        <strong>Tamanho do JSON:</strong> ${carrinhoRaw.length} caracteres
                    </p>
                `;
            } catch (e) {
                dadosCarrinho.innerHTML = `
                    <span class="status status-erro"><i class="fas fa-times"></i> Erro ao fazer parse</span>
                    <p style="color: var(--cor-erro); margin-top: 1rem;">${e.message}</p>
                `;
                itensCarrinho.innerHTML = '<p style="color: var(--cor-erro);"><i class="fas fa-exclamation-circle"></i> JSON inválido</p>';
                return;
            }
            
            // 5. Exibir itens
            if (!carrinho || carrinho.length === 0) {
                itensCarrinho.innerHTML = '<p style="color: var(--cor-erro);"><i class="fas fa-shopping-cart"></i> Carrinho vazio</p>';
                return;
            }
            
            let html = '<table style="width: 100%; border-collapse: collapse;">';
            html += '<thead><tr style="background: rgba(0,0,0,0.5); text-align: left;">';
            html += '<th style="padding: 0.75rem;">SKU</th>';
            html += '<th style="padding: 0.75rem;">Nome</th>';
            html += '<th style="padding: 0.75rem;">Qtd</th>';
            html += '<th style="padding: 0.75rem;">Série</th>';
            html += '</tr></thead><tbody>';
            
            carrinho.forEach((item, index) => {
                html += `<tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">`;
                html += `<td style="padding: 0.75rem;"><strong>${item.produto_sku || 'N/A'}</strong></td>`;
                html += `<td style="padding: 0.75rem;">${item.produto_nome || 'N/A'}</td>`;
                html += `<td style="padding: 0.75rem;">${item.quantidade || 1}</td>`;
                html += `<td style="padding: 0.75rem;">${item.produto_serie || 'N/A'}</td>`;
                html += `</tr>`;
            });
            
            html += '</tbody></table>';
            itensCarrinho.innerHTML = html;
            
            console.log('=== FIM DO DEBUG ===');
        }
        
        // Função para limpar carrinho
        function limparCarrinho() {
            if (confirm('Deseja realmente limpar o carrinho?')) {
                localStorage.removeItem('endall_carrinho');
                alert('Carrinho limpo com sucesso!');
                testarCarrinho();
            }
        }
        
        // Função para adicionar produto de teste
        function adicionarProdutoTeste() {
            const carrinho = JSON.parse(localStorage.getItem('endall_carrinho') || '[]');
            
            const produtoTeste = {
                produto_id: 999,
                produto_sku: 'TESTE-001',
                produto_nome: 'Produto de Teste - Debug',
                produto_serie: 'Série Teste',
                produto_serie_cor: '#F5A623',
                produto_imagem: 'assets/images/produto-sem-foto.svg',
                diametro: 5.0,
                comprimento: 3.0,
                quantidade: 1,
                observacoes: 'Produto adicionado via debug',
                adicionado_em: new Date().getTime()
            };
            
            carrinho.push(produtoTeste);
            localStorage.setItem('endall_carrinho', JSON.stringify(carrinho));
            
            alert('Produto de teste adicionado!');
            testarCarrinho();
        }
        
        // Executar debug ao carregar
        window.addEventListener('DOMContentLoaded', function() {
            testarCarrinho();
        });
    </script>
</body>
</html>
