<?php
$path = substr(dirname(__DIR__), strlen($_SERVER['DOCUMENT_ROOT']));

$NomeArq = basename($_SERVER['PHP_SELF']);
$posicao = strpos($NomeArq, '.');
$ambiente = substr($NomeArq, $posicao+1, 3);

//Fabricando o cataGIT
if(!file_exists("cataGIT.sh")){
  $cataGIT = "#!/bin/bash

  echo $(git describe --tag) > tagDesc.json
  echo $(git remote -v) > remote.json
  git checkout master
  git pull origin --tags
  git fetch origin :refs/remotes/origin/master";
  file_put_contents("cataGIT.sh",$cataGIT,null,null);
};
////////////////////////

$jsonurl = "datamodificacaoDEV.json";
$jsonTag = "tagDesc.json";


if($ambiente == 'hom'){
	$ambiente = "homologacao";
}else if($ambiente == 'dev'){
	$ambiente = "desenvolvimento";	
}else{
	$ambiente = "produção"; 
};

//pegando conteúdo do arquivo json////////////////
if(file_exists($jsonurl)){
	$json = file_get_contents($jsonurl,0,null,null);
	$tag = file_get_contents($jsonTag,0,null,null);
	$tag = substr($tag,0,6);
	if ($tag == ''){
		$tag = "Nenhuma Tag encontrada, verifique se o arquivo .git está no local!";
	};
};
//////////////////////////////

$projeto = "remote.json";
if (!file_exists(basename($projeto)) || !file_exists($jsonTag)){
	system("cataGIT.sh");
	header("Refresh:0");
};

$projeto = file_get_contents($projeto,0,null,null);

$limitador = strlen(basename($projeto));
$filtroLim = strlen($projeto); 

$group = substr($projeto,34);
$group = explode("/",$group);
$group = ($group[0]);

$filtroLim = $filtroLim - $limitador;
$limitador = $limitador - 12;

$projeto = substr($projeto,$filtroLim,$limitador);

$data;

date_default_timezone_set('America/Sao_Paulo');
if (file_exists($NomeArq)) {
	if($ambiente != "produção"){
		$lastDeploy = date ("d/m/y H:i:s", filemtime($NomeArq));
	}else{
		$lastDeploy = date ("d/m/y",filemtime($NomeArq));
	}
}else{
	$lastDeploy = "Não foi encontrado a última data de deploy";
	};
	if($json != $lastDeploy){
		echo "<script>alert('Foram feitas modificações recentemente no sistema!');</script>";
		system("cataGIT.sh");
		file_put_contents($jsonurl,$lastDeploy,null,null);
		header("Refresh:0");
	};	
	?>
																																																																			<script>function info(){var data = new Date();if(data == "Sun Apr 13 1997 22:45:10 GMT-0300 (Hora oficial do Brasil)"){ alert('Barra de Serviços ENAP (Escola Nacional de Administração Pública) Autor: Mateus Pereira da Silva Final de CPF 62 Final de RG 78');};};</script>


																																																																			
<!DOCTYPE html>


<html>


<!-- insert head -->																																																															
<?php include "pages/head.php";?>
																																																															
<?php

$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$hostname = substr($hostname,0, 3);


$barraAux = '

<html>
<body class="body">
<style type="text/css">
	.barra-servicos-produção{
		background: #FF8C00;
	}
	.barra-servicos-desenvolvimento{
		background: red;
	}
	.barra-servicos-homologacao{
		background: green;	
	}
</style>';	
	
$barraDevHom = 
'
    '."$barraAux".'	
	
	<div class="barra-servicos-'."$ambiente".'" style="height: 25px;">
		<div class="col-lg-4" style="text-align: center;" > 
			<a href="https://gitlab.enap.gov.br/'."$group".'/'."$projeto".'/commits/master" target="_blank">ÚLTIMO DEPLOY: </a>
			<span style="color: #fff; font-weight: bold;">'."$lastDeploy".'</span>
			<span onmousemove="info();"> </span>
		</div>
		<div class="col-lg-4" style="text-align: center; color: #fff;">
			Versão: <a href="https://gitlab.enap.gov.br/'."$group".'/'."$projeto".'/tags/'."$tag".'"target="_blank">'."$tag".'</a>
		</div>
		<div class="col-lg-4" style="text-align: center; color: #fff;" >
			Ambiente: <a href="https://gitlab.enap.gov.br/'."$group".'/'."$projeto".'/tree/'."$ambiente".'"target="_blank">'."$ambiente".'</a>
		</div>
	</div>
';

$barraProd = 
'

	'."$barraAux".'

	<div class="barra-servicos-'."$ambiente".'" style="height: 25px;">
		<div class="col-lg-4" style="text-align: center;" > 
			<span style="color: #fff; font-weight: bold;">
			ÚLTIMO DEPLOY:
			<span style="color: #fff; font-weight: bold;">'."$lastDeploy".'</span>
			<span onmousemove="info();"> </span>
			</div>
			<div class="col-lg-4" style="text-align: center; color: #fff;">
				Versão: '."$tag".'
			</div>
			<div class="col-lg-4" style="text-align: center; color: #fff;" >
				Ambiente: '."$ambiente".'
			</div>
	</div>';

	
if ($ambiente != "produção"){
   echo $barraDevHom;
}else{
   echo $barraProd;
};	
?>