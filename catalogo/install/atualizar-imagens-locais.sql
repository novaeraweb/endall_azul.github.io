-- =============================================
-- ENDALL INSPEÇÕES - Atualizar Imagens Locais
-- Script para substituir placeholders externos por imagem local
-- =============================================

-- Atualizar todas as imagens para usar o placeholder local
-- Este script substitui as URLs do via.placeholder.com pela imagem local

UPDATE produtos 
SET imagens = '["assets/images/produto-sem-foto.svg"]'
WHERE ativo = 1;

-- Verificar resultado
SELECT 
    id,
    sku,
    nome,
    imagens,
    'Imagem atualizada' as status
FROM produtos
WHERE ativo = 1
ORDER BY serie_id, id;

-- Mensagem final
SELECT CONCAT('✓ ', COUNT(*), ' produtos atualizados com imagem local') as resultado 
FROM produtos 
WHERE ativo = 1 AND imagens LIKE '%assets/images/produto-sem-foto.svg%';
