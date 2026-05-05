<?php
/**
 * Teste Completo de E-mail SMTP
 * Verifica configurações e envia e-mail de teste
 */

define('ENDALL_APP', true);
require_once __DIR__ . '/includes/config.php';

// Carrega PHPMailer
$phpmailerPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($phpmailerPath)) {
    die("❌ PHPMailer não está instalado! Execute: composer require phpmailer/phpmailer");
}
require_once $phpmailerPath;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de E-mail SMTP - Endall</title>
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
            max-width: 800px;
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
        
        .config-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
        }
        
        .config-box h3 {
            color: #667eea;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        
        .config-item {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .config-item:last-child {
            border-bottom: none;
        }
        
        .config-label {
            font-weight: 600;
            color: #495057;
        }
        
        .config-value {
            color: #212529;
            font-family: 'Courier New', monospace;
        }
        
        .form-box {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #495057;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
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
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .result-box {
            margin-top: 2rem;
            padding: 1.5rem;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .result-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .result-error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .result-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }
        
        .debug-box {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1.5rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Teste de E-mail SMTP</h1>
            <p>Endall Inspeções - Sistema de Orçamentos</p>
        </div>
        
        <div class="content">
            <!-- Configurações Atuais -->
            <div class="config-box">
                <h3>⚙️ Configurações SMTP Atuais</h3>
                <div class="config-item">
                    <div class="config-label">Host:</div>
                    <div class="config-value"><?= SMTP_HOST ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">Porta:</div>
                    <div class="config-value"><?= SMTP_PORT ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">Segurança:</div>
                    <div class="config-value"><?= SMTP_SECURE ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">Usuário:</div>
                    <div class="config-value"><?= SMTP_USER ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">Senha:</div>
                    <div class="config-value"><?= str_repeat('*', strlen(SMTP_PASS)) ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">De (Nome):</div>
                    <div class="config-value"><?= SMTP_FROM_NAME ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">De (E-mail):</div>
                    <div class="config-value"><?= SMTP_FROM_EMAIL ?></div>
                </div>
            </div>
            
            <!-- Formulário de Teste -->
            <form method="POST" class="form-box">
                <h3 style="margin-bottom: 1.5rem; color: #667eea;">📨 Enviar E-mail de Teste</h3>
                
                <div class="form-group">
                    <label>Para (E-mail):</label>
                    <input type="email" name="para" required 
                           value="contato@novaeraweb.com.br"
                           placeholder="destinatario@exemplo.com">
                </div>
                
                <div class="form-group">
                    <label>Assunto:</label>
                    <input type="text" name="assunto" required 
                           value="Teste de E-mail SMTP - Endall"
                           placeholder="Assunto do e-mail">
                </div>
                
                <div class="form-group">
                    <label>Mensagem:</label>
                    <textarea name="mensagem" rows="5" required 
                              placeholder="Digite sua mensagem...">Este é um e-mail de teste do sistema de orçamentos Endall Inspeções.

Se você recebeu este e-mail, significa que o servidor SMTP está configurado corretamente!

Data/Hora: <?= date('d/m/Y H:i:s') ?>
Servidor: <?= SMTP_HOST ?>:<?= SMTP_PORT ?></textarea>
                </div>
                
                <button type="submit" name="enviar" class="btn">
                    🚀 Enviar E-mail de Teste
                </button>
            </form>
            
            <?php
            // Processar envio
            if (isset($_POST['enviar'])) {
                $para = $_POST['para'];
                $assunto = $_POST['assunto'];
                $mensagem = nl2br(htmlspecialchars($_POST['mensagem']));
                
                echo '<div class="result-box result-info">';
                echo '⏳ Tentando enviar e-mail...';
                echo '</div>';
                
                echo '<div class="debug-box">';
                echo "=== DEBUG DO ENVIO ===\n\n";
                
                try {
                    $mail = new PHPMailer(true);
                    
                    // Configurações do servidor
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USER;
                    $mail->Password = SMTP_PASS;
                    $mail->SMTPSecure = SMTP_SECURE;
                    $mail->Port = SMTP_PORT;
                    $mail->CharSet = 'UTF-8';
                    $mail->SMTPDebug = 2; // Debug verbose
                    
                    echo "1️⃣ Conectando ao servidor SMTP...\n";
                    echo "   Host: " . SMTP_HOST . "\n";
                    echo "   Porta: " . SMTP_PORT . "\n";
                    echo "   Segurança: " . SMTP_SECURE . "\n\n";
                    
                    // Remetente
                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                    echo "2️⃣ Remetente configurado: " . SMTP_FROM_EMAIL . "\n\n";
                    
                    // Destinatário
                    $mail->addAddress($para);
                    echo "3️⃣ Destinatário: " . $para . "\n\n";
                    
                    // Conteúdo
                    $mail->isHTML(true);
                    $mail->Subject = $assunto;
                    $mail->Body = "<html><body style='font-family: Arial, sans-serif; padding: 20px;'>" . 
                                  "<h2 style='color: #667eea;'>📧 " . $assunto . "</h2>" .
                                  "<p>" . $mensagem . "</p>" .
                                  "<hr style='margin: 20px 0; border: none; border-top: 1px solid #ddd;'>" .
                                  "<p style='color: #666; font-size: 0.875rem;'>Enviado via Sistema Endall Inspeções</p>" .
                                  "</body></html>";
                    $mail->AltBody = strip_tags($mensagem);
                    
                    echo "4️⃣ Conteúdo preparado\n\n";
                    echo "5️⃣ Enviando...\n\n";
                    
                    // Capturar output do SMTPDebug
                    ob_start();
                    $mail->send();
                    $debug_output = ob_get_clean();
                    
                    echo $debug_output;
                    echo "\n\n✅ E-MAIL ENVIADO COM SUCESSO!\n";
                    
                    echo '</div>';
                    echo '<div class="result-box result-success">';
                    echo '✅ <strong>SUCESSO!</strong> E-mail enviado para: ' . htmlspecialchars($para);
                    echo '</div>';
                    
                } catch (Exception $e) {
                    $debug_output = ob_get_clean();
                    echo $debug_output;
                    echo "\n\n❌ ERRO: " . $mail->ErrorInfo . "\n";
                    echo '</div>';
                    
                    echo '<div class="result-box result-error">';
                    echo '❌ <strong>ERRO!</strong> Não foi possível enviar o e-mail.<br>';
                    echo '<strong>Detalhes:</strong> ' . htmlspecialchars($mail->ErrorInfo);
                    echo '</div>';
                }
            }
            ?>
            
            <!-- Dicas -->
            <div class="config-box" style="border-left-color: #17a2b8; margin-top: 2rem;">
                <h3 style="color: #17a2b8;">💡 Dicas de Troubleshooting</h3>
                <ul style="line-height: 1.8; color: #495057;">
                    <li><strong>Porta 587 (TLS):</strong> Recomendado para a maioria dos servidores</li>
                    <li><strong>Porta 465 (SSL):</strong> Use se a porta 587 não funcionar</li>
                    <li><strong>Autenticação:</strong> Verifique se o usuário e senha estão corretos no painel do Umbler</li>
                    <li><strong>Firewall:</strong> Certifique-se de que as portas SMTP não estão bloqueadas</li>
                    <li><strong>Logs:</strong> Verifique os logs do servidor para erros detalhados</li>
                    <li><strong>SPF/DKIM:</strong> Configure SPF e DKIM no DNS para evitar spam</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
