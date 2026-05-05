<?php
/**
 * ENDALL INSPEÇÕES - Página de Orçamento
 * Formulário para solicitar orçamento dos produtos selecionados
 */

// Definir constante do sistema
define('SISTEMA_ENDALL', true);
define('ENDALL_APP', true);

// Carregar configurações
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Configurações da página
$page_title = 'Solicitar Orçamento';
$page_description = 'Finalize seu orçamento e receba uma proposta personalizada da Endall Inspeções.';

// Processar envio do formulário
$orcamento_enviado = false;
$numero_orcamento = '';
$erro = ''; // Inicializar variável de erro

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_orcamento'])) {
    // DEBUG: Modo debug ativado?
    $debug_mode = isset($_GET['debug']) && $_GET['debug'] == '1';
    
    if ($debug_mode) {
        echo '<pre style="background:#2d2d2d;color:#f8f8f2;padding:2rem;margin:2rem;border-radius:8px;overflow-x:auto;">';
        echo "=== DEBUG MODE ATIVADO ===\n\n";
        echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
        echo "isset(\$_POST['enviar_orcamento']): " . (isset($_POST['enviar_orcamento']) ? 'true' : 'false') . "\n\n";
        
        echo "=== \$_POST ===\n";
        print_r($_POST);
        echo "\n";
        
        echo "=== ANÁLISE itens_json ===\n";
        $itens_json_debug = postParam('itens_json');
        echo "isset: " . (isset($_POST['itens_json']) ? 'true' : 'false') . "\n";
        echo "empty: " . (empty($itens_json_debug) ? 'true' : 'false') . "\n";
        echo "length: " . strlen($itens_json_debug) . " caracteres\n";
        echo "formato: base64\n";
        echo "primeiros 100 chars (base64): " . substr($itens_json_debug, 0, 100) . "...\n\n";
        
        if (!empty($itens_json_debug)) {
            // Decodificar base64
            $itens_json_decoded_debug = base64_decode($itens_json_debug);
            echo "base64_decode result: " . ($itens_json_decoded_debug !== false ? 'SUCCESS' : 'FAILED') . "\n";
            
            if ($itens_json_decoded_debug !== false) {
                echo "JSON decodificado (primeiros 200 chars): " . substr($itens_json_decoded_debug, 0, 200) . "...\n\n";
                
                // Decodificar JSON
                $itens_debug = json_decode($itens_json_decoded_debug, true);
                echo "json_decode result: " . ($itens_debug !== null ? 'SUCCESS' : 'FAILED') . "\n";
                echo "json_last_error_msg: " . json_last_error_msg() . "\n";
                echo "empty após decode: " . (empty($itens_debug) ? 'true' : 'false') . "\n";
                echo "count: " . (is_array($itens_debug) ? count($itens_debug) : 'N/A') . "\n\n";
                
                if (!empty($itens_debug)) {
                    echo "CONTEÚDO:\n";
                    print_r($itens_debug);
                }
            }
        }
        
        echo "</pre>";
        exit;
    }
    
    // DEBUG: Mostrar dados recebidos
    error_log('=== ORÇAMENTO RECEBIDO ===');
    error_log('cliente_nome: ' . ($_POST['cliente_nome'] ?? 'vazio'));
    error_log('cliente_email: ' . ($_POST['cliente_email'] ?? 'vazio'));
    error_log('cliente_telefone: ' . ($_POST['cliente_telefone'] ?? 'vazio'));
    error_log('itens_json: ' . (isset($_POST['itens_json']) ? substr($_POST['itens_json'], 0, 100) : 'vazio'));
    error_log('itens_json length: ' . (isset($_POST['itens_json']) ? strlen($_POST['itens_json']) : 0));
    
    // Verificar CSRF
    if (!verificarCSRFToken($_POST['csrf_token'] ?? '')) {
        $erro = 'Token de segurança inválido. Por favor, recarregue a página.';
    } else {
        // Validar dados
        $cliente_nome = postParam('cliente_nome');
        $cliente_empresa = postParam('cliente_empresa');
        $cliente_email = postParam('cliente_email');
        $cliente_telefone = postParam('cliente_telefone');
        $cliente_cargo = postParam('cliente_cargo');
        $cliente_mensagem = postParam('cliente_mensagem');
        $itens_json = postParam('itens_json');
        
        $erros = [];
        
        if (!validarObrigatorio($cliente_nome)) {
            $erros[] = 'Nome completo é obrigatório';
        }
        
        if (!validarEmail($cliente_email)) {
            $erros[] = 'E-mail inválido';
        }
        
        if (!validarTelefone($cliente_telefone)) {
            $erros[] = 'Telefone inválido';
        }
        
        if (empty($itens_json)) {
            $erros[] = 'Nenhum produto selecionado';
            error_log('ERRO: itens_json vazio');
        } else {
            error_log('itens_json recebido (base64): ' . substr($itens_json, 0, 100));
            
            // SOLUÇÃO DEFINITIVA: Decodificar base64 primeiro
            $itens_json_decoded = base64_decode($itens_json);
            
            if ($itens_json_decoded === false) {
                error_log('ERRO: Falha ao decodificar base64');
                $erros[] = 'Erro ao processar dados. Por favor, tente novamente.';
                $itens = [];
            } else {
                error_log('✅ Base64 decodificado com sucesso');
                error_log('JSON decodificado: ' . substr($itens_json_decoded, 0, 200));
                
                // Agora decodificar o JSON
                $itens = json_decode($itens_json_decoded, true);
                
                if (empty($itens) || json_last_error() !== JSON_ERROR_NONE) {
                    error_log('❌ ERRO: json_decode falhou após base64');
                    error_log('json_last_error: ' . json_last_error());
                    error_log('json_last_error_msg: ' . json_last_error_msg());
                    $erros[] = 'Erro ao processar produtos. Dados inválidos.';
                } else {
                    error_log('✅ JSON decodificado com sucesso!');
                    error_log('Total de itens: ' . count($itens));
                }
            }
            
            error_log('itens decodificados: ' . print_r($itens, true));
            error_log('empty($itens): ' . (empty($itens) ? 'true' : 'false'));
            error_log('is_array($itens): ' . (is_array($itens) ? 'true' : 'false'));
            error_log('count($itens): ' . (is_array($itens) ? count($itens) : 'N/A'));
            
            if (empty($itens)) {
                $erros[] = 'Erro ao processar os produtos selecionados. Verifique se os produtos foram adicionados corretamente.';
                error_log('ERRO: itens vazio após todas as tentativas');
            }
        }
        
        if (empty($erros)) {
            // Gerar número do orçamento
            $numero_orcamento = gerarNumeroOrcamento();
            
            // Inserir orçamento no banco
            $sql = "INSERT INTO orcamentos 
                    (numero, cliente_nome, cliente_empresa, cliente_email, cliente_telefone, 
                     cliente_cargo, cliente_mensagem, itens, total_itens, ip_cliente, user_agent, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'novo')";
            
            $ip_cliente = obterIP();
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $total_itens = count($itens);
            $itens_json_final = json_encode($itens, JSON_UNESCAPED_UNICODE);
            
            $resultado = db()->execute($sql, [
                $numero_orcamento,
                $cliente_nome,
                $cliente_empresa,
                $cliente_email,
                $cliente_telefone,
                $cliente_cargo,
                $cliente_mensagem,
                $itens_json_final,
                $total_itens,
                $ip_cliente,
                $user_agent
            ]);
            
            if ($resultado) {
                // O método execute() já retorna o ID do INSERT
                $orcamento_id = $resultado;
                $orcamento_enviado = true;
                
                // Enviar e-mails de confirmação
                try {
                    require_once __DIR__ . '/enviar-email.php';
                    $resultadoEmail = enviarEmailsOrcamento($orcamento_id);
                    
                    if ($resultadoEmail['sucesso']) {
                        registrarLog('email_enviado', "E-mails do orçamento {$numero_orcamento} enviados com sucesso");
                    } else {
                        registrarLog('email_erro', "Erro ao enviar e-mails do orçamento {$numero_orcamento}: " . $resultadoEmail['mensagem']);
                    }
                } catch (Exception $e) {
                    registrarLog('email_erro', "Exceção ao enviar e-mails: " . $e->getMessage());
                }
                
                // Registrar log
                registrarLog('orcamento_enviado', "Orçamento {$numero_orcamento} enviado por {$cliente_nome}");
                
                // Redirecionar para evitar reenvio do formulário
                header('Location: orcamento.php?sucesso=' . $numero_orcamento);
                exit;
            } else {
                $erro = 'Erro ao salvar orçamento. Por favor, tente novamente.';
            }
        } else {
            $erro = implode('<br>', $erros);
        }
    }
}

