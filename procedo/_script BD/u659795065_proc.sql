-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 10.2.1.124:3306
-- Tempo de geração: 16/04/2019 às 02:39
-- Versão do servidor: 10.2.16-MariaDB
-- Versão do PHP: 7.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u659795065_proc`
--

DELIMITER $$
--
-- Funções
--
CREATE DEFINER=`u659795065_admin`@`10.2.1.47` FUNCTION `ACESSO` (`pEMAIL` VARCHAR(90), `pSENHA` VARCHAR(32)) RETURNS INT(11) BEGIN
	DECLARE vEMAIL varchar(90) DEFAULT NULL;
	DECLARE vSENHA varchar(32) DEFAULT NULL;
	DECLARE vSITUACAO tinyint(1) DEFAULT NULL;
	SELECT EMAIL, SENHA, SITUACAO INTO vEMAIL, vSENHA, vSITUACAO FROM USUARIOS WHERE EMAIL = pEMAIL AND SENHA = pSENHA LIMIT 1;
	
	IF ((vEMAIL IS NOT NULL) and (vSENHA IS NOT NULL)) THEN 
		IF (vSITUACAO = 1) THEN
			RETURN 0; -- Sucesso
		ELSE
			RETURN 2; -- Usuário desativado
		END IF;
	ELSE
		RETURN 1; -- Email ou Senha errados
	END IF;
END$$

CREATE DEFINER=`u659795065_admin`@`10.2.1.47` FUNCTION `CADASTRO` (`pNOME` VARCHAR(30), `pEMAIL` VARCHAR(90), `pSENHA` VARCHAR(32), `pSEXO` CHAR(1), `pTELEFONE` VARCHAR(11), `pUF` VARCHAR(30), `pCIDADE` VARCHAR(50)) RETURNS INT(11) BEGIN
	DECLARE vCHECK varchar(100) DEFAULT NULL;
	SELECT NOME INTO vCHECK FROM USUARIOS WHERE NOME = pNOME LIMIT 1;
	IF (vCHECK IS NULL) THEN 
		SET vCHECK = NULL;
		SELECT EMAIL INTO vCHECK FROM USUARIOS WHERE EMAIL = pEMAIL LIMIT 1;
		IF (vCHECK IS NULL) THEN
			SET vCHECK = NULL;
			IF ((pSEXO = 'F') or (pSEXO = 'M')) THEN
				SELECT TELEFONE INTO vCHECK FROM USUARIOS WHERE TELEFONE = pTELEFONE LIMIT 1;
				IF (vCHECK IS NULL) THEN
					SET vCHECK = NULL;
					INSERT INTO USUARIOS VALUES(NULL, pNOME, pEMAIL, pSENHA, pSEXO, pTELEFONE, pUF, pCIDADE, 1);
					RETURN 0; -- Sucesso
				ELSE
					RETURN 4; -- Telefone já cadastrado
				END IF;
			ELSE
				RETURN 3; -- Valor fugiu de F ou M
			END IF;
		ELSE
			RETURN 2; -- Email já cadastrado 
		END IF;
	ELSE
		RETURN 1; -- Nome já cadastrado
	END IF;
END$$

CREATE DEFINER=`u659795065_admin`@`10.2.1.47` FUNCTION `CADASTRO_CLIENTE` (`pNOME` VARCHAR(32), `pEMAIL` VARCHAR(90), `pCPF` VARCHAR(11), `pTELEFONE` VARCHAR(11), `pUF` VARCHAR(30), `pCIDADE` VARCHAR(50), `pOA` TINYINT(1), `pOB` TINYINT(1), `pOC` TINYINT(1), `pOD` TINYINT(1), `pOBS` VARCHAR(200)) RETURNS INT(11) BEGIN
    DECLARE vCHECK varchar(100) DEFAULT NULL;
	SELECT NOME INTO vCHECK FROM CLIENTES WHERE NOME = pNOME LIMIT 1;
	IF (vCHECK IS NULL) THEN 
		SET vCHECK = NULL;
		SELECT EMAIL INTO vCHECK FROM CLIENTES WHERE EMAIL = pEMAIL LIMIT 1;
		IF (vCHECK IS NULL) THEN
			SET vCHECK = NULL;
            SELECT CPF INTO vCHECK FROM CLIENTES WHERE CPF = pCPF LIMIT 1;
			IF (vCHECK IS NULL) THEN
            	SET vCHECK = NULL;
                SELECT TELEFONE INTO vCHECK FROM CLIENTES WHERE TELEFONE = pTELEFONE LIMIT 1;
                IF (vCHECK IS NULL) THEN
                	SET vCHECK = NULL;
                	INSERT INTO ORIGEM VALUES (NULL, pOA, pOB, pOC, pOD);
                    SELECT ID INTO vCHECK FROM ORIGEM ORDER BY ID DESC LIMIT 1;
                    IF (vCHECK IS NOT NULL) THEN
                    	INSERT INTO CLIENTES VALUES (NULL,pNOME, pEMAIL, pCPF, pTELEFONE, pUF, pCIDADE, 1, pOBS, vCHECK);
                        RETURN 0; -- Sucesso
                    END IF;
                ELSE
                	RETURN 4; -- Telefone já cadastrado	
                END IF;
            ELSE
            	RETURN 3; -- CPF já cadastrado
            END IF;
		ELSE
			RETURN 2; -- Email já cadastrado 
		END IF;
	ELSE
		RETURN 1; -- Nome já cadastrado
	END IF;
END$$

CREATE DEFINER=`u659795065_admin`@`10.2.1.47` FUNCTION `VER_EMAIL` (`pEMAIL` VARCHAR(90)) RETURNS VARCHAR(32) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci BEGIN
	DECLARE vEMAIL varchar(90) DEFAULT NULL;
    DECLARE vSENHA varchar(90) DEFAULT NULL;
	SELECT EMAIL, SENHA INTO vEMAIL, vSENHA FROM USUARIOS WHERE EMAIL = pEMAIL LIMIT 1;
	IF (vEMAIL IS NOT NULL) THEN
		RETURN vSENHA; -- Sucesso
	ELSE
		RETURN 1; -- Email Nao Encontrado
	END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `CLIENTES`
--

CREATE TABLE `CLIENTES` (
  `ID` int(11) NOT NULL,
  `NOME` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `EMAIL` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CPF` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TELEFONE` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ESTADO` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CIDADE` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SITUACAO` tinyint(1) NOT NULL DEFAULT 1,
  `OBS` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_OR` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `CLIENTES`
--

INSERT INTO `CLIENTES` (`ID`, `NOME`, `EMAIL`, `CPF`, `TELEFONE`, `ESTADO`, `CIDADE`, `SITUACAO`, `OBS`, `ID_OR`) VALUES
(1, 'joao', 'jao@hotmail.com', '25698547820', '14981505647', 'SP', 'Bauru', 0, 'Sem obs', 2),
(2, 'Melissa', 'melissa@hotmail.com', '65584215201', '14997542103', 'SP', 'Bauru', 1, 'Cliente só aceita contato pela manhã.', 3),
(3, 'Angelica', 'gel-99@hotmail.com', '52365896521', '11997815632', 'SP', 'Santos', 1, 'Falar com André', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ORIGEM`
--

