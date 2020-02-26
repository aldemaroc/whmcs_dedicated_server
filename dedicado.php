<?php
use WHMCS\Database\Capsule;

if(!defined('WHMCS')){
    die('This file cannot be accessed directly');
}

function dedicado_MetaData(){
    return array(
        'DisplayName' => 'Servidor Dedicado Manual',
        'APIVersion' => '1.1',
        'RequiresServer' => false
    );
}

function dedicado_ConfigOptions(){
    return [
        "librenms" => [
            "FriendlyName" => "Hostname do LibreNMS",
            "Type" => "text",
            "Description" => "Hostname completo da sua instalação do LibreNMS",
            "Default" => "mylibrenms.mydomain.com"
        ],
    ];
}

function dedicado_CreateTodoItem($params){
	$pdo = Capsule::connection()->getPdo();
	$date = $params['date'];
	$title = $params['title'];
	$description = $params['description'];
	$status = $params['status'];
	$duedate = $params['duedate'];
	$sql = $pdo->query('INSERT INTO tbltodolist (date, title, description, status, duedate) VALUES ("'.$date.'", "'.$title.'", "'.$description.'", "'.$status.'", "'.$duedate.'")');
	if($sql){
		$response = [
			'success' => true
		];
	}else{
		$response = [
			'success' => false,
			'error' => $sql->errorInfo()
		];
	}
	return $response;
}

function dedicado_CreateAccount($params){
	// Create TodoItem
	dedicado_CreateTodoItem([
		'date' => date('Y-m-d'),
		'title' => 'Instalação de Servidor Dedicado - Client ID: '.$params['clientsdetails']['userid'].' - Service ID: '.$params['serviceid'],
		'description' => '',
		'status' => 'Pendente',
		'duedate' => date('Y-m-d')
	]);
	// Create Ticket
	$command = 'OpenTicket';
	$postData = array(
		'deptid' => '3',
		'subject' => 'Instalação de Servidor Dedicado',
		'message' => 'Olá '.$params['clientsdetails']['firstname'].'!
		
		Este ticket foi aberto automaticamente pelo sistema e será utilizado para instalação de seu servidor dedicado.
		
		Em breve nossa equipe irá entrar em contato com você por meio dele, e caso deseje adicionar alguma informação basta responde-lo.',
		'clientid' => $params['clientsdetails']['userid'],
		'priority' => 'Medium',
		'serviceid' => $params['serviceid'],
		'markdown' => false,
		'admin' => true,
		'responsetype' => 'json',
	);
	localAPI($command, $postData, $adminUsername);
	// Return status
	return 'Criação manual';
}

function dedicado_SuspendAccount($params){
	// Create TodoItem
	dedicado_CreateTodoItem([
		'date' => date('Y-m-d'),
		'title' => 'Suspensão de Servidor Dedicado - Client ID: '.$params['clientsdetails']['userid'].' - Service ID: '.$params['serviceid'],
		'description' => '',
		'status' => 'Pendente',
		'duedate' => date('Y-m-d')
	]);
	// Create Ticket
	$command = 'OpenTicket';
	$postData = array(
		'deptid' => '3',
		'subject' => 'Suspensão de Servidor Dedicado',
		'message' => 'Olá '.$params['clientsdetails']['firstname'].'!
		
		Este ticket foi aberto automaticamente pelo sistema pois seu servidor dedicado será suspenso.
		
		Em breve nossa equipe irá entrar em contato com você por meio dele, e caso deseje adicionar alguma informação basta responde-lo.',
		'clientid' => $params['clientsdetails']['userid'],
		'priority' => 'Medium',
		'serviceid' => $params['serviceid'],
		'markdown' => false,
		'admin' => true,
		'responsetype' => 'json',
	);
	localAPI($command, $postData, $adminUsername);
	// Return status
	return 'Suspensão manual';
}

function dedicado_TerminateAccount($params){
	// Create TodoItem
	dedicado_CreateTodoItem([
		'date' => date('Y-m-d'),
		'title' => 'Cancelamento de Servidor Dedicado - Client ID: '.$params['clientsdetails']['userid'].' - Service ID: '.$params['serviceid'],
		'description' => '',
		'status' => 'Pendente',
		'duedate' => date('Y-m-d')
	]);
	// Create Ticket
	$command = 'OpenTicket';
	$postData = array(
		'deptid' => '3',
		'subject' => 'Cancelamento de Servidor Dedicado',
		'message' => 'Olá '.$params['clientsdetails']['firstname'].'!
		
		Este ticket foi aberto automaticamente pelo sistema pois seu servidor dedicado será cancelado.
		
		Em breve nossa equipe irá entrar em contato com você por meio dele, e caso deseje adicionar alguma informação basta responde-lo.',
		'clientid' => $params['clientsdetails']['userid'],
		'priority' => 'Medium',
		'serviceid' => $params['serviceid'],
		'markdown' => false,
		'admin' => true,
		'responsetype' => 'json',
	);
	localAPI($command, $postData, $adminUsername);
	// Return status
	return 'Finalização manual';
}


