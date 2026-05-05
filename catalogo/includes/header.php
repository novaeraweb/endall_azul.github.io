<?php
/**
 * ENDALL INSPEÇÕES - Header do catálogo padronizado com o site principal.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

$csrf_token = gerarCSRFToken();
$pagina_atual = basename($_SERVER['PHP_SELF'], '.php');
$page_title_base = isset($page_title) ? $page_title . ' | ' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="description" content="<?= $page_description ?? 'Catálogo completo de boroscópios Yateks da Endall Inspeções. Consulte equipamentos para venda e solicite seu orçamento.' ?>">
    <meta name="keywords" content="boroscópio, inspeção industrial, Yateks, catálogo, Endall, venda de equipamentos">
    <meta name="author" content="Endall Inspeções">

    <meta property="og:title" content="<?= $page_title ?? 'Endall Inspeções - Catálogo de Equipamentos para Venda' ?>">
    <meta property="og:description" content="<?= $page_description ?? 'Catálogo de equipamentos para venda da Endall Inspeções' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= SITE_URL ?>">

    <title><?= $page_title_base ?>Endall Inspeções - Equipamentos para Venda</title>

    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css?v=<?= ASSETS_VERSION ?>">
    <link rel="stylesheet" href="../assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="<?= URL_ASSETS ?>/css/style.css?v=<?= ASSETS_VERSION ?>">

    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>?v=<?= ASSETS_VERSION ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <meta name="csrf-token" content="<?= $csrf_token ?>">
</head>
<body>

<header id="header">
    <div class="top">
        <div class="top-inner">
            <a href="mailto:contato@endall.com.br" target="_blank" rel="noopener noreferrer">
                <img src="../images/icon-mail-br.svg" alt="Email da Endall Inspeções">
                contato@endall.com.br
            </a>
            <a href="tel:1931320658" class="nophone" target="_blank" rel="noopener noreferrer">
                <img src="../images/icon-phone-br.svg" alt="Telefone da Endall Inspeções">
                (19) 3132-0658
            </a>
        </div>
    </div>

    <div class="inner">
        <a class="logo" href="../index.php">
            <img src="../images/logo.png" alt="Logo Endall Inspeções e Serviços" width="200" height="66">
        </a>

        <nav id="nav">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../endall-inspecoes-servicos.php">Sobre Nós</a></li>
                <li>
                    <a href="../servicos-endall-inspecoes.php">Serviços</a>
                    <ul>
                        <li><a href="../servicos/endoscopia-industrial.php">Endoscopia/Videoscopia Industrial</a></li>
                        <li><a href="../servicos/video-inspecao-rede-efluentes.php">Vídeo Inspeção em Rede de Efluentes</a></li>
                        <li><a href="../servicos/boroscopia-turbinas.php">Boroscopia em Turbinas</a></li>
                        <li><a href="../servicos/boroscopia-redutores.php">Boroscopia em Redutores</a></li>
                        <li><a href="../servicos/endoscopia-tubulacoes-sanitarias.php">Endoscopia de Tubulações Sanitárias</a></li>
                        <li><a href="../servicos/ensaio-liquido-penetrante.php">Ensaio por Líquido Penetrante</a></li>
                        <li><a href="../servicos/ensaio-particulas-magneticas.php">Ensaio por Partículas Magnéticas</a></li>
                        <li><a href="../servicos/ensaio-ultrassom.php">Ensaio por Ultrassom</a></li>
                        <li><a href="../servicos/inspecao-fiscalizacao-fabricacao.php">Inspeção / Fiscalização de Fabricação</a></li>
                        <li><a href="../servicos/nr-13.php">NR-13</a></li>
                        <li><a href="../servicos/documentacao-em-soldagem.php">Documentação em Soldagem</a></li>
                    </ul>
                </li>
                <li>
                    <a href="../locacao-equipamentos-boroscopia-endoscopia.php">Equipamentos</a>
                    <ul>
                        <li><a href="../locacao-equipamentos-boroscopia-endoscopia.php">Locação</a></li>
                        <li><a href="index.php">Venda</a></li>
                    </ul>
                </li>
                <li>
                    <a href="orcamento.php" class="btn-carrinho-header" id="carrinhoBadge">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="label-orcamento">Orçamento</span>
                        <span class="carrinho-count" id="carrinhoCount">0</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