// Verificar se há mensagem de sucesso via GET
if (isset($_GET['sucesso']) && !empty($_GET['sucesso'])) {
    $orcamento_enviado = true;
    $numero_orcamento = $_GET['sucesso'];
}

// Incluir header
include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<nav class="catalogo-breadcrumb" aria-label="breadcrumb">
    <ol>
        <li><a href="<?= EMPRESA_SITE ?>">Início</a></li>
        <li><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
        <li><a href="<?= urlBase() ?>">Vendas</a></li>
        <li><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
        <li class="breadcrumb-current">Orçamento</li>
    </ol>
</nav>

<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    
    <?php if ($orcamento_enviado): ?>
        
        <!-- MENSAGEM DE SUCESSO -->
        <div style="max-width: 800px; margin: 4rem auto; text-align: center; background: var(--cor-branco); padding: 3rem; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-xl);">
            <div style="width: 80px; height: 80px; background: var(--cor-sucesso); border-radius: 50%; margin: 0 auto 2rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check" style="font-size: 3rem; color: white;"></i>
            </div>
            
            <h1 style="color: var(--cor-sucesso); margin-bottom: 1rem;">Orçamento Enviado com Sucesso!</h1>
            
            <p style="font-size: 1.25rem; color: var(--cor-cinza-escuro); margin-bottom: 2rem;">
                Seu orçamento <strong style="color: var(--cor-secundaria);">#<?= $numero_orcamento ?></strong> foi recebido.
            </p>
            
            <div style="background: var(--cor-cinza-claro); padding: 2rem; border-radius: var(--border-radius-md); margin-bottom: 2rem;">
                <p style="margin: 0; color: var(--cor-cinza-escuro);">
                    <i class="fas fa-envelope" style="color: var(--cor-secundaria); margin-right: 0.5rem;"></i>
                    Em breve você receberá um e-mail com os detalhes do seu orçamento e nossa proposta comercial.
                </p>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?= urlBase('index.php') ?>" class="btn btn-outline">
                    <i class="fas fa-th-large"></i> Voltar ao Catálogo
                </a>
                <a href="<?= EMPRESA_SITE ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Ir para o Site
                </a>
                <a href="https://wa.me/<?= EMPRESA_WHATSAPP ?>" target="_blank" class="btn btn-secondary">
                    <i class="fab fa-whatsapp"></i> Falar no WhatsApp
                </a>
            </div>
        </div>
        
        <script>
        // LIMPAR CARRINHO APÓS ENVIO COM SUCESSO
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Carrinho !== 'undefined') {
                console.log('🗑️ Limpando carrinho após envio bem-sucedido do orçamento...');
                Carrinho.limpar();
                console.log('✅ Carrinho limpo com sucesso!');
                
                // Atualizar contador visualmente
                setTimeout(function() {
                    Carrinho.atualizarContador();
                    console.log('✅ Contador do carrinho atualizado!');
                }, 200);
                
                // Mostrar notificação visual de limpeza
                console.log('%c✨ Carrinho zerado! Pronto para novo orçamento.', 'color: green; font-weight: bold; font-size: 14px;');
            } else {
                console.error('❌ Objeto Carrinho não encontrado ao tentar limpar.');
            }
        });
        </script>
        
    <?php else: ?>
    
    <!-- FORMULÁRIO DE ORÇAMENTO -->
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <h1 style="text-align: center; margin-bottom: 2rem;">
            <i class="fas fa-file-invoice"></i> Solicitar Orçamento
        </h1>
        
        <?php if (!empty($erro) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div style="background: var(--cor-erro); color: white; padding: 1rem 1.5rem; border-radius: var(--border-radius-md); margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <i class="fas fa-exclamation-circle"></i> <?= $erro ?>
                </div>
                <button onclick="window.location.href='orcamento.php'" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
            
            <!-- PRODUTOS SELECIONADOS -->
            <div>
                <div style="background: var(--cor-branco); padding: 2rem; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-md);">
                    <h2 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-shopping-cart" style="color: var(--cor-secundaria);"></i>
                        Produtos Selecionados
                    </h2>
                    
                    <!-- Container dos produtos (preenchido via JavaScript) -->
                    <div id="carrinhoItens"></div>
                </div>
            </div>
            
            <!-- FORMULÁRIO DO CLIENTE -->
            <div>
                <form method="POST" id="formOrcamento" style="background: var(--cor-branco); padding: 2rem; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-md); position: sticky; top: calc(var(--header-height) + 1rem);">
                    
                    <h2 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-user" style="color: var(--cor-secundaria);"></i>
                        Seus Dados
                    </h2>
                    
                    <input type="hidden" name="csrf_token" value="<?= gerarCSRFToken() ?>">
                    <input type="hidden" name="itens_json" id="itensJson" value="">
                    <input type="hidden" name="enviar_orcamento" value="1">
                    
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        
                        <!-- Nome Completo -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--cor-cinza-escuro);">
                                Nome Completo <span style="color: var(--cor-erro);">*</span>
                            </label>
                            <input type="text" 
                                   name="cliente_nome" 
                                   required 
                                   style="width: 100%; padding: 0.75rem; border: 2px solid var(--cor-cinza-medio); border-radius: var(--border-radius-md); font-size: 1rem;"
                                   placeholder="João da Silva">
                        </div>
                        
                        <!-- Empresa -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--cor-cinza-escuro);">
                                Empresa
                            </label>
                            <input type="text" 
                                   name="cliente_empresa" 
                                   style="width: 100%; padding: 0.75rem; border: 2px solid var(--cor-cinza-medio); border-radius: var(--border-radius-md); font-size: 1rem;"
                                   placeholder="Nome da Empresa">
                        </div>
                        
                        <!-- E-mail -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--cor-cinza-escuro);">
                                E-mail <span style="color: var(--cor-erro);">*</span>
                            </label>
                            <input type="email" 
                                   name="cliente_email" 
                                   required 
                                   style="width: 100%; padding: 0.75rem; border: 2px solid var(--cor-cinza-medio); border-radius: var(--border-radius-md); font-size: 1rem;"
                                   placeholder="email@exemplo.com">
                        </div>
                        
                        <!-- Telefone -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--cor-cinza-escuro);">
                                Telefone/WhatsApp <span style="color: var(--cor-erro);">*</span>
                            </label>
                            <input type="tel" 
                                   name="cliente_telefone" 
                                   required 
                                   oninput="mascaraTelefone(this)"
                                   style="width: 100%; padding: 0.75rem; border: 2px solid var(--cor-cinza-medio); border-radius: var(--border-radius-md); font-size: 1rem;"
                                   placeholder="(11) 98765-4321">
                        </div>
                        
                        <!-- Cargo -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--cor-cinza-escuro);">
                                Cargo
                            </label>
                            <input type="text" 
                                   name="cliente_cargo" 
                                   style="width: 100%; padding: 0.75rem; border: 2px solid var(--cor-cinza-medio); border-radius: var(--border-radius-md); font-size: 1rem;"
                                   placeholder="Gerente de Compras">
                        </div>
                        
                        <!-- Mensagem -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--cor-cinza-escuro);">
                                Mensagem/Necessidade Específica
                            </label>
                            <textarea name="cliente_mensagem" 
                                      rows="4"
                                      style="width: 100%; padding: 0.75rem; border: 2px solid var(--cor-cinza-medio); border-radius: var(--border-radius-md); font-size: 1rem; font-family: inherit; resize: vertical;"
                                      placeholder="Descreva suas necessidades, prazo de entrega desejado, condições de pagamento, etc."></textarea>
                        </div>
                        
                        <!-- Botão Enviar -->
                        <button type="submit" name="enviar_orcamento" value="1" class="btn btn-primary btn-lg btn-block" id="btnEnviar" style="margin-top: 1rem;">
                            <i class="fas fa-paper-plane"></i> Enviar Orçamento
                        </button>
                        
                        <p style="font-size: 0.875rem; color: var(--cor-cinza); text-align: center; margin: 0;">
                            <i class="fas fa-lock"></i> Seus dados estão seguros
                        </p>
                    </div>
                    
                </form>
            </div>
            
        </div>
        
    </div>
    
    <?php endif; ?>
    
