<?php
/**
 * Script de Envio de E-mail - Endall Inspeções
 * Envia confirmação de orçamento para cliente e notificação para empresa
 * Utiliza PHPMailer (Composer: composer require phpmailer/phpmailer)
 */

if (!defined('ENDALL_APP')) {
    define('ENDALL_APP', true);
}

// Carrega configurações
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Carrega PHPMailer (procura em vários locais possíveis)
$phpmailerPaths = [
    __DIR__ . '/vendor/autoload.php',           // Composer dentro do catálogo
    __DIR__ . '/../vendor/autoload.php',        // Composer no root do site
];
$phpmailerLoaded = false;
foreach ($phpmailerPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $phpmailerLoaded = true;
        break;
    }
}

// Fallback: tenta carregar manualmente do legado /phpmailer
if (!$phpmailerLoaded && file_exists(__DIR__ . '/../phpmailer/class.phpmailer.php')) {
    require_once __DIR__ . '/../phpmailer/class.phpmailer.php';
    require_once __DIR__ . '/../phpmailer/class.smtp.php';
    // Cria aliases para a API moderna usada pelo código
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer') && class_exists('PHPMailer')) {
        class_alias('PHPMailer', 'PHPMailer\\PHPMailer\\PHPMailer');
        class_alias('phpmailerException', 'PHPMailer\\PHPMailer\\Exception');
    }
    $phpmailerLoaded = true;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carrega templates de e-mail
require_once __DIR__ . '/includes/email-template-cliente.php';
require_once __DIR__ . '/includes/email-template-empresa.php';

/**
 * Função para enviar e-mail usando PHP mail() nativo
 * Para produção, recomenda-se instalar PHPMailer via Composer
 * 
 * @param string $para Endereço de e-mail do destinatário
 * @param string $assunto Assunto do e-mail
 * @param string $mensagemHTML Conteúdo HTML do e-mail
 * @param string $de_nome Nome do remetente
 * @param string $de_email E-mail do remetente
 * @param string|null $anexo Caminho do arquivo para anexar
 * @return bool
 */
function enviarEmailNativo($para, $assunto, $mensagemHTML, $de_nome = 'Endall Inspeções', $de_email = '', $anexo = null) {
    if (empty($de_email)) {
        $de_email = EMPRESA_EMAIL;
    }
    
    // Boundary para separar conteúdo
    $boundary = md5(uniqid(time()));
    
    // Headers
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "From: {$de_nome} <{$de_email}>";
    $headers[] = "Reply-To: {$de_email}";
    $headers[] = "X-Mailer: PHP/" . phpversion();
    $headers[] = "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";
    
    // Corpo do e-mail
    $corpo = "--{$boundary}\r\n";
    $corpo .= "Content-Type: text/html; charset=UTF-8\r\n";
    $corpo .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $corpo .= $mensagemHTML . "\r\n\r\n";
    
    // Anexo (se houver)
    if ($anexo && file_exists($anexo)) {
        $nomeArquivo = basename($anexo);
        $conteudoArquivo = chunk_split(base64_encode(file_get_contents($anexo)));
        
        $corpo .= "--{$boundary}\r\n";
        $corpo .= "Content-Type: application/pdf; name=\"{$nomeArquivo}\"\r\n";
        $corpo .= "Content-Transfer-Encoding: base64\r\n";
        $corpo .= "Content-Disposition: attachment; filename=\"{$nomeArquivo}\"\r\n\r\n";
        $corpo .= $conteudoArquivo . "\r\n\r\n";
    }
    
    $corpo .= "--{$boundary}--";
    
    // Envia o e-mail
    $sucesso = mail($para, $assunto, $corpo, implode("\r\n", $headers));
    
    // Log
    if ($sucesso) {
        error_log("E-mail enviado com sucesso para: {$para}");
    } else {
        error_log("Erro ao enviar e-mail para: {$para}");
    }
    
    return $sucesso;
}

/**
 * Função para enviar e-mail usando PHPMailer (SMTP)
 * Requer: composer require phpmailer/phpmailer
 * 
 * @param string $para Endereço de e-mail do destinatário
 * @param string $assunto Assunto do e-mail
 * @param string $mensagemHTML Conteúdo HTML do e-mail
 * @param string $de_nome Nome do remetente
 * @param string $de_email E-mail do remetente
 * @param string|null $anexo Caminho do arquivo para anexar
 * @return bool
 */
