<?php
/**
 * ENDALL INSPEÇÕES - Geração de PDF do Orçamento
 * Gera PDF profissional com identidade visual Endall
 */

// Definir constante do sistema
define('SISTEMA_ENDALL', true);

// Carregar configurações
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Verificar se o Composer está instalado
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('
    <div style="font-family: Arial; padding: 40px; background: #fee; border: 3px solid #c00; border-radius: 10px; margin: 50px auto; max-width: 600px;">
        <h2 style="color: #c00; margin-top: 0;">⚠️ Dependências Não Instaladas</h2>
        <p style="font-size: 16px; line-height: 1.6;">
            As bibliotecas necessárias não estão instaladas.<br><br>
            <strong>Execute no terminal:</strong>
        </p>
        <pre style="background: #333; color: #0f0; padding: 15px; border-radius: 5px; overflow-x: auto;">
cd ' . __DIR__ . '
composer install
        </pre>
        <p style="font-size: 14px; color: #666; margin-bottom: 0;">
            Se não tem o Composer instalado, visite: 
            <a href="https://getcomposer.org" target="_blank">getcomposer.org</a>
        </p>
    </div>
    ');
}

// Carregar autoload do Composer
require_once __DIR__ . '/vendor/autoload.php';

// Importar mPDF
use Mpdf\Mpdf;

// Obter ID ou número do orçamento
$orcamento_id = (int) getParam('id', 0);
$orcamento_numero = getParam('numero', '');

// Buscar orçamento
if ($orcamento_id > 0) {
    $orcamento = db()->queryRow("SELECT * FROM orcamentos WHERE id = ?", [$orcamento_id]);
} elseif (!empty($orcamento_numero)) {
    $orcamento = db()->queryRow("SELECT * FROM orcamentos WHERE numero = ?", [$orcamento_numero]);
} else {
    die('Orçamento não especificado');
}

if (!$orcamento) {
    die('Orçamento não encontrado');
}

// Decodificar itens
$itens = json_decode($orcamento['itens'], true);

if (empty($itens)) {
    die('Orçamento sem itens');
}

// Criar instância do mPDF
try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 20,
        'margin_bottom' => 20,
        'margin_header' => 10,
        'margin_footer' => 10
    ]);
    
    // Configurações do PDF
    $mpdf->SetTitle('Orçamento ' . $orcamento['numero'] . ' - Endall Inspeções');
    $mpdf->SetAuthor('Endall Inspeções');
    $mpdf->SetCreator('Sistema de Vendas Endall');
    $mpdf->SetSubject('Orçamento de Produtos');
    
    // Gerar HTML do PDF
    $html = gerarHTMLPDF($orcamento, $itens);
    
    // Escrever HTML no PDF
    $mpdf->WriteHTML($html);
    
    // Definir nome do arquivo
    $filename = 'orcamento_' . $orcamento['numero'] . '.pdf';
    $filepath = DIR_PDFS . '/' . $filename;
    
    // Criar diretório se não existir
    if (!is_dir(DIR_PDFS)) {
        mkdir(DIR_PDFS, 0755, true);
    }
    
    // Salvar PDF no servidor
    $mpdf->Output($filepath, \Mpdf\Output\Destination::FILE);
    
    // Atualizar caminho no banco de dados
    db()->execute("UPDATE orcamentos SET pdf_path = ? WHERE id = ?", [$filepath, $orcamento['id']]);
    
    // Registrar log
    registrarLog('pdf_gerado', "PDF do orçamento {$orcamento['numero']} gerado com sucesso");
    
    // Baixar PDF no navegador
    $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die('Erro ao gerar PDF: ' . $e->getMessage());
    } else {
        registrarLog('pdf_erro', "Erro ao gerar PDF: " . $e->getMessage());
        die('Erro ao gerar PDF. Por favor, entre em contato com o suporte.');
    }
}

/**
 * Gerar HTML completo do PDF
 */
