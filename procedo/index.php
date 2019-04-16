<?php
	// Visualização de Erros - Apenas para Desenvolvimento
	//ini_set('display_errors', 1);
	//error_reporting(E_ALL);
	
	//echo var_dump($_POST);
	
	// Verifica COOKIE
	$cookie = (isset($_COOKIE['procedo']) ? $_COOKIE['procedo'] : header("location: login.html"));
	
	// Conexão com o BD
	include './class/db/bd.php';
	$mysqli = conectar();
	
	// Função de Mascaras
	function mask($val, $mask)
	{
		$maskared = '';
		$k = 0;
		for($i = 0; $i<=strlen($mask)-1; $i++)
		{
		if($mask[$i] == '#')
		{
		if(isset($val[$k]))
		$maskared .= $val[$k++];
		}
		else
		{
		if(isset($mask[$i]))
		$maskared .= $mask[$i];
		}
		}
		return $maskared;
	}
	// Variavel Botao 2 Tab
	$pesquisar = (isset($_POST['u_ir']) ? 1 : 0);
	// Variavel Botao 1 Tab
	$cpesquisar = (isset($_POST['c_ir']) ? 1 : 0);
	
	if ($pesquisar == '1')
	{
		$sql = "SELECT * FROM USUARIOS ";
		$nome = (isset($_POST['pnome']) ? $_POST['pnome'] : NULL);
		$cidade = (isset($_POST['cidades']) ? $_POST['cidades'] : NULL);
		$estado = (isset($_POST['estados']) ? $_POST['estados'] : NULL);
		$situacao = (isset($_POST['situacao']) ? $_POST['situacao'] : NULL);
		
		// Filro pesquisa por NOME > ESTADO > CIDADE > SITUACAO
		if ($nome <> NULL)
		{
			$sql .= " WHERE NOME = '$nome'";
			if ($estado <> NULL)
			{
				$sql .= " AND ESTADO = '$estado' ";
				if ($cidade <> NULL)
				{
					$sql .= " AND CIDADE = '$cidade' ";
				}
			}
			if ($situacao <> NULL)
			{
				$sql .= " AND SITUACAO = '$situacao' ";
			}
		}
		elseif ((($nome == NULL) or ($nome == '')) and ($estado <> NULL))
		{
			$sql .= " WHERE ESTADO = '$estado' ";
			if ($cidade <> NULL)
			{
				$sql .= " AND CIDADE = '$cidade' ";
			}
			if ($situacao <> NULL)
			{
				$sql .= " AND SITUACAO = '$situacao' ";
			}
		}
		elseif ($situacao <> NULL)
		{
			$sql .= "WHERE SITUACAO = '$situacao' ";
		}
		
		$sql .= "ORDER BY ID ASC;";
	}
	else
	{
		$sql = "SELECT * FROM USUARIOS ORDER BY ID ASC;";
	}
	
	if ($cpesquisar == '1')
	{
		$csql = "SELECT * FROM CLIENTES ORDER BY ID ASC;";
	}
	else
	{
		$csql = "SELECT * FROM CLIENTES ORDER BY ID ASC;";
	}
	
	