</div>

<script>
// Renderizar carrinho ao carregar página
document.addEventListener('DOMContentLoaded', function() {
    Carrinho.renderizar('carrinhoItens');
    
    // Verificar se há produtos no carrinho
    if (Carrinho.contar() === 0 && !<?= $orcamento_enviado ? 'true' : 'false' ?>) {
        document.getElementById('carrinhoItens').innerHTML = `
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--cor-cinza); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--cor-cinza-escuro);">Carrinho Vazio</h3>
                <p style="color: var(--cor-cinza); margin-bottom: 1rem;">Adicione produtos ao carrinho para solicitar um orçamento</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-th-large"></i> Ver Catálogo
                </a>
            </div>
        `;
        
        // Desabilitar botão de enviar
        document.getElementById('btnEnviar').disabled = true;
    }
    
    // Antes de enviar, preencher itens_json
    const formOrcamento = document.getElementById('formOrcamento');
    
    if (formOrcamento) {
        formOrcamento.addEventListener('submit', function(e) {
            console.log('=== FORMULÁRIO SENDO ENVIADO ===');
            
            // Função para reabilitar o botão em caso de erro
            const reabilitarBotao = function() {
                const btnEnviar = document.getElementById('btnEnviar');
                if (btnEnviar) {
                    btnEnviar.disabled = false;
                    btnEnviar.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Orçamento';
                    btnEnviar.style.opacity = '1';
                    btnEnviar.style.cursor = 'pointer';
                }
            };
            
            // Verificar se Carrinho existe
            if (typeof Carrinho === 'undefined') {
                console.error('❌ ERRO: Objeto Carrinho não está definido!');
                e.preventDefault();
                alert('ERRO: Sistema de carrinho não carregou. Recarregue a página (Ctrl+F5)');
                reabilitarBotao();
                return false;
            }
            
            console.log('✅ Objeto Carrinho existe');
            
            // Preparar itens
            const itens = Carrinho.prepararParaEnvio();
            console.log('Itens preparados:', itens);
            console.log('Total de itens:', itens.length);
            
            // Verificar se há itens
            if (itens.length === 0) {
                console.error('❌ Carrinho vazio!');
                e.preventDefault();
                alert('Adicione produtos ao carrinho primeiro!');
                reabilitarBotao();
                return false;
            }
            
            console.log('✅ Carrinho contém produtos');
            
            // BLOQUEAR BOTÃO IMEDIATAMENTE ANTES DE QUALQUER VALIDAÇÃO
            const btnEnviar = document.getElementById('btnEnviar');
            if (btnEnviar) {
                btnEnviar.disabled = true;
                btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                btnEnviar.style.opacity = '0.6';
                btnEnviar.style.cursor = 'not-allowed';
                btnEnviar.style.pointerEvents = 'none'; // Prevenir cliques adicionais
            }
            
            // Limpar dados antes de gerar JSON
            const itensLimpos = itens.map(item => ({
                produto_id: parseInt(item.produto_id) || 0,
                sku: String(item.sku || ''),
                nome: String(item.nome || ''),
                serie_nome: String(item.serie_nome || ''),
                quantidade: parseInt(item.quantidade) || 1,
                observacoes: String(item.observacoes || ''),
                diametro_camera: parseFloat(item.diametro_camera) || 0,
                comprimento_cabo: parseFloat(item.comprimento_cabo) || 0
            }));
            
            console.log('Itens limpos:', itensLimpos);
            
            // Gerar JSON
            const itensJson = JSON.stringify(itensLimpos);
            console.log('JSON gerado:', itensJson);
            console.log('Tamanho do JSON:', itensJson.length, 'caracteres');
            
            // SOLUÇÃO DEFINITIVA: Codificar em base64 para evitar problemas de encoding
            const itensJsonBase64 = btoa(unescape(encodeURIComponent(itensJson)));
            console.log('JSON em base64:', itensJsonBase64.substring(0, 100) + '...');
            
            // Preencher campo hidden
            const campoItensJson = document.getElementById('itensJson');
            
            if (!campoItensJson) {
                console.error('❌ ERRO: Campo itensJson não encontrado!');
                e.preventDefault();
                alert('ERRO: Campo do formulário não encontrado!');
                reabilitarBotao();
                return false;
            }
            
            // Enviar em base64 ao invés de JSON direto
            campoItensJson.value = itensJsonBase64;
            console.log('✅ Campo itensJson preenchido!');
            console.log('Valor do campo:', campoItensJson.value.substring(0, 100) + '...');
            console.log('Tamanho final:', campoItensJson.value.length);
            
            // Verificação adicional
            if (campoItensJson.value.length === 0) {
                console.error('❌ ERRO: Campo foi preenchido mas está vazio!');
                e.preventDefault();
                alert('ERRO: Não foi possível preparar os dados. Tente novamente.');
                return false;
            }
            
            console.log('✅ Formulário pronto para envio!');
            console.log('=== FIM DO DEBUG ===');;
            
            // Permitir envio
            return true;
        });
        
        console.log('✅ Event listener do formulário registrado');
    } else {
        console.error('❌ Formulário não encontrado!');
    }
});
</script>

<?php
// Incluir footer
include __DIR__ . '/includes/footer.php';
?>
