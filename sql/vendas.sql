-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18/09/2025 às 02:10
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `vendas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `fone` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `cpf`, `cidade`, `fone`, `estado`) VALUES
(1, 'Lucas Bortoloti', '111.111.111-11', 'Jaraguá do Sul', '99999-9999', 'SC'),
(2, 'Vitor de Souza', '222.222.222-22', 'Joinville', '88888-8888', 'SC'),
(3, 'Luana Menezes', '333.333.333-33', 'Guaramirim ', '77777-7777', 'SC'),
(4, 'Marcelo dos Santos Aveiro', '444.444.444-44', 'Schroeder', '66666-6666', 'SC'),
(5, 'Leticia Pereira', '555.555.555-55', 'Jaraguá do Sul', '55555-5555', 'SC'),
(6, 'Marcos Moretti', '666.666.666-66', 'Jaraguá do Sul', '44444-4444', 'SC'),
(7, 'Bryan De Luca', '777.777.777-77', 'Jaraguá do Sul', '33333-3333', 'SC'),
(8, 'Julia Schneider', '888.888.888-88', 'Guaramirim', '22222-2222', 'SC'),
(9, 'Joel Martínez', '999.999.999-99', 'Joinville', '11111-1111', 'SC'),
(10, 'Luiz Walker', '233.233.233-23', 'Schroeder', '00000-0000', 'SC'),
(11, 'Matheus Koch', '344.344.344-34', 'Jaraguá do Sul', '12121-1212', 'SC'),
(12, 'Vitor González', '556.556.556-56', 'Joinville', '01010-0101', 'SC'),
(13, 'Laura Perez', '778.778.778-78', 'Guaramirim', '20200-2020', 'SC'),
(14, 'Peter Herrera', '889.889.889-89', 'Jaraguá do Sul', '09099-0909', 'SC'),
(15, 'Yuri García', '991.991.991-91', 'Joinville', '71717-7171', 'SC'),
(16, 'Vinicius De Luca', '772.772.772-72', 'Joinville', '14545-4545', 'SC'),
(17, 'Pedro Bortoloti', '000.000.000-00', 'Jaraguá do Sul', '47996637957', 'SC'),
(18, 'Luan Vargas', '459.989.000-45', 'Jaraguá do Sul', '47891881239', 'SC'),
(19, 'Marcos Duval', '111.111.111-11', 'Jaraguá do Sul', '(47) 99888-8988', 'SC');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `preco` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id`, `nome`, `preco`) VALUES
(1, 'PS5', '3800'),
(2, 'Iphone 17', '5000'),
(3, 'Moto Edge 50', '2000'),
(4, 'PC Gamer', '5500'),
(5, 'Smartwatch', '600'),
(6, 'Nintendo Switch', '2000'),
(7, 'Headset Cloud 2', '999'),
(8, 'Controle Xbox', '379'),
(9, 'Xbox Series X', '4000'),
(10, 'G305', '349'),
(11, 'USB - hub', '35'),
(12, 'The Last of Us PS5', '249'),
(13, 'Cadeira Thunderx3', '1099'),
(14, 'Nintendo Swicth Oled', '2350'),
(15, 'Razer Kraken', '199'),
(16, 'Memoria Ram 8gb DDR4', '250'),
(17, 'Controle PS5', '350'),
(18, 'Headset Cloud Stinger', '119'),
(19, 'TV LG 55 4K', '2800');

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda`
--

CREATE TABLE `venda` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `total` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda`
--

INSERT INTO `venda` (`id`, `date`, `cliente_id`, `total`) VALUES
(1, '2025-07-04', 1, '10400'),
(2, '2025-07-07', 2, '6800'),
(3, '2025-07-11', 3, '2600'),
(4, '2025-07-18', 4, '13800'),
(5, '2025-07-23', 5, '5500'),
(6, '2025-07-26', 6, '678'),
(7, '2025-07-30', 7, '5379'),
(8, '2025-08-02', 8, '7000'),
(9, '2025-08-06', 9, '1356'),
(10, '2025-08-11', 10, '8600'),
(11, '2025-08-15', 11, '678'),
(12, '2025-08-18', 12, '4049'),
(13, '2025-08-22', 13, '3200'),
(14, '2025-08-26', 14, '2299'),
(15, '2025-09-01', 15, '13340'),
(16, '2025-09-03', 16, '2549'),
(17, '2025-09-05', 5, '4379'),
(18, '2025-09-08', 17, '4000'),
(19, '2025-09-09', 18, '1079'),
(20, '2025-09-12', 19, '984');

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda_item`
--

CREATE TABLE `venda_item` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` varchar(200) NOT NULL,
  `preco_venda` float NOT NULL,
  `desconto` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda_item`
--

INSERT INTO `venda_item` (`id`, `venda_id`, `produto_id`, `quantidade`, `preco_venda`, `desconto`) VALUES
(1, 3, 3, '1', 2000, 25),
(2, 3, 5, '1', 600, 0),
(5, 5, 4, '1', 5500, 0),
(6, 6, 8, '1', 379, 10),
(7, 6, 7, '1', 299, 0),
(8, 7, 2, '1', 5000, 150),
(9, 7, 8, '1', 379, 0),
(10, 8, 6, '1', 5000, 50),
(11, 8, 2, '1', 379, 0),
(12, 9, 8, '2', 758, 5),
(13, 9, 7, '2', 598, 0),
(15, 11, 8, '1', 379, 0),
(16, 11, 7, '1', 299, 0),
(21, 13, 6, '1', 2000, 0),
(22, 13, 5, '2', 1200, 0),
(23, 14, 3, '1', 2000, 100),
(24, 14, 7, '1', 299, 0),
(25, 16, 14, '1', 2350, 10),
(26, 16, 15, '1', 199, 0),
(30, 12, 12, '1', 249, 0),
(31, 12, 1, '1', 3800, 30),
(35, 18, 9, '1', 4000, 0),
(38, 19, 13, '1', 1099, 20),
(47, 4, 2, '1', 10000, 0),
(48, 4, 1, '1', 3800, 0),
(50, 15, 16, '2', 750, 10),
(51, 15, 9, '1', 4000, 50),
(52, 15, 3, '2', 4000, 100),
(54, 17, 9, '1', 4000, 0),
(55, 17, 8, '1', 379, 0),
(58, 10, 2, '1', 5000, 200),
(59, 10, 1, '1', 3800, 0),
(62, 2, 6, '1', 2000, 100),
(63, 2, 2, '1', 5000, 100),
(64, 1, 2, '1', 5000, 0),
(65, 1, 4, '1', 5500, 100),
(66, 20, 7, '1', 999, 15);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `venda`
--
ALTER TABLE `venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `venda_item`
--
ALTER TABLE `venda_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_item_FK` (`venda_id`),
  ADD KEY `sale_item_FK_1` (`produto_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `venda`
--
ALTER TABLE `venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `venda_item`
--
ALTER TABLE `venda_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `venda`
--
ALTER TABLE `venda`
  ADD CONSTRAINT `venda_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`);

--
-- Restrições para tabelas `venda_item`
--
ALTER TABLE `venda_item`
  ADD CONSTRAINT `sale_item_FK` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`),
  ADD CONSTRAINT `sale_item_FK_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
