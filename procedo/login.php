<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="UTF-8"> 
		<title>Login</title>
		<link rel="icon" href="class/img/logo.png" type="image" sizes="32x32">
		<link href="class/css/login.css"rel="stylesheet">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="/resources/demos/style.css">
	</head>
	<body>
		<div class="main">
			<div class="container">
				<div class="poslogin">
					<?php
						// Criar COOKIE
						function createcookie($email, $senha)
						{
							$id = md5($email . $senha);
							$expira = time() + 3600;
							setcookie('procedo', $id, $expira);
							echo "<p><p>Seja Bem Vindo!</p><div class='loader'></div></p>";
							header("refresh:5; url=index.php");
						}
						
						// Diminuir Repetição de Código
						function comeback()
						{
							echo "<p><div class='loader'></div></p>";
							header("refresh:5; url=login.html");
						}
						
						// Rodar Scripts SQL 
						function runsql($sql)
						{
							require_once('./class/db/bd.php');
							$mysqli = conectar();
							if(!$resultado = $mysqli->query($sql))
							{
								echo "Errno: " . $mysqli->errno . " - Errno: " . $mysqli->error . "\n";
								exit;
							}
							while($row = $resultado->fetch_array())
							{
								$codigo = $row['RETORNO']; //Captura valor de retorno
							}
							return $codigo;
						}
						
						//Verifica variaveis que foram passadas pelo método POST
						$entrar = (isset($_POST['entrar'])) ? TRUE : FALSE;
						$cadastrar = (isset($_POST['cadastrar'])) ? TRUE : FALSE;
						$recuperar = (isset($_POST['recuperar'])) ? TRUE : FALSE;
						
						//Inicio 
						if ($entrar == TRUE)
						{
							// Carregar Variaveis POST
							$email = (isset($_POST['email'])) ? $_POST['email']: '';
							$senha = (isset($_POST['senha'])) ? $_POST['senha']: '';
							// Chamar Função do MYSQL
							$sql = "SELECT ACESSO ('$email', '$senha') AS RETORNO";
							$codigo = runsql($sql);
							
							if ($codigo == 0)
							{
								createcookie($email, $senha);
							}
							elseif ($codigo == 1)
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Email ou Senha Incorretos<p>";
								comeback();
							}
							elseif ($codigo == 2)
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Usuário Desativado<p>";
								comeback();
							}
						}
						elseif ($cadastrar == TRUE)
						{
							// Carregar Variaveis POST
							$nome = (isset($_POST['nome'])) ? $_POST['nome']: '';
							$email = (isset($_POST['email'])) ? $_POST['email']: '';
							$senha = (isset($_POST['senha'])) ? $_POST['senha']: '';
							$sexo = (isset($_POST['sexo'])) ? $_POST['sexo']: '';
							$telefone = (isset($_POST['telefone'])) ? $_POST['telefone']: '';
							$cidade = (isset($_POST['cidades'])) ? $_POST['cidades']: '';
							$estado = (isset($_POST['estados'])) ? $_POST['estados']: '';
							// Chamar Função do MYSQL
							$sql = "SELECT CADASTRO('$nome', '$email', '$senha', '$sexo', '$telefone', '$estado', '$cidade') AS RETORNO";
							$codigo = runsql($sql);
							
							if ($codigo == 0)
							{
								echo "<span class='fa fa-check-circle'></span><p>Cadastrado com Sucesso!<p>";
								createcookie($email, $senha);
							}
							elseif ($codigo == 1)
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Nome já cadastrado<p>";
								comeback();
							}
							elseif ($codigo == 2)
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Email já cadastrado<p>";
								comeback();
							}
							elseif ($codigo == 3)
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Preencha todos os campos<p>";
								comeback();
							}
							elseif ($codigo == 4)
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Telefone já cadastrado<p>";
								comeback();
							}
							else
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Um erro ocorreu, tente novamente<p>";
								comeback();
							}
						}
						elseif ($recuperar == TRUE)
						{
							$email = (isset($_POST['email'])) ? $_POST['email']: '';
							
							$sql = "SELECT VER_EMAIL('$email') AS RETORNO";
							$codigo = runsql($sql);
							if ($codigo != 1)
							{
								$from = "contato@rarit.com.br";
								$to = $email;
								$subject = "Procedo Senha";
								$message = "Senha: $codigo";
								$headers = "De:". $from;
								mail($to, $subject, $message, $headers);
								echo "<span class='fa fa-check-circle'></span><p>Email Enviado: Verifique a caixa de Spam<p>";
								comeback();
							}
							else
							{
								echo "<span class='fa fa-exclamation-circle'></span><p>Email Não Encontrado<p>";
								comeback();
							}
						}
						else
						{
							echo "<span class='fa fa-exclamation-circle'></span><p>Um erro ocorreu, tente novamente<p>";
							comeback();
						}
					?>	
					</div>
				</div>
			</div>
		</div>
	</body>
</html>