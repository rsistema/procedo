<?php
	function conectar()
	{
		$banco = REMOVED;		// BD Nome
		$usuario = REMOVED;		// BD User
		$senha = REMOVED;		// BD Pass
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
