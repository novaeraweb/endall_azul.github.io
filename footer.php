<?php
$url_footer = substr($_SERVER['REQUEST_URI'] ?? '', strpos($_SERVER['REQUEST_URI'] ?? '/', '/') + 1);
$isSubdir = strpos($url_footer, 'servicos/') !== false || strpos($url_footer, 'equipamentos/') !== false;

$basePrefix = $isSubdir ? '../' : '';
$serviceTitle = $isSubdir ? 'Serviços' : 'Soluções';
$servicesPrefix = $isSubdir ? '../servicos/' : 'servicos/';
$catalogPrefix = $isSubdir ? '../catalogo/' : 'catalogo/';
$contactPage = $isSubdir ? '../contato-endall-inspecoes.php' : 'contato-endall-inspecoes.php';
$logoNovaEra = $isSubdir ? '../images/nova-era-web.png' : 'images/nova-era-web.png';

$serviceLinks = [
    ['href' => $servicesPrefix . 'endoscopia-industrial.php', 'label' => 'Endoscopia/Videoscopia Industrial'],
    ['href' => $servicesPrefix . 'video-inspecao-rede-efluentes.php', 'label' => 'Video Inspeção em Rede de Efluentes Industriais'],
    ['href' => $servicesPrefix . 'boroscopia-turbinas.php', 'label' => 'Boroscopia em Turbinas'],
    ['href' => $servicesPrefix . 'boroscopia-redutores.php', 'label' => 'Boroscopia em Redutores'],
    ['href' => $servicesPrefix . 'endoscopia-tubulacoes-sanitarias.php', 'label' => 'Endoscopia de Tubulações Sanitárias'],
    ['href' => $servicesPrefix . 'ensaio-liquido-penetrante.php', 'label' => 'Ensaio por Líquidos Penetrantes'],
    ['href' => $servicesPrefix . 'ensaio-particulas-magneticas.php', 'label' => 'Ensaio por Partículas Magnéticas'],
    ['href' => $servicesPrefix . 'ensaio-ultrassom.php', 'label' => 'Ensaio por Ultrassom'],
    ['href' => $servicesPrefix . 'inspecao-fiscalizacao-fabricacao.php', 'label' => 'Inspeção / Fiscalização de Fabricação'],
    ['href' => $servicesPrefix . 'nr-13.php', 'label' => 'NR-13'],
    ['href' => $servicesPrefix . 'documentacao-em-soldagem.php', 'label' => 'Documentação em Soldagem'],
];

$vendaLinks = [
    ['sku' => 'MV6-1', 'label' => 'Realta MV6 - Câmera 6mm HD'],
    ['sku' => 'MV3-5', 'label' => 'Realta MV3 - Câmera 3.9mm Flexível'],
    ['sku' => 'MV2-10', 'label' => 'Realta MV2 - Câmera 2.4mm Longo Alcance'],
    ['sku' => 'P39-10', 'label' => 'P+ 39 - Sistema Profissional 3.9mm'],
    ['sku' => 'P25-15', 'label' => 'P+ 25 - Ultra Longo Alcance 2.5mm'],
    ['sku' => 'SP-UV35', 'label' => 'Specialty UV - Detecção de Vazamentos'],
    ['sku' => 'SP-3D60', 'label' => 'Specialty 3D - Medição Estereoscópica'],
];
?>
<footer id="footer">
    <div class="inner">
        <div class="content">
            <section>
                <h2><?= $serviceTitle ?></h2>
                <ul class="alt">
                    <?php foreach ($serviceLinks as $item): ?>
                        <li><a href="<?= $item['href'] ?>"><?= $item['label'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="footer-vendas">
                <h2>Equipamentos para venda</h2>
                <ul class="alt footer-vendas-lista">
                    <?php foreach ($vendaLinks as $item): ?>
                        <li>
                            <a href="<?= $catalogPrefix ?>produto.php?sku=<?= urlencode($item['sku']) ?>">
                                <?= $item['label'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li class="footer-destaque-link">
                        <a href="<?= $catalogPrefix ?>index.php">Ver catálogo completo de vendas</a>
                    </li>
                </ul>
            </section>

            <section>
                <h2>Entre em contato</h2>
                <ul class="contact-icons">
                    <li class="icon solid fa-map-marker-alt">
                        <a href="https://www.google.com/maps/place/Endall+-+Inspe%C3%A7%C3%B5es+%26+Servi%C3%A7os/@-22.3529299,-47.3912398,17z/data=!3m1!4b1!4m6!3m5!1s0x94c879eaaddcd8ad:0xcc8649101e12b5a3!8m2!3d-22.3529299!4d-47.3886649!16s%2Fg%2F11g03w3_4l?entry=ttu" target="_blank" rel="noopener noreferrer">
                            <address>Rua Silvério Cressoni, 311 <br>Jardim Santa Efigênia | Araras - SP</address>
                        </a>
                    </li>
                    <li class="icon solid fa-envelope">
                        <ul class="ul-footer">
                            <li><a href="mailto:contato@endall.com.br" target="_blank" rel="noopener noreferrer">contato@endall.com.br</a></li>
                            <li><a href="mailto:allan@endall.com.br" target="_blank" rel="noopener noreferrer">allan@endall.com.br</a></li>
                            <li><a href="mailto:fabricia@endall.com.br" target="_blank" rel="noopener noreferrer">fabricia@endall.com.br</a></li>
                        </ul>
                    </li>
                    <li class="icon brands fa-linkedin">
                        <a href="https://www.linkedin.com/company/endall-inspecoes/about/" target="_blank" rel="noopener noreferrer">Endall Inspeções e Serviços</a>
                    </li>
                    <li class="icon solid fa-phone">
                        <ul class="ul-footer">
                            <li><a href="tel:1931320658" target="_blank" rel="noopener noreferrer">(19) 3132-0658</a></li>
                            <li><a href="tel:19999088253" target="_blank" rel="noopener noreferrer">(19) 9-9908-8253</a></li>
                            <li><a href="tel:19971452400" target="_blank" rel="noopener noreferrer">(19) 9-7145-2400</a></li>
                        </ul>
                    </li>
                </ul>
            </section>
        </div>

        <div class="copyright">
            desenvolvido por
            <a href="https://www.novaeraweb.com.br" target="_blank" rel="noopener noreferrer">
                <img src="<?= $logoNovaEra ?>" class="lozad autor" id="pulsar" title="Agencia Nova Era Web" alt="Logo Nova Era Web">
            </a>
        </div>
    </div>
</footer>
<?php require_once __DIR__ . '/whatsapp.php'; ?>
