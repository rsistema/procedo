<?php
	//echo var_dump($_POST);
	// Verifica variaveis da pagina atual
	$botao = ($_POST['botao'] == 'editar') ? '1' : NULL;
	$botao = ($_POST['botao'] == 'cadastrar') ? '2' : NULL;
	
	// Verifica COOKIE
	$cookie = (isset($_COOKIE['procedo']) ? $_COOKIE['procedo'] : header("location: login.html"));
	
	// Verifica variaveis que foi passado pelo Index.php-> Tab [Usuarios]
	$novo = ($_POST['c_cadastrar'] <> NULL) ? TRUE : FALSE;
	$editar = ($_POST['c_editar'] <> NULL) ? TRUE : FALSE;
	$excluir = ($_POST['c_excluir'] <> NULL) ? TRUE : FALSE;
	$opcao = (isset($_POST['opcao'])) ? $_POST['opcao']: NULL;
	
	// Conexão com Banco de Dados
	include './class/db/bd.php';
	$mysqli = conectar();
	
	// Diminuir replicação de código 
	function comeback()
	{
		echo "<p><div class='loader'></div></p>";
		header("refresh:5; url=index.php");
	}
	
	if (isset($_POST['botao']))
	{
		// Captura Váriaveis para Próximo Passo (Editar / Criar)
		$id = (isset($_POST['id'])) ? $_POST['id']: '';
		$nome = (isset($_POST['nome'])) ? $_POST['nome']: '';
		$email = (isset($_POST['email'])) ? $_POST['email']: '';
		$cpf = (isset($_POST['cpf'])) ? $_POST['cpf']: '';
		$telefone = (isset($_POST['telefone'])) ? $_POST['telefone']: '';
		$cidade = (isset($_POST['cidades'])) ? $_POST['cidades']: '';
		$estado = (isset($_POST['estados'])) ? $_POST['estados']: '';
		$obs = (isset($_POST['obs'])) ? $_POST['obs']: '';
		$situacao = (isset($_POST['situacao'])) ? $_POST['situacao']: '';
		// Origem
		$a = (isset($_POST['a'])) ? '1': '0';
		$b = (isset($_POST['b'])) ? '1': '0';
		$c = (isset($_POST['c'])) ? '1': '0';
		$d = (isset($_POST['d'])) ? '1': '0';
		//

		// Condição para definir se será um CADASTRO ou EDIÇÃO
		if ($_POST['botao'] == 'editar') 
		{
			echo "<title>Editar Cliente</title>";
			$sql = "UPDATE CLIENTES SET NOME='$nome', EMAIL='$email', CPF='$cpf', TELEFONE='$telefone', ESTADO='$estado', CIDADE='$cidade', SITUACAO='$situacao', OBS='$obs' WHERE ID = '$id'";
			$resultado = $mysqli->query($sql);
			$editado = TRUE; // Utilizado para comparação de que o usuario foi editado
		}
		elseif ($_POST['botao'] == 'cadastrar')
		{
			echo "<title>Cadastrar Novo Cliente</title>";
			$sql = "SELECT CADASTRO_CLIENTE ('$nome', '$email', '$cpf', '$telefone', '$estado', '$cidade', 1, 1, 1, 1, '$obs') AS RETORNO;";
			$resultado = $mysqli->query($sql);
			while($row = $resultado->fetch_array())
			{
				$codigo = $row['RETORNO']; //Captura valor de retorno
			}
			$cadastrado = TRUE; // Utilizado para comparação de que o usuario foi cadastrado
		}
	}
	
	if ($novo == TRUE)
	{
		// Sem necessidade de puxar nenhuma variavel do metodo POST 
	}
	elseif (($editar == TRUE) and ($opcao <> NULL))
	{
		$sql = "SELECT * FROM CLIENTES WHERE ID = '$opcao' LIMIT 1;";
		$resultado = $mysqli->query($sql);
		while ($linha = $resultado->fetch_array(MYSQLI_ASSOC))
		{
			$id = $linha["ID"];
			$nome = $linha["NOME"];
			$email = $linha["EMAIL"];
			$cpf = $linha["CPF"];
			$telefone = $linha["TELEFONE"];
			$estado = $linha["ESTADO"];
			$cidade = $linha["CIDADE"];
			$situacao = $linha["SITUACAO"];
			$obs = $linha["OBS"];
			$origem = $linha["ID_OR"];
		}
	}
	elseif (($excluir == TRUE) and ($opcao <> NULL))
	{
		echo "<title>Excluir Usuario</title>";
		$sql = "DELETE FROM CLIENTES WHERE ID = '$opcao' LIMIT 1;";
		$resultado = $mysqli->query($sql);
		$excluido = TRUE; // Utilizado para comparação de que o usuario foi excluido
	}
