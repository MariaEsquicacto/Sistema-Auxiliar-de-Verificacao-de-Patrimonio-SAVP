-- phpMyAdmin SQL Dump
-- version 5.2.2deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 01/10/2025 às 18:47
-- Versão do servidor: 11.8.2-MariaDB-1 from Debian
-- Versão do PHP: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `enzo-zanardi`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `patrimonios`
--

CREATE TABLE `patrimonios` (
  `id_patrimonio` int(11) NOT NULL,
  `patrimonio_del` enum('ativo','inativo') NOT NULL,
  `status` enum('pendente','localizado','fora do lugar','faltando') NOT NULL,
  `patrimonio_img` longtext NOT NULL,
  `denominacao` varchar(100) NOT NULL,
  `ambientes_id_ambientes` int(11) NOT NULL,
  `verificacao_ambiente_id_verificacao` int(11) DEFAULT NULL,
  `num_patrimonio` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `patrimonios`
--

INSERT INTO `patrimonios` (`id_patrimonio`, `patrimonio_del`, `status`, `patrimonio_img`, `denominacao`, `ambientes_id_ambientes`, `verificacao_ambiente_id_verificacao`, `num_patrimonio`, `created_at`) VALUES
(62, 'ativo', 'pendente', '', 'Computador muito incrível', 11, NULL, 695, '2025-10-01 14:36:36'),
(63, 'ativo', 'pendente', '', 'Computador muito incrível', 11, NULL, 677, '2025-10-01 14:36:36'),
(64, 'ativo', 'pendente', '', 'Computador muito incrível', 12, NULL, 701, '2025-10-01 14:36:36'),
(65, 'ativo', 'pendente', '', 'Computador muito incrível', 12, NULL, 707, '2025-10-01 14:36:36'),
(66, 'ativo', 'pendente', '', 'Computador muito incrível', 9, NULL, 685, '2025-10-01 14:36:36'),
(67, 'ativo', 'pendente', '', 'Computador muito incrível', 9, NULL, 688, '2025-10-01 14:36:36'),
(68, 'ativo', 'pendente', '', 'Computador muito incrível', 9, NULL, 693, '2025-10-01 14:36:36'),
(69, 'ativo', 'pendente', '', 'Computador muito incrível', 10, NULL, 680, '2025-10-01 14:36:36'),
(70, 'ativo', 'pendente', '', 'Computador muito incrível', 10, NULL, 702, '2025-10-01 14:36:36'),
(71, 'ativo', 'pendente', '', 'Computador muito incrível', 10, NULL, 690, '2025-10-01 14:36:36'),
(72, 'ativo', 'pendente', '', 'Notebook', 11, NULL, 1001, '2025-10-01 14:36:36'),
(73, 'ativo', 'pendente', '', 'Monitor', 12, NULL, 1002, '2025-10-01 14:36:36'),
(74, 'ativo', 'pendente', '', 'Mouse', 9, NULL, 1003, '2025-10-01 14:36:36'),
(75, 'ativo', 'pendente', '', 'Teclado', 10, NULL, 1004, '2025-10-01 14:36:36'),
(76, 'ativo', 'pendente', '', 'Impressora', 11, NULL, 1005, '2025-10-01 14:36:36'),
(77, 'ativo', 'pendente', '', 'Projetor', 12, NULL, 1006, '2025-10-01 14:36:36'),
(78, 'ativo', 'pendente', '', 'Cadeira de Escritório', 9, NULL, 1007, '2025-10-01 14:36:36'),
(79, 'ativo', 'pendente', '', 'Mesa de Escritório', 10, NULL, 1008, '2025-10-01 14:36:36'),
(80, 'ativo', 'pendente', '', 'Headset', 11, NULL, 1009, '2025-10-01 14:36:36'),
(81, 'ativo', 'pendente', '', 'Webcam', 12, NULL, 1010, '2025-10-01 14:36:36'),
(82, 'ativo', 'pendente', '', 'HD Externo', 9, NULL, 1011, '2025-10-01 14:36:36'),
(83, 'ativo', 'pendente', '', 'SSD Externo', 10, NULL, 1012, '2025-10-01 14:36:36'),
(84, 'ativo', 'pendente', '', 'Smartphone', 11, NULL, 1013, '2025-10-01 14:36:36'),
(85, 'ativo', 'pendente', '', 'Tablet', 12, NULL, 1014, '2025-10-01 14:36:36'),
(86, 'ativo', 'pendente', '', 'Servidor', 9, NULL, 1015, '2025-10-01 14:36:36'),
(87, 'ativo', 'pendente', '', 'Switch de Rede', 10, NULL, 1016, '2025-10-01 14:36:36'),
(88, 'ativo', 'pendente', '', 'Roteador Wi-Fi', 11, NULL, 1017, '2025-10-01 14:36:36'),
(89, 'ativo', 'pendente', '', 'Nobreak', 12, NULL, 1018, '2025-10-01 14:36:36'),
(90, 'ativo', 'pendente', '', 'Estabilizador', 9, NULL, 1019, '2025-10-01 14:36:36'),
(91, 'ativo', 'pendente', '', 'Placa de Vídeo', 10, NULL, 1020, '2025-10-01 14:36:36'),
(92, 'ativo', 'pendente', '', 'Microfone', 11, NULL, 1021, '2025-10-01 14:36:36'),
(93, 'ativo', 'pendente', '', 'Caixa de Som', 12, NULL, 1022, '2025-10-01 14:36:36'),
(94, 'ativo', 'pendente', '', 'Luminária', 9, NULL, 1023, '2025-10-01 14:36:36'),
(95, 'ativo', 'pendente', '', 'TV LED', 10, NULL, 1024, '2025-10-01 14:36:36'),
(96, 'ativo', 'pendente', '', 'Ar Condicionado', 11, NULL, 1025, '2025-10-01 14:36:36'),
(97, 'ativo', 'pendente', '', 'Ventilador', 12, NULL, 1026, '2025-10-01 14:36:36'),
(98, 'ativo', 'pendente', '', 'Notebook Gamer', 9, NULL, 1027, '2025-10-01 14:36:36'),
(99, 'ativo', 'pendente', '', 'Chromebook', 10, NULL, 1028, '2025-10-01 14:36:36'),
(100, 'ativo', 'pendente', '', 'Scanner', 11, NULL, 1029, '2025-10-01 14:36:36'),
(101, 'ativo', 'pendente', '', 'Console de Videogame', 12, NULL, 1030, '2025-10-01 14:36:36');

--
-- Acionadores `patrimonios`
--
DELIMITER $$
CREATE TRIGGER `trg_movimentacao_patrimonio` AFTER UPDATE ON `patrimonios` FOR EACH ROW BEGIN
    IF OLD.ambientes_id_ambientes <> NEW.ambientes_id_ambientes OR OLD.status <> NEW.status THEN
        INSERT INTO `movimentacao_item` (
            `data_hora`,
            `patrimonios_num_patrimonio`,
            `origem`,
            `destino`,
            `usuarios_id_usuario`
        ) VALUES (
            NOW(),
            NEW.num_patrimonio,
            OLD.ambientes_id_ambientes,
            NEW.ambientes_id_ambientes,
            @id_usuario_logado -- A trigger vai pegar o valor daqui
        );
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `patrimonios`
--
ALTER TABLE `patrimonios`
  ADD PRIMARY KEY (`id_patrimonio`),
  ADD KEY `ambientes_id_ambientes` (`ambientes_id_ambientes`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `patrimonios`
--
ALTER TABLE `patrimonios`
  MODIFY `id_patrimonio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `patrimonios`
--
ALTER TABLE `patrimonios`
  ADD CONSTRAINT `patrimonios_ibfk_1` FOREIGN KEY (`ambientes_id_ambientes`) REFERENCES `ambientes` (`id_ambientes`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
