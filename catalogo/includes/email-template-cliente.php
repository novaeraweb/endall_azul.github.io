<?php
/**
 * Template de e-mail para o cliente
 * Confirmação de recebimento do orçamento
 */

if (!defined('ENDALL_APP')) {
    exit('Acesso negado');
}

function getEmailTemplateCliente($dados) {
    ob_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento Recebido - Endall Inspeções</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; background-color: #F4F6F9;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #F4F6F9; padding: 20px;">
        <tr>
            <td align="center">
                <!-- Container Principal -->
                <table cellpadding="0" cellspacing="0" border="0" width="600" style="background-color: #FFFFFF; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0D1B2A 0%, #1B2D3E 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #FFFFFF; margin: 0; font-size: 28px; font-weight: 700;">
                                ENDALL INSPEÇÕES
                            </h1>
                            <p style="color: #2F81DF; margin: 10px 0 0 0; font-size: 14px; font-weight: 600;">
                                SOLUÇÕES EM INSPEÇÃO VISUAL
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Conteúdo -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <!-- Título -->
                            <h2 style="color: #0D1B2A; margin: 0 0 20px 0; font-size: 24px;">
                                Olá, <?= htmlspecialchars($dados['cliente_nome']) ?>!
                            </h2>
                            
                            <!-- Mensagem -->
                            <p style="color: #374151; line-height: 1.6; margin: 0 0 20px 0; font-size: 16px;">
                                Recebemos sua solicitação de orçamento com sucesso! Nossa equipe já está analisando os produtos selecionados e em breve entraremos em contato com uma proposta personalizada.
                            </p>
                            
                            <!-- Box de Informação -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #F4F6F9; border-radius: 8px; padding: 20px; margin: 20px 0;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #6B7280; font-size: 14px; font-weight: 600;">
                                            NÚMERO DO ORÇAMENTO
                                        </p>
                                        <p style="margin: 0; color: #0D1B2A; font-size: 24px; font-weight: 700;">
                                            #<?= htmlspecialchars($dados['numero_orcamento']) ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Detalhes da Solicitação -->
                            <h3 style="color: #0D1B2A; margin: 30px 0 15px 0; font-size: 18px; font-weight: 600;">
                                Detalhes da sua solicitação:
                            </h3>
                            
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-top: 2px solid #E5E7EB;">
                                <tr>
                                    <td style="padding: 15px 0; border-bottom: 1px solid #E5E7EB;">
                                        <strong style="color: #6B7280; font-size: 14px;">Nome:</strong><br>
                                        <span style="color: #0D1B2A; font-size: 15px;"><?= htmlspecialchars($dados['cliente_nome']) ?></span>
                                    </td>
                                </tr>
                                <?php if (!empty($dados['cliente_empresa'])): ?>
                                <tr>
                                    <td style="padding: 15px 0; border-bottom: 1px solid #E5E7EB;">
                                        <strong style="color: #6B7280; font-size: 14px;">Empresa:</strong><br>
                                        <span style="color: #0D1B2A; font-size: 15px;"><?= htmlspecialchars($dados['cliente_empresa']) ?></span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding: 15px 0; border-bottom: 1px solid #E5E7EB;">
                                        <strong style="color: #6B7280; font-size: 14px;">E-mail:</strong><br>
                                        <span style="color: #0D1B2A; font-size: 15px;"><?= htmlspecialchars($dados['cliente_email']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 0; border-bottom: 1px solid #E5E7EB;">
                                        <strong style="color: #6B7280; font-size: 14px;">Telefone:</strong><br>
                                        <span style="color: #0D1B2A; font-size: 15px;"><?= htmlspecialchars($dados['cliente_telefone']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 0;">
                                        <strong style="color: #6B7280; font-size: 14px;">Produtos Solicitados:</strong><br>
                                        <span style="color: #0D1B2A; font-size: 15px;"><?= count($dados['itens']) ?> item(ns)</span>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Próximos Passos -->
                            <h3 style="color: #0D1B2A; margin: 30px 0 15px 0; font-size: 18px; font-weight: 600;">
                                Próximos passos:
                            </h3>
                            
                            <ul style="color: #374151; line-height: 1.8; padding-left: 20px; margin: 0 0 20px 0;">
                                <li>Nossa equipe comercial analisará sua solicitação</li>
                                <li>Você receberá uma proposta personalizada em até 24 horas úteis</li>
                                <li>Caso tenha dúvidas, entre em contato conosco</li>
                            </ul>
                            
                            <!-- Botões de Ação -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="https://wa.me/<?= WHATSAPP ?>?text=Olá! Gostaria de falar sobre o orçamento <?= urlencode($dados['numero_orcamento']) ?>" 
                                           style="display: inline-block; background-color: #25D366; color: #FFFFFF; text-decoration: none; padding: 15px 30px; border-radius: 6px; font-weight: 600; font-size: 16px; margin: 5px;">
                                            💬 Falar no WhatsApp
                                        </a>
                                        <a href="tel:<?= preg_replace('/[^0-9]/', '', TELEFONE) ?>" 
                                           style="display: inline-block; background-color: #2F81DF; color: #FFFFFF; text-decoration: none; padding: 15px 30px; border-radius: 6px; font-weight: 600; font-size: 16px; margin: 5px;">
                                            📞 Ligar Agora
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F4F6F9; padding: 30px; text-align: center; border-top: 1px solid #E5E7EB;">
                            <p style="margin: 0 0 10px 0; color: #0D1B2A; font-weight: 600; font-size: 16px;">
                                <?= EMPRESA_NOME ?>
                            </p>
                            <p style="margin: 0 0 5px 0; color: #6B7280; font-size: 14px;">
                                <?= EMPRESA_ENDERECO ?>
                            </p>
                            <p style="margin: 0 0 5px 0; color: #6B7280; font-size: 14px;">
                                📧 <?= EMPRESA_EMAIL ?> | 📞 <?= TELEFONE ?>
                            </p>
                            <p style="margin: 15px 0 0 0;">
                                <a href="<?= EMPRESA_SITE ?>" style="color: #2F81DF; text-decoration: none; font-weight: 600;">
                                    www.endall.com.br
                                </a>
                            </p>
                            
                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #E5E7EB;">
                                <p style="margin: 0; color: #9CA3AF; font-size: 12px;">
                                    Este é um e-mail automático. Por favor, não responda diretamente a esta mensagem.<br>
                                    Para entrar em contato, utilize nossos canais de atendimento acima.
                                </p>
                            </div>
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
