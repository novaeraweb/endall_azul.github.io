<?php
/**
 * Teste rápido - Verificar URLs
 */

define('SISTEMA_ENDALL', true);
define('ENDALL_APP', true);

require_once __DIR__ . '/includes/config.php';

echo "<h1>Teste de URLs</h1>";
echo "<pre>";
echo "SITE_URL: " . SITE_URL . "\n";
echo "URL_ASSETS: " . URL_ASSETS . "\n";
echo "ASSETS_VERSION: " . ASSETS_VERSION . "\n";
echo "\n";
echo "URL do carrinho.js: " . URL_ASSETS . "/js/carrinho.js?v=" . ASSETS_VERSION . "\n";
echo "\n";
echo "Caminho absoluto do arquivo: " . __DIR__ . "/assets/js/carrinho.js\n";
echo "Arquivo existe? " . (file_exists(__DIR__ . "/assets/js/carrinho.js") ? "SIM" : "NÃO") . "\n";
echo "</pre>";

echo "<h2>Teste de Carregamento</h2>";
echo "<p>Abra o console (F12) e veja se o arquivo carrega:</p>";
echo '<script src="' . URL_ASSETS . '/js/carrinho.js?v=' . ASSETS_VERSION . '"></script>';
echo '<script>
setTimeout(function() {
    if (typeof Carrinho !== "undefined") {
        document.write("<p style=\"color: green; font-weight: bold;\">✅ Carrinho carregado com sucesso!</p>");
    } else {
        document.write("<p style=\"color: red; font-weight: bold;\">❌ Carrinho NÃO foi carregado!</p>");
    }
}, 1000);
</script>';
?>