CREATE TABLE `ORIGEM` (
  `ID` int(11) NOT NULL,
  `SITE` tinyint(1) DEFAULT NULL,
  `BOCA` tinyint(1) DEFAULT NULL,
  `FACEBOOK` tinyint(1) DEFAULT NULL,
  `INDICACAO` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `ORIGEM`
--

INSERT INTO `ORIGEM` (`ID`, `SITE`, `BOCA`, `FACEBOOK`, `INDICACAO`) VALUES
(2, 1, 1, 1, 1),
(3, 1, 1, 1, 1),
(4, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `USUARIOS`
--

CREATE TABLE `USUARIOS` (
  `ID` int(11) NOT NULL,
  `NOME` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `EMAIL` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SENHA` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SEXO` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TELEFONE` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ESTADO` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CIDADE` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SITUACAO` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `USUARIOS`
--

INSERT INTO `USUARIOS` (`ID`, `NOME`, `EMAIL`, `SENHA`, `SEXO`, `TELEFONE`, `ESTADO`, `CIDADE`, `SITUACAO`) VALUES
(2, 'Adriano', 'adriano@hotmail.com', '31_Bgjf', 'M', '45997905684', 'PR', 'Arapongas', 0),
(3, 'Jessica', 'jess_sp@gmail.com', 'Br4z_1L', 'F', '11981064711', 'SP', 'Santos', 1),
(5, 'Miguel', 'miguelsk8@outlook.com', 'Bau_5683P', 'M', '33996547958', 'RJ', 'Cantagalo', 0),
(10, 'Julha', 'julhaferr@hotmail.com', 'Abc_123', 'F', '14991005847', 'SP', 'Bauru', 1),
(11, 'Rodrigo', 'rod.1995@hotmail.com', 'S3nha_', 'M', '14981505968', 'SP', 'Bauru', 1),
(12, 'Rafaela', 'rafa@hotmail.com', '4234-GHh', 'F', '36996478213', 'RJ', 'Carmo', 0),
(14, 'Cristian', 'cris_2006@live.com', 'g0t_S0801', 'M', '37981545268', 'PR', 'Arapongas', 1),
(27, 'Diego', 'diegorodrigues@hotmail.com', 'Gk_2018', 'M', '48995312015', 'SC', 'Chapecó', 1);

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `CLIENTES`
--
ALTER TABLE `CLIENTES`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `UC_CLI` (`NOME`,`EMAIL`,`CPF`,`TELEFONE`),
  ADD KEY `ID_OR` (`ID_OR`);

--
-- Índices de tabela `ORIGEM`
--
ALTER TABLE `ORIGEM`
  ADD PRIMARY KEY (`ID`);

--
-- Índices de tabela `USUARIOS`
--
ALTER TABLE `USUARIOS`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `UC_EMAIL` (`NOME`,`EMAIL`,`TELEFONE`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `CLIENTES`
--
ALTER TABLE `CLIENTES`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `ORIGEM`
--
ALTER TABLE `ORIGEM`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `USUARIOS`
--
ALTER TABLE `USUARIOS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `CLIENTES`
--
ALTER TABLE `CLIENTES`
  ADD CONSTRAINT `CLIENTES_ibfk_1` FOREIGN KEY (`ID_OR`) REFERENCES `ORIGEM` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
