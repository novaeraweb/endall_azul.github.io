<?php
/**
 * ENDALL INSPEÇÕES - Atualizar Imagens para Local
 * Script para substituir URLs externas pela imagem local
 * 
 * Acesse: http://localhost:8888/Endall/catalogo/projeto/atualizar-imagens.php
 */

// Definir constante do sistema
define('SISTEMA_ENDALL', true);

// Carregar configurações
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Verificar se deve executar a atualização
$executar = isset($_GET['executar']) && $_GET['executar'] === 'sim';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Imagens - Endall Inspeções</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --cor-primaria: #0D1B2A;
            --cor-secundaria: #F5A623;
            --cor-sucesso: #28a745;
            --cor-erro: #dc3545;
            --cor-branco: #FFFFFF;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--cor-primaria) 0%, #1a2f4a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .container {
            background: var(--cor-branco);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 3rem;
        }
        
        h1 {
            color: var(--cor-primaria);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        h1 i {
            color: var(--cor-secundaria);
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 4px;
        }
        
        .warning i {
            color: #ffc107;
            margin-right: 0.5rem;
        }
        
        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #0288d1;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 4px;
        }
        
        .info-box i {
            color: #0288d1;
            margin-right: 0.5rem;
        }
        
        .success-box {
            background: #d4edda;
            border-left: 4px solid var(--cor-sucesso);
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 4px;
        }
        
        .success-box i {
            color: var(--cor-sucesso);
            margin-right: 0.5rem;
        }
        
        .error-box {
            background: #f8d7da;
            border-left: 4px solid var(--cor-erro);
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 4px;
        }
        
        .error-box i {
            color: var(--cor-erro);
            margin-right: 0.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: var(--cor-secundaria);
            color: var(--cor-branco);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn:hover {
            background: #e09416;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 166, 35, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            margin-left: 1rem;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background: var(--cor-primaria);
            color: var(--cor-branco);
            font-weight: 600;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-image"></i>
            Atualizar Imagens dos Produtos
        </h1>
        <p class="subtitle">Sistema de Vendas - Endall Inspeções</p>
        
        <?php if (!$executar): ?>
            
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Atenção:</strong> Este script irá atualizar TODAS as imagens dos produtos no banco de dados.
            </div>
            
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <strong>O que será feito:</strong>
                <ul style="margin-left: 2rem; margin-top: 0.5rem;">
                    <li>Substituir URLs externas (via.placeholder.com) pela imagem local</li>
                    <li>Definir <code>assets/images/produto-sem-foto.svg</code> como imagem padrão</li>
                    <li>Atualizar todos os produtos ativos no banco de dados</li>
                </ul>
            </div>
            
            <?php
            // Verificar quantos produtos serão atualizados
            $sql_count = "SELECT COUNT(*) as total FROM produtos WHERE ativo = 1";
            $result = db()->queryRow($sql_count);
            $total_produtos = $result['total'] ?? 0;
            
            // Verificar quantos têm URLs externas
            $sql_externos = "SELECT COUNT(*) as total FROM produtos WHERE ativo = 1 AND imagens LIKE '%placeholder%'";
            $result_externos = db()->queryRow($sql_externos);
            $total_externos = $result_externos['total'] ?? 0;
            ?>
            
            <div class="info-box">
                <i class="fas fa-database"></i>
                <strong>Estatísticas:</strong>
                <ul style="margin-left: 2rem; margin-top: 0.5rem;">
                    <li><strong><?= $total_produtos ?></strong> produtos ativos no banco de dados</li>
                    <li><strong><?= $total_externos ?></strong> produtos com URLs externas (via.placeholder.com)</li>
                    <li><strong><?= $total_produtos - $total_externos ?></strong> produtos já com imagens locais</li>
                </ul>
            </div>
            
            <div class="actions">
                <a href="?executar=sim" class="btn">
                    <i class="fas fa-play"></i> Executar Atualização
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
            
        <?php else: ?>
            
            <?php
            try {
                // Executar atualização
                $sql_update = "UPDATE produtos 
                              SET imagens = '[\"assets/images/produto-sem-foto.svg\"]'
                              WHERE ativo = 1";
                
                $resultado = db()->exec($sql_update);
                
                // Buscar produtos atualizados para exibir
                $sql_produtos = "SELECT id, sku, nome, imagens 
                                FROM produtos 
                                WHERE ativo = 1 
                                ORDER BY id 
                                LIMIT 10";
                $produtos = db()->query($sql_produtos);
                
                ?>
                
                <div class="success-box">
                    <i class="fas fa-check-circle"></i>
                    <strong>Sucesso!</strong> Todas as imagens foram atualizadas com sucesso.
                </div>
                
                <div class="info-box">
                    <i class="fas fa-database"></i>
                    <strong>Resultado:</strong>
                    <ul style="margin-left: 2rem; margin-top: 0.5rem;">
                        <li><strong><?= $resultado ?></strong> produtos atualizados</li>
                        <li>Todas as imagens agora apontam para: <code>assets/images/produto-sem-foto.svg</code></li>
                    </ul>
                </div>
                
                <h3 style="margin-top: 2rem; color: var(--cor-primaria);">
                    <i class="fas fa-list"></i> Amostra de Produtos Atualizados
                </h3>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Nome</th>
                            <th>Imagem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?= $produto['id'] ?></td>
                                <td><strong><?= htmlspecialchars($produto['sku']) ?></strong></td>
                                <td><?= htmlspecialchars($produto['nome']) ?></td>
                                <td>
                                    <small style="color: var(--cor-sucesso);">
                                        <i class="fas fa-check"></i> Local
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="actions">
                    <a href="index.php" class="btn">
                        <i class="fas fa-home"></i> Ir para o Catálogo
                    </a>
                    <a href="produto.php?id=1" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> Ver Produto
                    </a>
                </div>
                
                <div class="info-box" style="margin-top: 2rem;">
                    <i class="fas fa-info-circle"></i>
                    <strong>Próximos passos:</strong>
                    <ol style="margin-left: 2rem; margin-top: 0.5rem;">
                        <li>Limpe o cache do navegador (Ctrl + Shift + R)</li>
                        <li>Acesse o catálogo para verificar as imagens</li>
                        <li>Adicione imagens reais na pasta <code>uploads/produtos/</code> quando disponíveis</li>
                    </ol>
                </div>
                
                <?php
            } catch (Exception $e) {
                ?>
                
                <div class="error-box">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Erro!</strong> Não foi possível atualizar as imagens.
                    <br><br>
                    <code><?= htmlspecialchars($e->getMessage()) ?></code>
                </div>
                
                <div class="actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Voltar para o Catálogo
                    </a>
                </div>
                
                <?php
            }
            ?>
            
        <?php endif; ?>
        
    </div>
</body>
</html>
