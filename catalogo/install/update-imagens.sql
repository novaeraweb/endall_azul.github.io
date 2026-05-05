-- =============================================
-- ENDALL INSPEÇÕES - Update de Imagens dos Produtos
-- =============================================
-- Atualiza o campo `imagens` (JSON) de cada produto
-- Aponta para arquivos locais em uploads/produtos/<SKU>/
-- 
-- USO:
--   mysql -u<usuario> -p<senha> endall_vendas < update-imagens.sql
-- =============================================

USE endall_vendas;

-- Série Realta (3 produtos)
UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/MV6-1/01-principal.webp',
    'uploads/produtos/MV6-1/02-frontal.webp',
    'uploads/produtos/MV6-1/03-lateral-direita.webp',
    'uploads/produtos/MV6-1/04-portas.webp'
) WHERE sku = 'MV6-1';

UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/MV3-5/01-principal.webp',
    'uploads/produtos/MV3-5/02-probe-close.webp',
    'uploads/produtos/MV3-5/03-iluminacao.webp',
    'uploads/produtos/MV3-5/04-vista-esquerda.webp'
) WHERE sku = 'MV3-5';

UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/MV2-10/01-principal.webp',
    'uploads/produtos/MV2-10/02-vista-traseira.webp',
    'uploads/produtos/MV2-10/03-bateria.webp',
    'uploads/produtos/MV2-10/04-frontal.webp'
) WHERE sku = 'MV2-10';

-- Série P+ (3 produtos)
UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/P39-10/01-principal.webp',
    'uploads/produtos/P39-10/02-angulo.webp',
    'uploads/produtos/P39-10/03-monitor-destacado.webp',
    'uploads/produtos/P39-10/04-articulacao.webp'
) WHERE sku = 'P39-10';

UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/P60-3/01-principal.webp',
    'uploads/produtos/P60-3/02-handle-lateral.webp',
    'uploads/produtos/P60-3/03-conexao.webp',
    'uploads/produtos/P60-3/04-portas.webp'
) WHERE sku = 'P60-3';

UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/P25-15/01-principal.webp',
    'uploads/produtos/P25-15/02-case-fechado.webp',
    'uploads/produtos/P25-15/03-articulacao.webp',
    'uploads/produtos/P25-15/04-sistema.webp'
) WHERE sku = 'P25-15';

-- Série G (2 produtos)
UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/G40-5/01-principal.webp',
    'uploads/produtos/G40-5/02-angulo.webp',
    'uploads/produtos/G40-5/03-conector.webp',
    'uploads/produtos/G40-5/04-acessorios.webp',
    'uploads/produtos/G40-5/05-detalhe.webp'
) WHERE sku = 'G40-5';

UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/G55-8/01-principal.webp',
    'uploads/produtos/G55-8/02-mount.webp',
    'uploads/produtos/G55-8/03-case.webp',
    'uploads/produtos/G55-8/04-360.webp',
    'uploads/produtos/G55-8/05-mosaic.webp'
) WHERE sku = 'G55-8';

-- Série Specialty (2 produtos)
UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/SP-UV35/01-principal.webp',
    'uploads/produtos/SP-UV35/02-articulacao.webp'
) WHERE sku = 'SP-UV35';

UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/SP-3D60/01-principal.webp',
    'uploads/produtos/SP-3D60/02-probe.webp'
) WHERE sku = 'SP-3D60';

-- Série P Legacy (2 produtos)
UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/PL35-6/01-principal.webp',
    'uploads/produtos/PL35-6/02-monitor.webp'
) WHERE sku = 'PL35-6';

UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/PL50-4/01-principal.webp',
    'uploads/produtos/PL50-4/02-angulo.webp'
) WHERE sku = 'PL50-4';

-- Série B+ Portable Legacy (1 produto)
UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/BP28-2/01-principal.webp',
    'uploads/produtos/BP28-2/02-case.webp'
) WHERE sku = 'BP28-2';

-- Série N Legacy (1 produto)
UPDATE produtos SET imagens = JSON_ARRAY(
    'uploads/produtos/NL22-3/01-principal.webp',
    'uploads/produtos/NL22-3/02-handle.webp'
) WHERE sku = 'NL22-3';

-- Verificação
SELECT 
    sku, 
    nome, 
    JSON_LENGTH(imagens) AS total_imagens,
    JSON_EXTRACT(imagens, '$[0]') AS primeira_imagem
FROM produtos 
ORDER BY serie_id, sku;
