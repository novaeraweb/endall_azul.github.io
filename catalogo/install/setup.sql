-- =============================================
-- ENDALL INSPEÇÕES - Sistema de Vendas
-- Script de Instalação do Banco de Dados
-- =============================================

DROP DATABASE IF EXISTS endall_vendas;
CREATE DATABASE endall_vendas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE endall_vendas;

-- =============================================
-- Tabela: series
-- Armazena as 9 séries de boroscópios Yateks
-- =============================================
CREATE TABLE series (
  id INT PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(50) UNIQUE NOT NULL,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  imagem VARCHAR(255),
  cor VARCHAR(7) DEFAULT '#0D1B2A' COMMENT 'Cor hexadecimal para badge',
  ordem INT DEFAULT 0,
  ativo TINYINT DEFAULT 1,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: produtos
-- Catálogo completo de 111 boroscópios
-- =============================================
CREATE TABLE produtos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  sku VARCHAR(50) UNIQUE NOT NULL,
  nome VARCHAR(200) NOT NULL,
  serie_id INT NOT NULL,
  descricao TEXT,
  descricao_curta VARCHAR(500),
  
  -- Especificações Técnicas
  diametro_camera DECIMAL(4,1) NOT NULL COMMENT 'em mm (1.1 a 6.0)',
  comprimento_cabo DECIMAL(5,1) NOT NULL COMMENT 'em metros (1 a 20)',
  resolucao VARCHAR(50) DEFAULT 'HD',
  direcao_visao VARCHAR(100) COMMENT 'Direta, 90°, 45°, etc.',
  angulo_visao INT COMMENT 'em graus',
  linha_produto VARCHAR(100),
  
  -- Recursos Especiais (JSON array)
  recursos_especiais JSON COMMENT '["UV", "3D", "HD", "4-vias", "Wi-Fi"]',
  
  -- Mídia
  imagens JSON COMMENT '["url1.jpg", "url2.jpg"]',
  video_url VARCHAR(500),
  pdf_url VARCHAR(500),
  
  -- Ficha Técnica Adicional (JSON object)
  ficha_tecnica JSON COMMENT '{"peso": "500g", "bateria": "Li-ion", ...}',
  
  -- Preço e Status
  preco_referencia DECIMAL(10,2),
  ativo TINYINT DEFAULT 1,
  destaque TINYINT DEFAULT 0,
  
  -- Metadados
  visualizacoes INT DEFAULT 0,
  orcamentos_count INT DEFAULT 0,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (serie_id) REFERENCES series(id) ON DELETE CASCADE,
  INDEX idx_sku (sku),
  INDEX idx_serie (serie_id),
  INDEX idx_ativo (ativo),
  INDEX idx_diametro (diametro_camera),
  INDEX idx_comprimento (comprimento_cabo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: orcamentos
-- Histórico de orçamentos solicitados
-- =============================================
CREATE TABLE orcamentos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  numero VARCHAR(20) UNIQUE NOT NULL,
  
  -- Dados do Cliente
  cliente_nome VARCHAR(200) NOT NULL,
  cliente_empresa VARCHAR(200),
  cliente_email VARCHAR(200) NOT NULL,
  cliente_telefone VARCHAR(50) NOT NULL,
  cliente_cargo VARCHAR(100),
  cliente_mensagem TEXT,
  
  -- Itens do Orçamento (JSON array)
  itens JSON NOT NULL COMMENT '[{"produto_id": 1, "sku": "MV6-1", "nome": "...", "quantidade": 1, "observacoes": "..."}]',
  
  -- Status e Controle
  status ENUM('novo','enviado','visualizado','negociando','fechado','cancelado') DEFAULT 'novo',
  pdf_path VARCHAR(500),
  ip_cliente VARCHAR(45),
  user_agent TEXT,
  
  -- Metadados
  total_itens INT DEFAULT 0,
  visualizado_em TIMESTAMP NULL,
  respondido_em TIMESTAMP NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX idx_numero (numero),
  INDEX idx_status (status),
  INDEX idx_email (cliente_email),
  INDEX idx_criado (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: configuracoes
-- Configurações globais do sistema
-- =============================================
CREATE TABLE configuracoes (
  chave VARCHAR(100) PRIMARY KEY,
  valor TEXT,
  descricao VARCHAR(500),
  tipo ENUM('texto','numero','email','telefone','url','json') DEFAULT 'texto',
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: usuarios_admin
-- Usuários do painel administrativo
-- =============================================
CREATE TABLE usuarios_admin (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(200) NOT NULL,
  email VARCHAR(200) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt',
  nivel ENUM('admin','operador') DEFAULT 'operador',
  ativo TINYINT DEFAULT 1,
  ultimo_acesso TIMESTAMP NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: logs_sistema
-- Log de ações importantes
-- =============================================
CREATE TABLE logs_sistema (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NULL,
  acao VARCHAR(100) NOT NULL,
  descricao TEXT,
  ip VARCHAR(45),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (usuario_id) REFERENCES usuarios_admin(id) ON DELETE SET NULL,
  INDEX idx_acao (acao),
  INDEX idx_criado (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DADOS INICIAIS
-- =============================================

-- Inserir as 9 séries Yateks com cores diferentes
INSERT INTO series (slug, nome, descricao, cor, ordem) VALUES
('realta', 'Série Realta', 'Linha mais recente com câmera HD avançada, ideal para inspeções de alta precisão. Tecnologia de ponta para aplicações industriais exigentes.', '#E63946', 1),
('p-plus', 'Série P+', 'Alta performance com múltiplos diâmetros e comprimentos de cabo flexíveis. Versatilidade para diversos tipos de inspeção.', '#457B9D', 2),
('g', 'Série G', 'Linha robusta para ambientes industriais exigentes. Durabilidade e resistência em condições severas.', '#2A9D8F', 3),
('specialty', 'Série Specialty', 'Modelos especiais com recursos únicos: UV, 3D e 4 vias. Soluções específicas para aplicações técnicas complexas.', '#F4A261', 4),
('p-legacy', 'Série P Legacy', 'Linha clássica com excelente custo-benefício. Qualidade comprovada e confiabilidade.', '#264653', 5),
('b-plus-portable-legacy', 'Série B+ Portable Legacy', 'Versão portátil compacta para campo. Mobilidade sem comprometer a qualidade.', '#E76F51', 6),
('b-legacy', 'Série B Legacy', 'Modelo tradicional robusto para inspeções industriais. Confiabilidade testada ao longo dos anos.', '#6D6875', 7),
('n-legacy', 'Série N Legacy', 'Linha econômica para inspeções básicas. Solução acessível com qualidade garantida.', '#B5838D', 8),
('b-plus-benchtop-legacy', 'Série B+ Benchtop Legacy', 'Bancada de alta precisão para laboratórios. Análises detalhadas com estabilidade superior.', '#588157', 9);

-- Configurações iniciais do sistema
INSERT INTO configuracoes (chave, valor, descricao, tipo) VALUES
('empresa_nome', 'Endall Inspeções', 'Nome da empresa', 'texto'),
('empresa_email', 'comercial@endall.com.br', 'E-mail principal para receber orçamentos', 'email'),
('empresa_telefone', '(11) 3456-7890', 'Telefone comercial', 'telefone'),
('empresa_whatsapp', '5511987654321', 'WhatsApp para contato (formato: 55XXXXXXXXXXX)', 'telefone'),
('empresa_endereco', 'Rua Exemplo, 123 - São Paulo - SP', 'Endereço completo', 'texto'),
('site_url', 'https://endall.com.br/vendas', 'URL base do sistema', 'url'),
('smtp_host', 'smtp.gmail.com', 'Servidor SMTP para envio de e-mails', 'texto'),
('smtp_port', '587', 'Porta SMTP', 'numero'),
('smtp_user', 'comercial@endall.com.br', 'Usuário SMTP', 'email'),
('smtp_pass', '', 'Senha SMTP (configurar após instalação)', 'texto'),
('email_logo', 'https://endall.com.br/assets/img/logo-email.png', 'URL do logo para e-mails', 'url'),
('limite_carrinho', '20', 'Limite máximo de itens no carrinho', 'numero'),
('mensagem_boas_vindas', 'Bem-vindo ao catálogo digital da Endall Inspeções', 'Mensagem inicial da página', 'texto');

-- Criar usuário admin padrão (senha: admin123)
INSERT INTO usuarios_admin (nome, email, senha, nivel) VALUES
('Administrador', 'admin@endall.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =============================================
-- PRODUTOS DE EXEMPLO (15 produtos realistas)
-- =============================================

-- Série Realta (3 produtos)
INSERT INTO produtos (sku, nome, serie_id, descricao, descricao_curta, diametro_camera, comprimento_cabo, resolucao, direcao_visao, angulo_visao, linha_produto, recursos_especiais, imagens, video_url, pdf_url, ficha_tecnica, preco_referencia, destaque) VALUES
('MV6-1', 'Realta MV6 - Câmera 6mm HD', 1, 'Boroscópio industrial de alta definição com câmera de 6mm. Ideal para inspeções em motores, turbinas e componentes industriais de grande porte. Sistema de iluminação LED ajustável e gravação de vídeo em Full HD.', 'Boroscópio HD 6mm para inspeções industriais de precisão', 6.0, 1.5, 'Full HD 1920x1080', 'Direta (0°)', 90, 'Linha Industrial', '["HD", "Wi-Fi", "Gravação de Vídeo"]', '["uploads/produtos/MV6-1/01-principal.webp", "uploads/produtos/MV6-1/02-frontal.webp", "uploads/produtos/MV6-1/03-lateral-direita.webp", "uploads/produtos/MV6-1/04-portas.webp"]', 'https://www.youtube.com/watch?v=example', '/pdfs/realta-mv6-1.pdf', '{"peso": "850g", "bateria": "Li-ion 3000mAh", "autonomia": "4 horas", "temperatura": "-20°C a 80°C", "iluminacao": "LED ajustável 6 níveis", "display": "5.0 polegadas touchscreen"}', 12500.00, 1),
('MV3-5', 'Realta MV3 - Câmera 3.9mm Flexível', 1, 'Boroscópio compacto com cabo semi-rígido de alta flexibilidade. Câmera de 3.9mm permite acesso a espaços confinados. Excelente para inspeções automotivas, aeronáuticas e manutenção preventiva.', 'Boroscópio flexível 3.9mm para espaços confinados', 3.9, 5.0, 'HD 1280x720', 'Direta (0°)', 80, 'Linha Automotiva', '["HD", "Cabo Flexível", "USB-C"]', '["uploads/produtos/MV3-5/01-principal.webp", "uploads/produtos/MV3-5/02-probe-close.webp", "uploads/produtos/MV3-5/03-iluminacao.webp", "uploads/produtos/MV3-5/04-vista-esquerda.webp"]', '', '/pdfs/realta-mv3-5.pdf', '{"peso": "450g", "bateria": "Li-ion 2000mAh", "autonomia": "3 horas", "temperatura": "-10°C a 60°C", "iluminacao": "LED 4 níveis", "display": "4.3 polegadas LCD"}', 8900.00, 1),
('MV2-10', 'Realta MV2 - Câmera 2.4mm Longo Alcance', 1, 'Boroscópio de longo alcance com cabo de 10 metros e câmera ultra compacta de 2.4mm. Perfeito para inspeções em tubulações, dutos de ar condicionado e sistemas elétricos. Articulação 4 vias para navegação precisa.', 'Câmera ultrafina 2.4mm com alcance de 10 metros', 2.4, 10.0, 'HD 1280x720', 'Ajustável 4 vias', 70, 'Linha Predial', '["4-vias", "Longo Alcance", "LED Potente"]', '["uploads/produtos/MV2-10/01-principal.webp", "uploads/produtos/MV2-10/02-vista-traseira.webp", "uploads/produtos/MV2-10/03-bateria.webp", "uploads/produtos/MV2-10/04-frontal.webp"]', 'https://www.youtube.com/watch?v=example2', '/pdfs/realta-mv2-10.pdf', '{"peso": "1200g", "bateria": "Li-ion 4000mAh", "autonomia": "5 horas", "temperatura": "-15°C a 70°C", "iluminacao": "LED ultra-brilhante 8 níveis", "display": "5.5 polegadas IPS"}', 15800.00, 1),

-- Série P+ (3 produtos)
('P39-10', 'P+ 39 - Sistema Profissional 3.9mm', 2, 'Sistema de inspeção profissional com câmera de 3.9mm e cabo de 10 metros. Monitor de alta resolução com interface intuitiva. Ideal para indústrias automobilística, aeroespacial e energia. Conectividade Wi-Fi e software de análise incluído.', 'Sistema profissional completo 3.9mm com 10m de cabo', 3.9, 10.0, 'Full HD 1920x1080', 'Direta (0°)', 85, 'Linha Industrial Plus', '["HD", "Wi-Fi", "Software Incluído", "Análise de Imagem"]', '["uploads/produtos/P39-10/01-principal.webp", "uploads/produtos/P39-10/02-angulo.webp", "uploads/produtos/P39-10/03-monitor-destacado.webp", "uploads/produtos/P39-10/04-articulacao.webp"]', '', '/pdfs/p-plus-p39-10.pdf', '{"peso": "2100g", "bateria": "Li-ion 5000mAh", "autonomia": "6 horas", "temperatura": "-20°C a 85°C", "iluminacao": "LED duplo 10 níveis", "display": "7.0 polegadas touchscreen", "software": "Windows/Mac compatível"}', 18900.00, 1),
('P60-3', 'P+ 60 - Câmera 6mm Robusta', 2, 'Boroscópio robusto de 6mm para ambientes industriais severos. Resistente a óleos, graxas e produtos químicos. Cabo reforçado de 3 metros com proteção metálica. Ideal para manutenção de máquinas pesadas e equipamentos offshore.', 'Boroscópio robusto 6mm resistente a ambientes severos', 6.0, 3.0, 'HD 1280x720', 'Lateral 90°', 120, 'Linha Heavy Duty', '["Resistente a Químicos", "Cabo Reforçado", "IP67"]', '["uploads/produtos/P60-3/01-principal.webp", "uploads/produtos/P60-3/02-handle-lateral.webp", "uploads/produtos/P60-3/03-conexao.webp", "uploads/produtos/P60-3/04-portas.webp"]', 'https://www.youtube.com/watch?v=example3', '/pdfs/p-plus-p60-3.pdf', '{"peso": "950g", "bateria": "Li-ion 3500mAh", "autonomia": "4.5 horas", "temperatura": "-25°C a 90°C", "iluminacao": "LED 6 níveis", "display": "4.5 polegadas ruggedized", "certificacao": "IP67"}', 13500.00, 0),
('P25-15', 'P+ 25 - Ultra Longo Alcance 2.5mm', 2, 'Boroscópio de alcance extremo com 15 metros de cabo semi-rígido. Câmera de 2.5mm ultrafina para inspeções em espaços restritos de longa distância. Sistema de orientação com marcação de profundidade. Perfeito para tubulações industriais e sistemas de ventilação.', 'Alcance extremo de 15 metros com câmera 2.5mm', 2.5, 15.0, 'HD 1280x720', 'Direta (0°)', 75, 'Linha Predial Plus', '["Ultra Longo", "Marcação de Profundidade", "Memória 32GB"]', '["uploads/produtos/P25-15/01-principal.webp", "uploads/produtos/P25-15/02-case-fechado.webp", "uploads/produtos/P25-15/03-articulacao.webp", "uploads/produtos/P25-15/04-sistema.webp"]', '', '/pdfs/p-plus-p25-15.pdf', '{"peso": "1800g", "bateria": "Li-ion dual 6000mAh", "autonomia": "8 horas", "temperatura": "-15°C a 75°C", "iluminacao": "LED 8 níveis", "display": "6.0 polegadas", "memoria": "32GB interna + SD card"}', 19500.00, 1),

-- Série G (2 produtos)
('G40-5', 'G-Series 40 - Industrial 4mm', 3, 'Boroscópio industrial da série G com câmera de 4mm e cabo de 5 metros. Construção robusta com classificação IP68. Resistente a quedas e choques. Monitor com tela antirreflexo para uso em ambientes externos. Bateria de longa duração.', 'Boroscópio industrial robusto 4mm IP68', 4.0, 5.0, 'HD 1280x720', 'Direta (0°)', 90, 'Linha Industrial G', '["IP68", "Resistente a Quedas", "Tela Antirreflexo"]', '["uploads/produtos/G40-5/01-principal.webp", "uploads/produtos/G40-5/02-angulo.webp", "uploads/produtos/G40-5/03-conector.webp", "uploads/produtos/G40-5/04-acessorios.webp", "uploads/produtos/G40-5/05-detalhe.webp"]', 'https://www.youtube.com/watch?v=example4', '/pdfs/g-series-g40-5.pdf', '{"peso": "1100g", "bateria": "Li-ion 4500mAh", "autonomia": "7 horas", "temperatura": "-30°C a 95°C", "iluminacao": "LED industrial 8 níveis", "display": "5.0 polegadas ruggedized antirreflexo", "certificacao": "IP68, drop-test 2m"}', 16200.00, 1),
('G55-8', 'G-Series 55 - Heavy Duty 5.5mm', 3, 'Boroscópio heavy-duty com câmera de 5.5mm e cabo reforçado de 8 metros. Desenvolvido para condições extremas em petroquímica, mineração e plantas industriais. Lentes resistentes a arranhões e sistema de limpeza integrado.', 'Heavy-duty 5.5mm para condições extremas', 5.5, 8.0, 'Full HD 1920x1080', 'Lateral 90°', 110, 'Linha Heavy G', '["HD", "Lente Anti-Arranhão", "Sistema de Limpeza"]', '["uploads/produtos/G55-8/01-principal.webp", "uploads/produtos/G55-8/02-mount.webp", "uploads/produtos/G55-8/03-case.webp", "uploads/produtos/G55-8/04-360.webp", "uploads/produtos/G55-8/05-mosaic.webp"]', '', '/pdfs/g-series-g55-8.pdf', '{"peso": "1650g", "bateria": "Li-ion 5500mAh", "autonomia": "6.5 horas", "temperatura": "-35°C a 100°C", "iluminacao": "LED high-power 10 níveis", "display": "6.5 polegadas industrial grade", "certificacao": "IP68, ATEX Zone 2"}', 21700.00, 0),

-- Série Specialty (2 produtos)
('SP-UV35', 'Specialty UV - Detecção de Vazamentos', 4, 'Boroscópio especializado com iluminação UV para detecção de vazamentos e trincas invisíveis. Câmera de 3.5mm com filtros especiais. Essencial para inspeções aeronáuticas, automotivas de alta performance e controle de qualidade. Inclui óculos de proteção UV.', 'Boroscópio UV 3.5mm para detecção de vazamentos', 3.5, 3.0, 'HD 1280x720', 'Direta (0°)', 80, 'Linha UV Specialty', '["UV", "Filtros Especiais", "Óculos UV Inclusos"]', '["uploads/produtos/SP-UV35/01-principal.webp", "uploads/produtos/SP-UV35/02-articulacao.webp"]', 'https://www.youtube.com/watch?v=example5', '/pdfs/specialty-uv35.pdf', '{"peso": "720g", "bateria": "Li-ion 2500mAh", "autonomia": "3.5 horas", "temperatura": "-10°C a 60°C", "iluminacao": "LED UV 365nm + LED branco", "display": "4.5 polegadas high contrast", "acessorios": "Óculos UV, corante fluorescente"}', 14800.00, 1),
('SP-3D60', 'Specialty 3D - Medição Estereoscópica', 4, 'Sistema avançado de inspeção 3D com câmera estereoscópica de 6mm. Permite medição precisa de profundidade, largura e volume de defeitos. Software de análise 3D incluído. Revolucionário para análise de corrosão, trincas e desgaste dimensional.', 'Sistema 3D estereoscópico 6mm com software de medição', 6.0, 2.0, 'Dual HD Stereoscopic', 'Direta (0°)', 95, 'Linha 3D Specialty', '["3D", "Medição Precisa", "Software 3D", "Wi-Fi"]', '["uploads/produtos/SP-3D60/01-principal.webp", "uploads/produtos/SP-3D60/02-probe.webp"]', 'https://www.youtube.com/watch?v=example6', '/pdfs/specialty-3d60.pdf', '{"peso": "1350g", "bateria": "Li-ion 4000mAh", "autonomia": "4 horas", "temperatura": "-15°C a 70°C", "iluminacao": "LED duplo sincronizado", "display": "7.0 polegadas 3D-ready", "software": "Windows análise 3D + relatórios", "precisao": "±0.1mm"}', 28500.00, 1),

-- Série P Legacy (2 produtos)
('PL35-6', 'P Legacy 35 - Clássico 3.5mm', 5, 'Boroscópio clássico da linha Legacy com excelente custo-benefício. Câmera de 3.5mm e cabo de 6 metros. Confiabilidade comprovada em milhares de inspeções. Perfeito para empresas que buscam qualidade sem investimento elevado. Garantia estendida de 3 anos.', 'Boroscópio clássico 3.5mm com ótimo custo-benefício', 3.5, 6.0, 'SD 640x480', 'Direta (0°)', 75, 'Linha Legacy Standard', '["Custo-Benefício", "Garantia 3 Anos"]', '["uploads/produtos/PL35-6/01-principal.webp", "uploads/produtos/PL35-6/02-monitor.webp"]', '', '/pdfs/p-legacy-pl35-6.pdf', '{"peso": "680g", "bateria": "Li-ion 2000mAh", "autonomia": "4 horas", "temperatura": "-10°C a 55°C", "iluminacao": "LED 4 níveis", "display": "3.5 polegadas LCD", "garantia": "3 anos"}', 5900.00, 0),
('PL50-4', 'P Legacy 50 - Robusto 5mm', 5, 'Modelo robusto da linha Legacy com câmera de 5mm. Cabo semi-rígido de 4 metros ideal para inspeções mecânicas gerais. Resistente e durável. Opção econômica para oficinas mecânicas, manutenção predial e serviços gerais. Display colorido de boa visualização.', 'Modelo robusto 5mm econômico para uso geral', 5.0, 4.0, 'SD 640x480', 'Lateral 90°', 100, 'Linha Legacy Pro', '["Durável", "Economia"]', '["uploads/produtos/PL50-4/01-principal.webp", "uploads/produtos/PL50-4/02-angulo.webp"]', '', '/pdfs/p-legacy-pl50-4.pdf', '{"peso": "820g", "bateria": "Li-ion 2500mAh", "autonomia": "5 horas", "temperatura": "-15°C a 65°C", "iluminacao": "LED 5 níveis", "display": "4.0 polegadas TFT", "garantia": "2 anos"}', 7200.00, 0),

-- Série B+ Portable Legacy (1 produto)
('BP28-2', 'B+ Portable - Compacto 2.8mm', 6, 'Boroscópio portátil ultra compacto de 2.8mm com cabo de 2 metros. Cabe no bolso e está sempre pronto para uso. Bateria de longa duração permite inspeções rápidas em campo. Ideal para técnicos móveis, eletricistas e inspetores itinerantes. Estojo rígido incluído.', 'Ultra portátil 2.8mm para inspeções em campo', 2.8, 2.0, 'SD 640x480', 'Direta (0°)', 70, 'Linha Portable', '["Ultra Portátil", "Estojo Incluído", "Pronto para Uso"]', '["uploads/produtos/BP28-2/01-principal.webp", "uploads/produtos/BP28-2/02-case.webp"]', '', '/pdfs/b-plus-portable-bp28-2.pdf', '{"peso": "350g", "bateria": "Li-ion 1500mAh", "autonomia": "3 horas", "temperatura": "-5°C a 50°C", "iluminacao": "LED 3 níveis", "display": "2.7 polegadas LCD", "acessorios": "Estojo rígido, alça de pescoço"}', 3800.00, 0),

-- Série N Legacy (1 produto)
('NL22-3', 'N Legacy - Básico 2.2mm', 8, 'Boroscópio de entrada da linha Legacy. Câmera ultrafina de 2.2mm com cabo de 3 metros. Solução acessível para inspeções básicas e ocasionais. Perfeito para pequenas empresas, estudantes técnicos e uso doméstico avançado. Simples e funcional.', 'Boroscópio básico 2.2mm acessível', 2.2, 3.0, 'SD 480x320', 'Direta (0°)', 65, 'Linha Entry', '["Acessível", "Uso Básico"]', '["uploads/produtos/NL22-3/01-principal.webp", "uploads/produtos/NL22-3/02-handle.webp"]', '', '/pdfs/n-legacy-nl22-3.pdf', '{"peso": "280g", "bateria": "Li-ion 1000mAh", "autonomia": "2.5 horas", "temperatura": "0°C a 45°C", "iluminacao": "LED 2 níveis", "display": "2.4 polegadas LCD", "garantia": "1 ano"}', 2400.00, 0);

-- =============================================
-- VIEWS ÚTEIS
-- =============================================

-- View: Produtos com informações da série
CREATE VIEW v_produtos_completo AS
SELECT 
  p.*,
  s.nome as serie_nome,
  s.slug as serie_slug,
  s.cor as serie_cor
FROM produtos p
INNER JOIN series s ON p.serie_id = s.id
WHERE p.ativo = 1;

-- View: Estatísticas de orçamentos
CREATE VIEW v_stats_orcamentos AS
SELECT 
  COUNT(*) as total,
  COUNT(CASE WHEN status = 'novo' THEN 1 END) as novos,
  COUNT(CASE WHEN status = 'enviado' THEN 1 END) as enviados,
  COUNT(CASE WHEN status = 'fechado' THEN 1 END) as fechados,
  COUNT(CASE WHEN DATE(criado_em) = CURDATE() THEN 1 END) as hoje,
  COUNT(CASE WHEN YEARWEEK(criado_em) = YEARWEEK(NOW()) THEN 1 END) as esta_semana
FROM orcamentos;

-- =============================================
-- TRIGGERS
-- =============================================

-- Trigger: Gerar número único de orçamento antes de inserir
DELIMITER $$
CREATE TRIGGER before_insert_orcamento
BEFORE INSERT ON orcamentos
FOR EACH ROW
BEGIN
  IF NEW.numero IS NULL OR NEW.numero = '' THEN
    SET NEW.numero = CONCAT('ORC', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 9999), 4, '0'));
  END IF;
  
  -- Contar total de itens do JSON
  SET NEW.total_itens = JSON_LENGTH(NEW.itens);
END$$

-- Trigger: Incrementar contador de orçamentos ao adicionar produto
CREATE TRIGGER after_insert_orcamento_produto
AFTER INSERT ON orcamentos
FOR EACH ROW
BEGIN
  DECLARE produto_id INT;
  DECLARE i INT DEFAULT 0;
  DECLARE total INT;
  
  SET total = JSON_LENGTH(NEW.itens);
  
  WHILE i < total DO
    SET produto_id = JSON_UNQUOTE(JSON_EXTRACT(NEW.itens, CONCAT('$[', i, '].produto_id')));
    UPDATE produtos SET orcamentos_count = orcamentos_count + 1 WHERE id = produto_id;
    SET i = i + 1;
  END WHILE;
END$$

DELIMITER ;

-- =============================================
-- FINALIZANDO
-- =============================================

-- Mensagem de sucesso
SELECT '✓ Banco de dados instalado com sucesso!' as mensagem;
SELECT CONCAT('✓ ', COUNT(*), ' séries cadastradas') as series FROM series;
SELECT CONCAT('✓ ', COUNT(*), ' produtos de exemplo cadastrados') as produtos FROM produtos;
SELECT '✓ Pronto para uso!' as status;