function enviarEmailSMTP($para, $assunto, $mensagemHTML, $de_nome = 'Endall Inspeções', $de_email = '', $anexo = null) {
    // Log no início para debug
    error_log("[ENDALL EMAIL] Iniciando envio para: {$para} | Assunto: {$assunto}");
    
    // Verifica se PHPMailer está disponível
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer') && !class_exists('PHPMailer')) {
        error_log("[ENDALL EMAIL] PHPMailer não instalado. Tentando mail() nativa.");
        return enviarEmailNativo($para, $assunto, $mensagemHTML, $de_nome, $de_email, $anexo);
    }
    error_log("[ENDALL EMAIL] PHPMailer disponível. Conectando em " . SMTP_HOST . ":" . SMTP_PORT);
    
    if (empty($de_email)) {
        $de_email = SMTP_FROM_EMAIL;
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Configurações de debug (desabilitar em produção)
        $mail->SMTPDebug = 0;
        
        // Remetente e destinatário
        $mail->setFrom($de_email, $de_nome);
        $mail->addAddress($para);
        $mail->addReplyTo($de_email, $de_nome);
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body = $mensagemHTML;
        $mail->AltBody = strip_tags($mensagemHTML);
        
        // Anexo (se houver)
        if ($anexo && file_exists($anexo)) {
            $mail->addAttachment($anexo, basename($anexo));
        }
        
        // Envia
        $sucesso = $mail->send();
        
        if ($sucesso) {
            error_log("[ENDALL EMAIL] ✅ SMTP send() retornou TRUE para: {$para}");
        } else {
            error_log("[ENDALL EMAIL] ⚠️ SMTP send() retornou FALSE para: {$para} | ErrorInfo: " . ($mail->ErrorInfo ?? 'N/A'));
        }
        
        return $sucesso;
        
    } catch (\Throwable $e) {
        $errInfo = isset($mail) && property_exists($mail, 'ErrorInfo') ? $mail->ErrorInfo : '';
        error_log("[ENDALL EMAIL] ❌ EXCEÇÃO ao enviar para {$para}: " . $e->getMessage() . " | SMTP ErrorInfo: {$errInfo}");
        
        // Fallback para mail() nativo
        error_log("[ENDALL EMAIL] Tentando fallback mail() nativo para: {$para}");
        return enviarEmailNativo($para, $assunto, $mensagemHTML, $de_nome, $de_email, $anexo);
    }
}

/**
 * Função principal para enviar e-mails de orçamento
 * 
 * @param int $orcamentoId ID do orçamento no banco de dados
 * @return array ['sucesso' => bool, 'mensagem' => string, 'detalhes' => array]
 */