?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="UTF-8"> 
		<title>Desafio Técnico</title>
		<link rel="icon" href="class/img/logo.png" type="image" sizes="32x32">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="/resources/demos/style.css">
		<link href="class/css/index.css"rel="stylesheet">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script>
		$( function() {
			$( "#tabs" ).tabs();
			
			// Getter
			//var active = $( "#tabs" ).tabs( "option", "active" );
			
			// Setter
			$( "#tabs" ).tabs( "option", "active", <?php echo $pesquisar ?>);
		} );
		</script>
		<script type="text/javascript">	
		$(document).ready(function () 
		{
			$.getJSON('class/json/brasil.json', function (data) 
			{
				var items = [];
				var options = '<option value="" disabled selected>UF</option>';
				$.each(data, function (key, val) {
					options += '<option value="' + val.sigla + '">' + val.sigla + '</option>';
				});					
				$("#estados").html(options);				
				
				$("#estados").change(function () 
				{
					var options_cidades = '<option value="" disabled selected>Cidade</option>';
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
		<div class="header"><br></div>
			<div id="tabs">
				<ul>  			
					<li><a href="#tabs-1"> Clientes </a></li>
					<li><a href="#tabs-2"> Usuarios </a></li>
					<form method="POST" action="logout.php">
						<input class="sair" type="submit" name="logout" value="Sair">
					</form>
				</ul> 
				<div id="tabs-1">
					<div>
						<form method="POST" action="index.php">
							<center>
								<input type="text" placeholder="Pesquisar por Nome" name="c_nome" value="">
								<input class="menubotao"type="submit" name="c_ir" value="ir">
								<select class="appearance-select" name="c_cidades" id="cidades">
								</select>
								<select class="appearance-select" name="c_estados" id="estados">
									<option value="">UF</option>
								</select>
								<select class="appearance-select" name="c_situacao">
									<option value="" Selected>Situacao</option>
									<option value="1"> Ativo </option>
									<option value="0"> Desativado </option>
								</select>
								<select  name="c_origem" multiple size="1">
									<option value="" Selected>Origem</option>
									<option value="1"> Site </option>
									<option value="0"> Boca a Boca </option>
									<option value="0"> Facebook </option>
									<option value="0"> Indicação </option>
								</select>
							</center>
						</form>
					</div>
					<div>
						<hr class="new5">
						<form method="POST" action="clientes.php">
							<center>
							<div class="btn-group">
								<input type="submit" name="c_cadastrar" value="Cadastrar Novo Cliente">
								<input type="submit" name="c_editar" value="Editar Cliente Selecionado">
								<input type="submit" name="c_excluir" value="Excluir Cliente Selecionado">
							</div>
							</center>
							<hr class="new5">
							<table class="tabela">
								<tr>
									<th>Nome</th>
									<th>Email</th>
									<th>Documento</th>
									<th>Telefone</th>
									<th>Origem</th>
									<th>Estado</th>
									<th>Cidade</th>
									<th>Situação</th>
									<th>Selecionar</th>
								</tr>
								<?php
									//$csql = "SELECT * FROM CLIENTES ORDER BY ID ASC;";
									if(!$cresultado = $mysqli->query($csql))
									{
										echo "Errno: " . $mysqli->errno . " - Errno: " . $mysqli->error . "\n";
										exit;
									}
									while ($linha = $cresultado->fetch_array(MYSQLI_ASSOC))
									{
										$id = $linha["ID"];
										$nome = $linha["NOME"];
										$email = $linha["EMAIL"];
										$cpf = mask($linha["CPF"], '###.###.###-##');
										$telefone = mask($linha["TELEFONE"], '(##) # ####-####');
										$origem = 'N/A';
										$cidade = $linha["CIDADE"];
										$estado = $linha["ESTADO"];
										$situacao = ($linha["SITUACAO"] == 1) ? 'Ativo' : 'Desativado';
										echo "<tr>";
										echo "<td align='left'>" . $nome . "</td>";
										echo "<td align='left'>" . $email . "</td>";
										echo "<td align='left'>" . $cpf . "</td>";
										echo "<td align='center'>" . $telefone . "</td>";
										echo "<td align='center'>" . $origem . "</td>";
										echo "<td align='center'>" . $estado . "</td>";
										echo "<td align='center'>" . $cidade . "</td>";
										echo "<td align='center'>" . $situacao . "</td>";
										echo "<td align='center'> <input type='radio' name='opcao' value=$id> </td>";
										echo "</tr>";
									}
									
								?>
							</table>
						</form>
					</div>
				</div>
				<div id="tabs-2">
					<div>
						<form method="POST" action="index.php">
							<center>
								<input type="text" placeholder="Pesquisar por Nome" name="pnome" value="">
								<input class="menubotao"type="submit" name="u_ir" value="ir">
								<select class="appearance-select" name="cidades" id="cidades">
								</select>
								<select class="appearance-select" name="estados" id="estados">
									<option value="">UF</option>
								</select>
								<select class="appearance-select" name="situacao">
									<option value="" Selected>Situacao</option>
									<option value="1"> Ativo </option>
									<option value="0"> Desativado </option>
								</select>
							</center>
						</form>
					</div>
					<div>
						<hr class="new5">
						<form method="POST" action="usuarios.php">
							<center>
							<div class="btn-group">
								<input type="submit" name="cadastrar" value="Cadastrar Novo Usuario">
								<input type="submit" name="editar" value="Editar Usuario Selecionado">
								<input type="submit" name="excluir" value="Excluir Usuario Selecionado">
							</div>
							</center>
							<hr class="new5">
							<table class="tabela">
								<tr>
									<th>Nome</th>
									<th>Email</th>
									<th>Sexo</th>
									<th>Telefone</th>
									<th>Cidade</th>
									<th>Estado</th>
									<th>Situação</th>
									<th>Selecionar</th>
								</tr>
								<?php
									//$sql = "SELECT * FROM USUARIOS ORDER BY ID ASC;";
									if(!$resultado = $mysqli->query($sql))
									{
										echo "Errno: " . $mysqli->errno . " - Errno: " . $mysqli->error . "\n";
										exit;
									}
									while ($linha = $resultado->fetch_array(MYSQLI_ASSOC))
									{
										$id = $linha["ID"];
										$nome = $linha["NOME"];
										$email = $linha["EMAIL"];
										$sexo = ($linha["SEXO"] == 'M') ? 'Masculino' : 'Feminino';
										$telefone = mask($linha["TELEFONE"], '(##) # ####-####');
										$cidade = $linha["CIDADE"];
										$estado = $linha["ESTADO"];
										$situacao = ($linha["SITUACAO"] == 1) ? 'Ativo' : 'Desativado';
										echo "<tr>";
										echo "<td align='left'>" . $nome . "</td>";
										echo "<td align='left'>" . $email . "</td>";
										echo "<td align='left'>" . $sexo . "</td>";
										echo "<td align='center'>" . $telefone . "</td>";
										echo "<td align='center'>" . $cidade . "</td>";
										echo "<td align='center'>" . $estado . "</td>";
										echo "<td align='center'>" . $situacao . "</td>";
										echo "<td align='center'> <input type='radio' name='opcao' value=$id> </td>";
										echo "</tr>";
									}
								?>
							</table>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>

