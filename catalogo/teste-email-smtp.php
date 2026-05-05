<?php
/**
 * TESTE DE SMTP — Endall Inspeções
 * 
 * Acesse em: https://seusite.com/catalogo/teste-email-smtp.php?email=seu@email.com
 * Mostra todo o diálogo SMTP em tempo real para diagnóstico.
 * 
 * APAGUE ESTE ARQUIVO APÓS RESOLVER O PROBLEMA.
 */

if (!defined('ENDALL_APP')) {
    define('ENDALL_APP', true);
}

require_once __DIR__ . '/includes/config.php';

// Carrega PHPMailer
$phpmailerPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];
$phpmailerLoaded = false;
foreach ($phpmailerPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $phpmailerLoaded = true;
        echo "✅ PHPMailer carregado de: <code>$path</code><br>\n";
        break;
    }
}

if (!$phpmailerLoaded) {
    die("❌ PHPMailer não encontrado em nenhum dos caminhos esperados.<br>");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$emailDestino = $_GET['email'] ?? 'teste@exemplo.com';

echo "<pre style='background:#f4f6f9;padding:1rem;border-radius:8px;font-family:monospace;font-size:13px;'>";
echo "============================================\n";
echo " TESTE DE SMTP — Endall Inspeções\n";
echo "============================================\n";
echo " Destino:    $emailDestino\n";
echo " Host SMTP:  " . SMTP_HOST . ":" . SMTP_PORT . " (" . SMTP_SECURE . ")\n";
echo " Usuario:    " . SMTP_USER . "\n";
echo " Remetente:  " . SMTP_FROM_EMAIL . "\n";
echo "============================================\n\n";
flush();

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'echo';
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($emailDestino);
    $mail->isHTML(true);
    $mail->Subject = 'Teste SMTP — Endall Inspeções';
    $mail->Body = '
        <div style="font-family: Arial; padding: 20px; background: #f4f6f9;">
            <h2 style="color: #2F81DF;">✅ Teste de SMTP funcionando!</h2>
            <p>Se você recebeu este email, o sistema está configurado corretamente.</p>
            <p><strong>Servidor:</strong> ' . SMTP_HOST . '</p>
            <p><strong>Data:</strong> ' . date('d/m/Y H:i:s') . '</p>
        </div>';
    
    $result = $mail->send();
    echo "\n\n";
    echo "============================================\n";
    if ($result) {
        echo " ✅ EMAIL ENVIADO COM SUCESSO\n";
    } else {
        echo " ❌ FALHA NO ENVIO (send() retornou false)\n";
        echo " ErrorInfo: " . $mail->ErrorInfo . "\n";
    }
    echo "============================================\n";
    
} catch (Exception $e) {
    echo "\n\n";
    echo "============================================\n";
    echo " ❌ EXCEÇÃO CAPTURADA\n";
    echo "============================================\n";
    echo " Mensagem: " . $e->getMessage() . "\n";
    echo " ErrorInfo: " . ($mail->ErrorInfo ?? 'N/A') . "\n";
    echo "============================================\n";
}

echo "</pre>";
echo "<p style='font-family:Arial; padding:1rem; background:#fff3cd; border-left:4px solid #ffc107;'>";
echo "<strong>⚠ Importante:</strong> Após resolver, apague este arquivo (catalogo/teste-email-smtp.php) por segurança.";
echo "</p>";
