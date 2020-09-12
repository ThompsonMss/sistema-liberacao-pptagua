<?php
	include("config.php");

	//Declarando variaveis
	$alerta = false;

	//Verificando data do servidor
	$dataServidor = $conn->query("SELECT `dataSystem` FROM `system` WHERE `dataSystem` <= CURDATE()");
	if($dataServidor->rowCount() > 0){
    	$updateDataSystem = $conn->query("UPDATE system SET dataSystem = CURDATE() WHERE id_system = 1");
    	$updateDataSystem->execute();
    }else{
    	header("Location: bloqueado.php");	
    	exit;
    }

    //Verificando se tem evento cadastrado
    $eventoCadastrado = $conn->query("SELECT id_evento FROM evento WHERE data_inicio <= CURDATE() AND data_fim >= CURDATE()");

    if(!$eventoCadastrado->rowCount() > 0){
    	header("Location: eventonull.php");	
    	exit;
    }

    //Verificando se tem placa
    if(isset($_POST['doSub'])){
    	if(isset($_POST['placa']) && !empty($_POST['placa'])){
    		$placa = $_POST['placa'];
    		$verPlaca = $conn->query("SELECT l.*, f.nome FROM `liberacao` as l INNER JOIN `funcionario` as f ON l.id_funcionario = f.id_funcionario WHERE `id_evento` = (SELECT MAX(id_evento) FROM liberacao) AND `placa` = '$placa' AND l.status = '0'");
    		if($verPlaca->rowCount()>0){
    			$verPlaca = $verPlaca->fetch();
    			$verPerma = $conn->query("SELECT l.*,f.nome, (SELECT CURRENT_TIMESTAMP) as dataAtual FROM `liberacao` as l INNER JOIN `funcionario` as f ON l.id_funcionario = f.id_funcionario WHERE id_evento = (SELECT MAX(id_evento) FROM liberacao) AND `placa` = '$placa' AND `permanencia` = '1'");
    			if($verPerma->rowCount()>0){
    				$verPerma = $verPerma->fetch();
    				$dataAtual = $verPerma['dataAtual'];
    				$dataLib = $verPerma['data_liberacao'];

    				if(strtotime($dataAtual) - strtotime($dataLib) > 1200){
    					header("Location: permanenciafail.php");
    					exit;
    				}else{
    					$placaLiberada = $verPerma;
    				}
    			}else{
    				$placaLiberada = $verPlaca;
    			}
    		}else{
    			header("Location: placafail.php");
    			exit;
    		}
    	}else{
    		$alerta = true;
    	}
    }
    if(isset($_POST['doUp'])){
    	$id = $_POST['idliberacao'];
    	$updateDes = $conn->query("UPDATE `liberacao` SET `status` = '1' WHERE `id_liberacao` = '$id'");
    	header("Location: index.php");
    	exit;
    }
?>
<html>
	<head>
		<title>Sistema Liberação - Pesque Pague Taguatinga</title>
		<link rel="stylesheet" href="bootstrap-4.0.0-dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/style.css">
		<script type="text/javascript" src="/bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

		<meta charset=utf-8>
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name=description content="">
    	<meta name=viewport content="width=device-width, initial-scale=1">

    	<link rel="icon" href="favicon.ico">

    	<script>
    		function maiscula(z){
    			v = z.value.toUpperCase();
    			z.value = v;
    		}
    	</script>
	</head>
	<body class="body-index">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="title"><span>Sistema Liberação</span></div>
					<div class="img-logo"><center><img src="logotipo.png" alt="LogoTipo"></center></div>
				</div>
			</div>
			<?php if($alerta == true): ?>
				<div class="row">
					<div class="col-md-12">
						<div class="alerta"><span>PREENCHA O CAMPO PLACA!</span></div>
					</div>
				</div>
			<?php endif; ?>
			<div class="row">
				<div class="col-md-12">
					<div class="form-index">
						<form action="" method="POST" name="primer">
							<div class="input-index">
								<input type="text" onkeyup="maiscula(this)" name="placa" autofocus="">,
							</div>
							<div class="botao-index">
								<input type="submit" name="doSub" value="BUSCAR">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php if(!empty($placaLiberada)): ?>
		<div class="liberado" style="display: block;">
			<style>
				.input-index input{
					display: none;
				}
				.botao-index input{
					display: none;
				}
			</style>
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<span>Placa Liberada!<br><br><?php echo "Cartão: ".@$placaLiberada['cartao']."<br>Placa: ".@$placaLiberada['placa']."<br>Qtd pessoas: ".@$placaLiberada['qtd_pessoas']."<br>Autorizado por: ".@$placaLiberada['nome']."<br><br>Obrigado e volte sempre!" ?></span>
						<form action="" method="POST">
							<input type="text" name="idliberacao" value="<?php echo @$placaLiberada['id_liberacao']?>" style="display: none;">
							<div class="btn-des"><input type="submit" name="doUp" value="DESCARTAR"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</body>
</html>