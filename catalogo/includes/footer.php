</main>
<!-- Fim do Conteúdo Principal -->

<?php
/**
 * Reaproveita o footer do site principal para manter consistência visual.
 *
 * O footer principal possui variações por contexto com links relativos.
 * Para páginas dentro de /catalogo, usamos a variação de /equipamentos,
 * pois ela já aponta os caminhos com ../, que são compatíveis com este módulo.
 */
$currentRequestUri = $_SERVER['REQUEST_URI'] ?? '';
$currentWorkingDir = getcwd();

$_SERVER['REQUEST_URI'] = '/equipamentos/catalogo-footer';
chdir(dirname(__DIR__, 2));

ob_start();
include __DIR__ . '/../../footer.php';
$footerHtml = ob_get_clean();

// Padronizar título do bloco de serviços para 'Soluções' (igual ao home)
$footerHtml = preg_replace(
    '#(<section>\s*<h2>)Serviços(</h2>)#u',
    '$1Soluções$2',
    $footerHtml,
    1
);

echo $footerHtml;

$_SERVER['REQUEST_URI'] = $currentRequestUri;
if ($currentWorkingDir) {
    chdir($currentWorkingDir);
}
?>

<!-- JavaScript do site principal para navegação/dropdowns do header -->
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/jquery.dropotron.min.js"></script>
<script src="../assets/js/browser.min.js"></script>
<script src="../assets/js/breakpoints.min.js"></script>
<script src="../assets/js/util.js"></script>
<script src="../assets/js/main.js"></script>

<!-- JavaScript do catálogo -->
<script src="<?= URL_ASSETS ?>/js/main.js?v=<?= ASSETS_VERSION ?>"></script>
<script src="<?= URL_ASSETS ?>/js/carrinho.js?v=<?= ASSETS_VERSION ?>"></script>

<!-- JavaScript Adicional (se definido) -->
<?php if (isset($additional_js)): ?>
    <?php foreach ($additional_js as $js): ?>
        <script src="<?= $js ?>?v=<?= ASSETS_VERSION ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Script Inline (se definido) -->
<?php if (isset($inline_js)): ?>
    <script>
        <?= $inline_js ?>
    </script>
<?php endif; ?>

<!-- Inicializar Carrinho ao Carregar Página -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Carrinho !== 'undefined') {
            Carrinho.atualizarContador();
        }

        window.addEventListener('produtoAdicionado', function() {
            if (typeof mostrarToast === 'function') {
                mostrarToast('Produto adicionado ao orçamento!', 'success');
            }
        });
    });
</script>

</body>
</html>