function dedicado_HTML($params){
	
	if(!empty($params['customfields']['ipmiip'])){
		$url = 'https://'.$params['customfields']['ipmiuser'].':'.$params['customfields']['ipmipass'].'@'.$params['customfields']['ipmiip'].'/redfish/v1/Systems/1/';
		$saida = file_get_contents($url, false, stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false,), 'http' => array('timeout' => 2,))));
		$var = json_decode($saida);
	}
	if($var->PowerState=="On"){
		$estado = '<b style="color:#2ab551"><i class="fas fa-power-off"></i> Ligado</b>';
		$botao = 'Desligar';
	}elseif($var->PowerState=="Off"){
		$estado = '<b style="color:#fa0000"><i class="fas fa-power-off"></i> Desligado</b>';
		$botao = 'Ligar';
	}else{
		$estado = '<b style="color:#737373"><i class="fas fa-question-circle"></i> Desconhecido</b>';
		$botao = 'Desconhecido';
	}
		
	if(!empty($params['customfields']['librenmsid'])){
		$imagem = file_get_contents('http://'.$params["configoption1"].'/graph.php?height=250&width=600&id='.$params['customfields']['librenmsid'].'&type=port_bits&inverse=1');
		$base64 = base64_encode($imagem);

		$criarHTML = '
		<center>
		<div style="background-color: #fdfdfd;border-style: solid; border-width: 1px; border-color: #d9d9d9; border-radius: 5px;margin:5px 0 10px 0;">
			<h3 style="margin:15px 10px 10px 10px;">Controle de energia</h3>
			<p style="margin:5px 10px 10px 10px;">Estado atual: '.$estado.'</p>
			<button style="color: #3b3b3b;border-style: solid; border-width: 1px; border-color: #c9c9c9; border-radius: 3px; line-height: 35px;margin:5px 5px 15px 0; word-spacing: 3px;" onclick="liga()"><i class="fas fa-play"></i> Ligar</button>
			<button style="color: #3b3b3b;border-style: solid; border-width: 1px; border-color: #c9c9c9; border-radius: 3px; line-height: 35px;margin:5px 5px 15px 0; word-spacing: 3px;" onclick="desliga()"><i class="fas fa-stop"></i> Desligar</button>
			<button style="color: #3b3b3b;border-style: solid; border-width: 1px; border-color: #c9c9c9; border-radius: 3px; line-height: 35px;margin:5px 5px 15px 0; word-spacing: 3px;" onclick="reinicia()"><i class="fas fa-sync-alt"></i> Reiniciar</button>
		</div>
		<div style="background-color: #fdfdfd	;border-style: solid; border-width: 1px; border-color: #d9d9d9; border-radius: 5px;margin:5px 0 10px 0;">
		<h3 style="margin:15px 10px 10px 10px;">Monitoramento de rede (últimas 24 horas)</h3>
		<img style="max-width: 100%;margin:5px 5px 15px 0;" src="data:image/png;base64,'.$base64.'" alt="" />
		</div>
		</center>
		<script>
		function desliga(){
			var alertConfirm = confirm("Você tem certeza que deseja desligar o servidor?\nEste é um desligamento forçado!");
			if(alertConfirm){
				window.location = location.protocol + "//" + location.host + location.pathname + "?action=productdetails&id='.$params['serviceid'].'&modop=custom&a=stop";
			}
		}
		
		function reinicia(){
			var alertConfirm2 = confirm("Você tem certeza que deseja reiniciar o servidor?\nEsta é uma reinicialização forçada!");
			if(alertConfirm2){
				window.location = location.protocol + "//" + location.host + location.pathname + "?action=productdetails&id='.$params['serviceid'].'&modop=custom&a=reboot";
			}
		}

		function liga(){
			var alertConfirm3 = confirm("Você tem certeza que deseja ligar o servidor?");
			if(alertConfirm3){
				window.location = location.protocol + "//" + location.host + location.pathname + "?action=productdetails&id='.$params['serviceid'].'&modop=custom&a=start";
			}
		}
		</script>
		';
	}
	return $criarHTML;	
}

function dedicado_ClientArea($params){ 
	
		return dedicado_HTML($params);
}

function dedicado_AdminServicesTabFields($params){ 

		$fieldsarray = array(
		 'Graph' => '<div style="width:100%" id="tab1"></div>'.dedicado_HTML($params),
		);
		
		return $fieldsarray;
}
function dedicado_AdminCustomButtonArray(){
	return array(
		"Ligar" => "start",
		"Desligar" => "stop",
		"Reiniciar"=> "reboot"
	);
}
function dedicado_ClientAreaCustomButtonArray(){
	return array(
		"Ligar" => "start",
		"Desligar" => "stop",
		"Reiniciar"=> "reboot"
	);
}
function dedicado_start(){
	return 'Esta função está desativada. Contate o suporte para habilita-la.';
}
function dedicado_stop(){
	return 'Esta função está desativada. Contate o suporte para habilita-la.';
}
function dedicado_reboot(){
	return 'Esta função está desativada. Contate o suporte para habilita-la.';
}
?>