function gerarHTMLPDF($orcamento, $itens) {
    $data_formatada = formatarData($orcamento['criado_em'], true);
    
    // Calcular totais
    $total_produtos = count($itens);
    $total_unidades = array_sum(array_column($itens, 'quantidade'));
    
    $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #0D1B2A;
        }
        .header {
            background: linear-gradient(135deg, #0D1B2A 0%, #1a3a52 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .header-logo {
            font-size: 28pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header-logo .orange {
            color: #2F81DF;
        }
        .header-info {
            font-size: 10pt;
            opacity: 0.9;
            line-height: 1.8;
        }
        .header-orcamento {
            background-color: #2F81DF;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            text-align: center;
        }
        .header-orcamento h2 {
            margin: 0;
            font-size: 18pt;
        }
        .header-orcamento p {
            margin: 5px 0 0 0;
            font-size: 11pt;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #0D1B2A;
            color: white;
            padding: 10px 15px;
            font-size: 13pt;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .cliente-info {
            background-color: #f9fafb;
            padding: 15px;
            border-left: 4px solid #2F81DF;
            border-radius: 4px;
        }
        .cliente-info p {
            margin: 8px 0;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table thead {
            background-color: #0D1B2A;
            color: white;
        }
        table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 10pt;
            font-weight: bold;
        }
        table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
        }
        table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .produto-sku {
            font-weight: bold;
            color: #2F81DF;
        }
        .produto-nome {
            color: #0D1B2A;
            font-weight: 600;
        }
        .produto-specs {
            color: #6b7280;
            font-size: 9pt;
        }
        .produto-obs {
            color: #374151;
            font-style: italic;
            font-size: 9pt;
            margin-top: 5px;
        }
        .totais {
            background-color: #f4f6f9;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .totais p {
            margin: 8px 0;
            font-size: 12pt;
        }
        .totais strong {
            color: #0D1B2A;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #2F81DF;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer strong {
            color: #0D1B2A;
        }
        .obs-importante {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .obs-importante h4 {
            margin: 0 0 10px 0;
            color: #92400e;
        }
        .obs-importante p {
            margin: 0;
            color: #78350f;
            font-size: 10pt;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    
    <!-- Cabeçalho -->
    <div class="header">
        <div class="header-logo">
            ENDALL <span class="orange">INSPEÇÕES</span>
        </div>
        <div class="header-info">
            <strong>' . EMPRESA_NOME . '</strong><br>
            ' . EMPRESA_ENDERECO . '<br>
            Tel: ' . EMPRESA_TELEFONE . ' | E-mail: ' . EMPRESA_EMAIL . '<br>
            Site: ' . EMPRESA_SITE . '
        </div>
        <div class="header-orcamento">
            <h2>ORÇAMENTO Nº ' . htmlspecialchars($orcamento['numero']) . '</h2>
            <p>Emitido em: ' . $data_formatada . '</p>
        </div>
    </div>
    
    <!-- Dados do Cliente -->
    <div class="section">
        <div class="section-title">👤 DADOS DO CLIENTE</div>
        <div class="cliente-info">
            <p><strong>Nome:</strong> ' . htmlspecialchars($orcamento['cliente_nome']) . '</p>';
    
    if (!empty($orcamento['cliente_empresa'])) {
        $html .= '<p><strong>Empresa:</strong> ' . htmlspecialchars($orcamento['cliente_empresa']) . '</p>';
    }
    
    $html .= '
            <p><strong>E-mail:</strong> ' . htmlspecialchars($orcamento['cliente_email']) . '</p>
            <p><strong>Telefone:</strong> ' . formatarTelefone($orcamento['cliente_telefone']) . '</p>';
    
    if (!empty($orcamento['cliente_cargo'])) {
        $html .= '<p><strong>Cargo:</strong> ' . htmlspecialchars($orcamento['cliente_cargo']) . '</p>';
    }
    
    $html .= '
        </div>
    </div>';
    
    // Mensagem do Cliente
    if (!empty($orcamento['cliente_mensagem'])) {
        $html .= '
    <div class="section">
        <div class="section-title">💬 MENSAGEM DO CLIENTE</div>
        <div class="cliente-info">
            <p>' . nl2br(htmlspecialchars($orcamento['cliente_mensagem'])) . '</p>
        </div>
    </div>';
    }
    
    // Produtos Solicitados
    $html .= '
    <div class="section">
        <div class="section-title">📦 PRODUTOS SOLICITADOS</div>
        <table>
            <thead>
                <tr>
                    <th width="15%">SKU</th>
                    <th width="35%">Produto</th>
                    <th width="15%">Série</th>
                    <th width="20%">Especificações</th>
                    <th width="8%" style="text-align: center;">Qtd</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($itens as $item) {
        $specs = '';
        if (!empty($item['diametro_camera'])) {
            $specs .= 'Ø ' . formatarNumero($item['diametro_camera']) . 'mm';
        }
        if (!empty($item['comprimento_cabo'])) {
            if ($specs) $specs .= ' | ';
            $specs .= formatarNumero($item['comprimento_cabo']) . 'm';
        }
        
        $html .= '
                <tr>
                    <td><span class="produto-sku">' . htmlspecialchars($item['sku']) . '</span></td>
                    <td>
                        <div class="produto-nome">' . htmlspecialchars($item['nome']) . '</div>';
        
        if (!empty($item['observacoes'])) {
            $html .= '<div class="produto-obs">Obs: ' . htmlspecialchars($item['observacoes']) . '</div>';
        }
        
        $html .= '
                    </td>
                    <td>' . htmlspecialchars($item['serie_nome'] ?? '-') . '</td>
                    <td><span class="produto-specs">' . $specs . '</span></td>
                    <td style="text-align: center; font-weight: bold;">' . $item['quantidade'] . '</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="totais">
            <p><strong>Total de Produtos Diferentes:</strong> ' . $total_produtos . '</p>
            <p><strong>Total de Unidades:</strong> ' . $total_unidades . '</p>
        </div>
    </div>';
    
    // Observações Importantes
    $html .= '
    <div class="obs-importante">
        <h4>⚠️ OBSERVAÇÕES IMPORTANTES</h4>
        <p>
            • Este é um orçamento preliminar e não constitui uma proposta formal.<br>
            • Os valores e prazos serão informados pela nossa equipe comercial.<br>
            • A disponibilidade dos produtos está sujeita a confirmação.<br>
            • Validade do orçamento: 30 dias corridos a partir da data de emissão.
        </p>
    </div>';
    
    // Rodapé
    $html .= '
    <div class="footer">
        <p><strong>ENDALL INSPEÇÕES - Especialistas em Equipamentos de Inspeção</strong></p>
        <p>
            ' . EMPRESA_ENDERECO . '<br>
            Telefone: ' . EMPRESA_TELEFONE . ' | WhatsApp: ' . formatarTelefone(EMPRESA_WHATSAPP) . '<br>
            E-mail: ' . EMPRESA_EMAIL . ' | Site: ' . EMPRESA_SITE . '
        </p>
        <p style="margin-top: 15px; font-size: 8pt;">
            Documento gerado automaticamente pelo Sistema de Vendas Endall em ' . date('d/m/Y H:i:s') . '
        </p>
    </div>
    
</body>
</html>';
    
    return $html;
}
?>
