<?php
	function conectar()
	{
		$banco = "u659795065_proc";		// BD Nome
		$usuario = "u659795065_admin";	// BD User
		$senha = "procedobauru";		// BD Pass
		$hostname = "127.0.0.1";		// BD IP

		$mysqli = new mysqli($hostname, $usuario, $senha, $banco);
		if($mysqli->connect_errno)
		{
			printf("Conexão falhou: %s\n", $mysqli->connect_error);
			exit;
		}
		return $mysqli;
	}
?>