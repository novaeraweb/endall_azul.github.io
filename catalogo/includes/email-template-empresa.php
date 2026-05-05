<?php
/**
 * Template de e-mail para a empresa
 * Notificação de novo orçamento recebido
 */

if (!defined('ENDALL_APP')) {
    exit('Acesso negado');
}

function getEmailTemplateEmpresa($dados) {
    ob_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Orçamento Recebido - Endall Inspeções</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; background-color: #F4F6F9;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #F4F6F9; padding: 20px;">
        <tr>
            <td align="center">
                <!-- Container Principal -->
                <table cellpadding="0" cellspacing="0" border="0" width="700" style="background-color: #FFFFFF; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2F81DF 0%, #1565C0 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #FFFFFF; margin: 0; font-size: 26px; font-weight: 700;">
                                🔔 NOVO ORÇAMENTO RECEBIDO
                            </h1>
                            <p style="color: #FFFFFF; margin: 10px 0 0 0; font-size: 16px; opacity: 0.95;">
                                Um novo cliente solicitou orçamento no site
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Conteúdo -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <!-- Número do Orçamento -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: linear-gradient(135deg, #0D1B2A 0%, #1B2D3E 100%); border-radius: 8px; padding: 25px; margin: 0 0 25px 0;">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 5px 0; color: #2F81DF; font-size: 14px; font-weight: 600; text-transform: uppercase;">
                                            Número do Orçamento
                                        </p>
                                        <p style="margin: 0; color: #FFFFFF; font-size: 32px; font-weight: 700;">
                                            #<?= htmlspecialchars($dados['numero_orcamento']) ?>
                                        </p>
                                        <p style="margin: 10px 0 0 0; color: #FFFFFF; font-size: 14px; opacity: 0.8;">
                                            Recebido em: <?= date('d/m/Y \à\s H:i') ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Dados do Cliente -->
                            <h3 style="color: #0D1B2A; margin: 0 0 15px 0; font-size: 20px; font-weight: 600; border-bottom: 2px solid #2F81DF; padding-bottom: 10px;">
                                📋 Dados do Cliente
                            </h3>
                            
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #F4F6F9; border-radius: 8px; padding: 20px; margin: 0 0 25px 0;">
                                <tr>
                                    <td width="35%" style="padding: 10px 0; vertical-align: top;">
                                        <strong style="color: #6B7280; font-size: 13px; text-transform: uppercase;">Nome:</strong>
                                    </td>
                                    <td style="padding: 10px 0;">
                                        <span style="color: #0D1B2A; font-size: 16px; font-weight: 600;"><?= htmlspecialchars($dados['cliente_nome']) ?></span>
                                    </td>
                                </tr>
                                <?php if (!empty($dados['cliente_empresa'])): ?>
                                <tr>
                                    <td style="padding: 10px 0; vertical-align: top;">
                                        <strong style="color: #6B7280; font-size: 13px; text-transform: uppercase;">Empresa:</strong>
                                    </td>
                                    <td style="padding: 10px 0;">
                                        <span style="color: #0D1B2A; font-size: 16px; font-weight: 600;"><?= htmlspecialchars($dados['cliente_empresa']) ?></span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($dados['cliente_cargo'])): ?>
                                <tr>
                                    <td style="padding: 10px 0; vertical-align: top;">
                                        <strong style="color: #6B7280; font-size: 13px; text-transform: uppercase;">Cargo:</strong>
                                    </td>
                                    <td style="padding: 10px 0;">
                                        <span style="color: #0D1B2A; font-size: 15px;"><?= htmlspecialchars($dados['cliente_cargo']) ?></span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding: 10px 0; vertical-align: top;">
                                        <strong style="color: #6B7280; font-size: 13px; text-transform: uppercase;">E-mail:</strong>
                                    </td>
                                    <td style="padding: 10px 0;">
                                        <a href="mailto:<?= htmlspecialchars($dados['cliente_email']) ?>" style="color: #2F81DF; text-decoration: none; font-size: 15px;">
                                            <?= htmlspecialchars($dados['cliente_email']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; vertical-align: top;">
                                        <strong style="color: #6B7280; font-size: 13px; text-transform: uppercase;">Telefone:</strong>
                                    </td>
                                    <td style="padding: 10px 0;">
                                        <a href="tel:<?= preg_replace('/[^0-9]/', '', $dados['cliente_telefone']) ?>" style="color: #2F81DF; text-decoration: none; font-size: 15px;">
                                            <?= htmlspecialchars($dados['cliente_telefone']) ?>
                                        </a>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $dados['cliente_telefone']) ?>" style="display: inline-block; background-color: #25D366; color: #FFFFFF; text-decoration: none; padding: 5px 12px; border-radius: 4px; font-size: 12px; margin-left: 10px;">
                                            WhatsApp
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Mensagem do Cliente -->
                            <?php if (!empty($dados['mensagem'])): ?>
                            <h3 style="color: #0D1B2A; margin: 0 0 15px 0; font-size: 20px; font-weight: 600; border-bottom: 2px solid #2F81DF; padding-bottom: 10px;">
                                💬 Mensagem do Cliente
                            </h3>
                            <div style="background-color: #EEF5FF; border-left: 4px solid #2F81DF; padding: 20px; margin: 0 0 25px 0; border-radius: 0 8px 8px 0;">
                                <p style="margin: 0; color: #374151; font-size: 15px; line-height: 1.6; font-style: italic;">
                                    "<?= nl2br(htmlspecialchars($dados['mensagem'])) ?>"
                                </p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Produtos Solicitados -->
                            <h3 style="color: #0D1B2A; margin: 0 0 15px 0; font-size: 20px; font-weight: 600; border-bottom: 2px solid #2F81DF; padding-bottom: 10px;">
                                🛒 Produtos Solicitados (<?= count($dados['itens']) ?>)
                            </h3>
                            
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; margin: 0 0 25px 0;">
                                <thead>
                                    <tr style="background-color: #0D1B2A;">
                                        <th style="padding: 15px; text-align: left; color: #FFFFFF; font-size: 14px; font-weight: 600;">Produto</th>
                                        <th style="padding: 15px; text-align: center; color: #FFFFFF; font-size: 14px; font-weight: 600;">Qtd.</th>
                                        <th style="padding: 15px; text-align: left; color: #FFFFFF; font-size: 14px; font-weight: 600;">Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dados['itens'] as $index => $item): ?>
                                    <tr style="<?= $index % 2 === 0 ? 'background-color: #F4F6F9;' : 'background-color: #FFFFFF;' ?>">
                                        <td style="padding: 15px; border-bottom: 1px solid #E5E7EB;">
                                            <strong style="color: #0D1B2A; font-size: 15px; display: block; margin-bottom: 5px;">
                                                <?= htmlspecialchars($item['nome'] ?? 'Produto') ?>
                                            </strong>
                                            <span style="color: #6B7280; font-size: 13px;">
                                                SKU: <?= htmlspecialchars($item['sku'] ?? 'N/A') ?> | Série: <?= htmlspecialchars($item['serie_nome'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #E5E7EB;">
                                            <span style="display: inline-block; background-color: #2F81DF; color: #FFFFFF; padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 14px;">
                                                <?= (int)$item['quantidade'] ?>
                                            </span>
                                        </td>
                                        <td style="padding: 15px; border-bottom: 1px solid #E5E7EB;">
                                            <?php if (!empty($item['observacoes'])): ?>
                                                <span style="color: #374151; font-size: 14px;"><?= nl2br(htmlspecialchars($item['observacoes'])) ?></span>
                                            <?php else: ?>
                                                <span style="color: #9CA3AF; font-size: 14px; font-style: italic;">Nenhuma observação</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <!-- Ações Rápidas -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: linear-gradient(135deg, #F4F6F9 0%, #E5E7EB 100%); border-radius: 8px; padding: 25px; margin: 25px 0 0 0;">
                                <tr>
                                    <td>
                                        <h4 style="margin: 0 0 15px 0; color: #0D1B2A; font-size: 16px; font-weight: 600;">
                                            ⚡ Ações Rápidas
                                        </h4>
                                        <p style="margin: 0 0 20px 0; color: #6B7280; font-size: 14px;">
                                            Entre em contato com o cliente o mais rápido possível:
                                        </p>
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td>
                                                    <a href="mailto:<?= htmlspecialchars($dados['cliente_email']) ?>?subject=Proposta Comercial - Orçamento <?= urlencode($dados['numero_orcamento']) ?>" 
                                                       style="display: inline-block; background-color: #0D1B2A; color: #FFFFFF; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 5px 5px 5px 0;">
                                                        📧 Enviar E-mail
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $dados['cliente_telefone']) ?>?text=Olá <?= urlencode($dados['cliente_nome']) ?>! Sou da Endall Inspeções e recebi seu orçamento <?= urlencode($dados['numero_orcamento']) ?>." 
                                                       style="display: inline-block; background-color: #25D366; color: #FFFFFF; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 5px 5px 5px 0;">
                                                        💬 WhatsApp
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="tel:<?= preg_replace('/[^0-9]/', '', $dados['cliente_telefone']) ?>" 
                                                       style="display: inline-block; background-color: #2F81DF; color: #FFFFFF; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 5px 0 5px 0;">
                                                        📞 Ligar
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0D1B2A; padding: 20px; text-align: center;">
                            <p style="margin: 0; color: #FFFFFF; font-size: 14px;">
                                Sistema de Vendas - Endall Inspeções
                            </p>
                            <p style="margin: 10px 0 0 0; color: #9CA3AF; font-size: 12px;">
                                E-mail automático gerado pelo sistema
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php
    return ob_get_clean();
}
?>