?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="UTF-8"> 
		<link rel="icon" href="class/img/logo.png" type="image" sizes="32x32">
		<link href="class/css/login.css"rel="stylesheet">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="/resources/demos/style.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script type="text/javascript">	
		$(document).ready(function () 
		{
			$.getJSON('class/json/brasil.json', function (data) 
			{
				var items = [];
				var options = '<option <?php echo ($estado <> NULL ? 'value='. $estado .'' : 'value="" disabled') ?> selected><?php echo ($estado <> NULL ? $estado : 'UF')?></option>';
				$.each(data, function (key, val) {
					options += '<option value="' + val.sigla + '">' + val.sigla + '</option>';
				});					
				$("#estados").html(options);				
				
				$("#estados").change(function () 
				{
					var options_cidades = '<option <?php echo ($cidade <> NULL ? 'value='. $cidade .'' : 'value="" disabled') ?> selected><?php echo ($cidade <> NULL ? $cidade : 'Cidade')?></option>';
					var str = "";		
					
					$("#estados option:selected").each(function () {
						str += $(this).text();
					});
					
					$.each(data, function (key, val) {
						if(val.sigla == str) {							
							$.each(val.cidades, function (key_city, val_city) {
								options_cidades += '<option value="' + val_city + '">' + val_city + '</option>';
							});							
						}
					});
					$("#cidades").html(options_cidades);
					
				}).change();		
			});
		});
		</script>
	</head>
	<body>
		<div class="main">
			<div class="container">
			<?php
				if ($editado == TRUE)
				{
					echo "<div class='poslogin'>";
					echo "<span class='fa fa-check-circle'></span><p> Cliente Editado com Sucesso !<p>";
					comeback();
					echo "</div>";
				}
				elseif ($excluido == TRUE)
				{
					echo "<div class='poslogin'>";
					echo "<span class='fa fa-check-circle'></span><p> Cliente Excluido com Sucesso !<p>";
					comeback();
					echo "</div>";
				}
				elseif ($cadastrado == TRUE)
				{
					if ($codigo == 0)
					{
						echo "<div class='poslogin'>";
						echo "<span class='fa fa-check-circle'></span><p>Cliente Cadastrado com Sucesso!<p>";
						comeback();
						echo "</div>";
					}
					elseif ($codigo == 1)
					{
						echo "<div class='poslogin'>";
						echo "<span class='fa fa-exclamation-circle'></span><p>Nome já cadastrado<p>";
						comeback();
						echo "</div>";
					}
					elseif ($codigo == 2)
					{
						echo "<div class='poslogin'>";
						echo "<span class='fa fa-exclamation-circle'></span><p>Email já cadastrado<p>";
						comeback();
						echo "</div>";
					}
					elseif ($codigo == 3)
					{
						echo "<div class='poslogin'>";
						echo "<span class='fa fa-exclamation-circle'></span><p>CPF já cadastrado<p>";
						comeback();
						echo "</div>";
					}
					elseif ($codigo == 4)
					{
						echo "<div class='poslogin'>";
						echo "<span class='fa fa-exclamation-circle'></span><p>Telefone já cadastrado<p>";
						comeback();
						echo "</div>";
					}
					else
					{
						echo "<div class='poslogin'>";
						echo "<span class='fa fa-exclamation-circle'></span><p>Um erro ocorreu, tente novamente<p>";
						comeback();
						echo "</div>";
					}	
				}
				else
				{?>
					<div id="login">
					<form method="POST" action="clientes.php">
						<input type="hidden" id="id" name="id" value="<?php echo $id ?>">
						<p><span class="fa fa-user"></span><input type="text" name="nome" Placeholder="Nome" pattern="[A-Za-z]{3,}" required value="<?php echo $nome ?>"></p>
						<p><span class="fa fa-envelope"></span><input type="email" name="email" Placeholder="Email" required value="<?php echo $email ?>"></p> 
						<p><span class="fa fa-lock"></span><input type="text" name="cpf" Placeholder="CPF" required value="<?php echo $cpf ?>" maxlength="11" pattern="\d{11}"> 
						<div class="tooltip">
							<p><span class="fa fa-phone"></span><input type="text" name="telefone" Placeholder="Telefone" pattern="\d{11}" maxlength="11" required value="<?php echo $telefone ?>">
							<span class="tooltiptext">Requisitos do Telefone:<br>• DD<br>• 9º Digito<br>• Mais Numero<br>Exemplo: 14997013456<span></p></p>
						</div>
						<p><span class="fa fa-location-arrow"></span>
							<select name="cidades" id="cidades" class="appearance-select" required>
							</select>
							<select name="estados" id="estados" class="appearance-select" required>
								<option value="">UF</option>
							</select>
						</p>
						<br>
						<div class="checkplace">
							<p><input type="checkbox" name="a" value="1">Site
							<input type="checkbox" name="b" value="1">Boca a Boca</p>
							<p><input type="checkbox" name="c" value="1">Facebook
							<input type="checkbox" name="d" value="1">Indicação</p>
						</div>
						<br>
						<p><span class="fa fa-user"></span><input type="text" name="obs" Placeholder="Observação" value="<?php echo $obs ?>"></p>
						<?php
							if ($editar == TRUE)
							{
								?>
								<p>
								<span class="fa fa-signal"></span>
								<select name="situacao" id="situacao" class="appearance-select" required>
									<option value="1" <?php echo ($situacao == 1 ? 'selected' : '');?>>Ativo</option>
									<option value="0" <?php echo ($situacao == 0 ? 'selected' : '');?>>Desativado</option>
								</select>
								</p>
								<?php
							};
						?>
						<br>
						<div>
							<span style="width:100%; text-align:right;  display: inline-block;"><input type="submit" name="botao" <?php echo ($editar == TRUE ? 'value="editar"' : 'value="cadastrar"'); ?>></span>
						</div>
					</form>
					</div>
				<?php
				}?>
			</div>
		</div>
	</body>
</html>