function enviarEmailsOrcamento($orcamentoId) {
    // Obter conexão PDO através da classe Database
    $pdo = db()->getConnection();
    
    $resultado = [
        'sucesso' => false,
        'mensagem' => '',
        'detalhes' => [
            'cliente' => false,
            'empresa' => false
        ]
    ];
    
    try {
        // Busca dados do orçamento
        $stmt = $pdo->prepare("
            SELECT * FROM orcamentos 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $orcamentoId]);
        $orcamento = $stmt->fetch();
        
        if (!$orcamento) {
            $resultado['mensagem'] = 'Orçamento não encontrado';
            return $resultado;
        }
        
        // Decodifica itens
        $itens = json_decode($orcamento['itens'], true);
        
        if (empty($itens)) {
            $resultado['mensagem'] = 'Nenhum item no orçamento';
            return $resultado;
        }
        
        // Prepara dados para os templates (usa coluna 'criado_em' do schema, com fallback)
        $dataCriacao = $orcamento['criado_em'] ?? $orcamento['created_at'] ?? date('Y-m-d H:i:s');
        $dados = [
            'numero_orcamento' => $orcamento['numero'],
            'cliente_nome' => $orcamento['cliente_nome'],
            'cliente_empresa' => $orcamento['cliente_empresa'] ?? '',
            'cliente_email' => $orcamento['cliente_email'],
            'cliente_telefone' => $orcamento['cliente_telefone'],
            'cliente_cargo' => $orcamento['cliente_cargo'] ?? '',
            'mensagem' => $orcamento['cliente_mensagem'] ?? $orcamento['mensagem'] ?? '',
            'itens' => $itens,
            'data' => date('d/m/Y H:i', strtotime($dataCriacao))
        ];
        
        // Caminho do PDF (se existir)
        $pdfPath = null;
        if (!empty($orcamento['pdf_path']) && file_exists(__DIR__ . '/' . $orcamento['pdf_path'])) {
            $pdfPath = __DIR__ . '/' . $orcamento['pdf_path'];
        }
        
        // 1. Envia e-mail para o CLIENTE
        $htmlCliente = getEmailTemplateCliente($dados);
        $assuntoCliente = "Orçamento #{$orcamento['numero']} - Endall Inspeções";
        
        $emailClienteEnviado = enviarEmailSMTP(
            $orcamento['cliente_email'],
            $assuntoCliente,
            $htmlCliente,
            SMTP_FROM_NAME,
            SMTP_FROM_EMAIL,
            $pdfPath
        );
        
        $resultado['detalhes']['cliente'] = $emailClienteEnviado;
        
        // 2. Envia e-mail para a EMPRESA
        $htmlEmpresa = getEmailTemplateEmpresa($dados);
        $assuntoEmpresa = "🔔 Novo Orçamento Recebido - #{$orcamento['numero']} - {$orcamento['cliente_nome']}";
        
        $emailEmpresaEnviado = enviarEmailSMTP(
            EMPRESA_EMAIL,
            $assuntoEmpresa,
            $htmlEmpresa,
            'Sistema Endall',
            SMTP_FROM_EMAIL,
            $pdfPath
        );
        
        $resultado['detalhes']['empresa'] = $emailEmpresaEnviado;
        
        // Resultado final
        if ($emailClienteEnviado && $emailEmpresaEnviado) {
            $resultado['sucesso'] = true;
            $resultado['mensagem'] = 'E-mails enviados com sucesso';
            
            // Atualiza status no banco (usa colunas reais do schema: status + atualizado_em)
            try {
                $stmt = $pdo->prepare("
                    UPDATE orcamentos 
                    SET status = 'enviado'
                    WHERE id = :id
                ");
                $stmt->execute(['id' => $orcamentoId]);
            } catch (PDOException $e) {
                error_log('Erro ao atualizar status do orçamento: ' . $e->getMessage());
            }
            
        } elseif ($emailClienteEnviado) {
            $resultado['sucesso'] = true;
            $resultado['mensagem'] = 'E-mail enviado ao cliente. Falha ao notificar empresa.';
        } elseif ($emailEmpresaEnviado) {
            $resultado['sucesso'] = true;
            $resultado['mensagem'] = 'Empresa notificada. Falha ao enviar e-mail ao cliente.';
        } else {
            $resultado['mensagem'] = 'Falha ao enviar e-mails';
        }
        
    } catch (PDOException $e) {
        error_log("Erro ao buscar orçamento: " . $e->getMessage());
        $resultado['mensagem'] = 'Erro ao buscar dados do orçamento';
    }
    
    return $resultado;
}

/**
 * Teste de envio de e-mail
 * Para testar, acesse: enviar-email.php?teste=1&email=seu@email.com
 */
if (isset($_GET['teste']) && !empty($_GET['email'])) {
    $emailTeste = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
    
    if ($emailTeste) {
        $htmlTeste = '
        <html>
        <body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5;">
            <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px;">
                <h1 style="color: #0D1B2A;">Teste de E-mail - Endall Inspeções</h1>
                <p>Este é um e-mail de teste do sistema de vendas.</p>
                <p>Se você recebeu esta mensagem, o sistema de envio de e-mails está funcionando corretamente!</p>
                <p><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</p>
                <hr>
                <p style="color: #999; font-size: 12px;">Sistema de Vendas Endall Inspeções</p>
            </div>
        </body>
        </html>
        ';
        
        $resultado = enviarEmailSMTP(
            $emailTeste,
            'Teste de E-mail - Endall Inspeções',
            $htmlTeste,
            'Endall Inspeções - Teste',
            EMPRESA_EMAIL
        );
        
        if ($resultado) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => "E-mail de teste enviado para {$emailTeste}"
            ]);
        } else {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Falha ao enviar e-mail de teste'
            ]);
        }
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail inválido'
        ]);
    }
    exit;
}

// Se chamado via POST com ID do orçamento
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['orcamento_id'])) {
    $orcamentoId = (int)$_POST['orcamento_id'];
    $resultado = enviarEmailsOrcamento($orcamentoId);
    
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}
?>
