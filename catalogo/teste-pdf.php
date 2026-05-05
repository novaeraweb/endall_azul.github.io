<?php
/**
 * Teste de Geração de PDF
 * Busca o último orçamento e gera o PDF
 */

define('SISTEMA_ENDALL', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Verificar se vendor existe
$vendorPath = __DIR__ . '/vendor/autoload.php';
$vendorExists = file_exists($vendorPath);

// Carregar autoload se existir
if ($vendorExists) {
    require_once $vendorPath;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Geração de PDF - Endall</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .content {
            padding: 2rem;
        }
        
        .status-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
        }
        
        .status-box h3 {
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .status-icon.success { color: #28a745; }
        .status-icon.error { color: #dc3545; }
        .status-icon.warning { color: #ffc107; }
        
        .orcamento-card {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        
        .orcamento-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .orcamento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .orcamento-numero {
            font-size: 1.25rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .orcamento-data {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .orcamento-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 0.975rem;
            color: #212529;
            font-weight: 500;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        
        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📄 Teste de Geração de PDF</h1>
            <p>Sistema de Orçamentos Endall Inspeções</p>
        </div>
        
        <div class="content">
            <!-- Status do Sistema -->
            <div class="status-box">
                <h3>🔍 Status do Sistema</h3>
                
                <div class="status-item">
                    <span class="status-icon <?= $vendorExists ? 'success' : 'error' ?>">
                        <?= $vendorExists ? '✅' : '❌' ?>
                    </span>
                    <div>
                        <strong>Vendor (Composer):</strong>
                        <?= $vendorExists ? 'Instalado' : 'NÃO INSTALADO' ?>
                    </div>
                </div>
                
                <?php if ($vendorExists): ?>
                <div class="status-item">
                    <span class="status-icon <?= class_exists('Mpdf\Mpdf') ? 'success' : 'error' ?>">
                        <?= class_exists('Mpdf\Mpdf') ? '✅' : '❌' ?>
                    </span>
                    <div>
                        <strong>mPDF:</strong>
                        <?= class_exists('Mpdf\Mpdf') ? 'Instalado' : 'NÃO ENCONTRADO' ?>
                    </div>
                </div>
                
                <div class="status-item">
                    <span class="status-icon <?= is_dir(DIR_PDFS) ? 'success' : 'warning' ?>">
                        <?= is_dir(DIR_PDFS) ? '✅' : '⚠️' ?>
                    </span>
                    <div>
                        <strong>Diretório de PDFs:</strong>
                        <?= is_dir(DIR_PDFS) ? 'Existe' : 'Será criado automaticamente' ?>
                        <br><small style="color: #6c757d;"><?= DIR_PDFS ?></small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!$vendorExists): ?>
                <!-- Erro: Vendor não instalado -->
                <div class="alert alert-danger">
                    <strong>❌ Dependências Não Instaladas</strong>
                    <p style="margin-top: 0.5rem;">
                        Execute o seguinte comando no terminal:
                    </p>
                    <pre>cd <?= __DIR__ ?>
composer install</pre>
                </div>
                
            <?php elseif (!class_exists('Mpdf\Mpdf')): ?>
                <!-- Erro: mPDF não encontrado -->
                <div class="alert alert-danger">
                    <strong>❌ mPDF Não Encontrado</strong>
                    <p style="margin-top: 0.5rem;">
                        Execute o seguinte comando no terminal:
                    </p>
                    <pre>cd <?= __DIR__ ?>
composer require mpdf/mpdf</pre>
                </div>
                
            <?php else: ?>
                <!-- Sistema OK - Buscar orçamentos -->
                <?php
                // Buscar últimos orçamentos
                $orcamentos = db()->query("
                    SELECT * FROM orcamentos 
                    ORDER BY criado_em DESC 
                    LIMIT 5
                ");
                
                if (empty($orcamentos)):
                ?>
                    <div class="alert alert-warning">
                        <strong>⚠️ Nenhum Orçamento Encontrado</strong>
                        <p style="margin-top: 0.5rem;">
                            Crie um orçamento primeiro acessando:
                            <a href="orcamento.php" style="color: #667eea; font-weight: 600;">
                                Página de Orçamento
                            </a>
                        </p>
                    </div>
                    
                <?php else: ?>
                    <h3 style="color: #667eea; margin-bottom: 1.5rem;">
                        📋 Últimos Orçamentos
                    </h3>
                    
                    <?php foreach ($orcamentos as $orc): ?>
                        <?php
                        $itens = json_decode($orc['itens'], true);
                        $total_itens = is_array($itens) ? count($itens) : 0;
                        ?>
                        <div class="orcamento-card">
                            <div class="orcamento-header">
                                <div class="orcamento-numero">
                                    <?= htmlspecialchars($orc['numero']) ?>
                                </div>
                                <div class="orcamento-data">
                                    <?= date('d/m/Y H:i', strtotime($orc['criado_em'])) ?>
                                </div>
                            </div>
                            
                            <div class="orcamento-info">
                                <div class="info-item">
                                    <div class="info-label">Cliente</div>
                                    <div class="info-value"><?= htmlspecialchars($orc['cliente_nome']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">E-mail</div>
                                    <div class="info-value"><?= htmlspecialchars($orc['cliente_email']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Telefone</div>
                                    <div class="info-value"><?= htmlspecialchars($orc['cliente_telefone']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Total de Itens</div>
                                    <div class="info-value"><?= $total_itens ?> produto(s)</div>
                                </div>
                            </div>
                            
                            <a href="gerar-pdf.php?id=<?= $orc['id'] ?>" 
                               class="btn" 
                               target="_blank">
                                📄 Gerar PDF
                            </a>
                        </div>
                    <?php endforeach; ?>
                    
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Instruções -->
            <div class="status-box" style="border-left-color: #17a2b8; margin-top: 2rem;">
                <h3 style="color: #17a2b8;">💡 Como Testar</h3>
                <ol style="line-height: 2; color: #495057; padding-left: 1.5rem;">
                    <li>Verifique se todas as dependências estão instaladas (✅ acima)</li>
                    <li>Clique em <strong>"Gerar PDF"</strong> em um dos orçamentos</li>
                    <li>O PDF deve ser baixado automaticamente</li>
                    <li>Verifique se o PDF contém todos os dados corretos</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
