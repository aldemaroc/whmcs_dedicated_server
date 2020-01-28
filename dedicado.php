<?php
use WHMCS\Database\Capsule;

if(!defined('WHMCS')){
    die('This file cannot be accessed directly');
}

function dedicado_MetaData(){
    return array(
        'DisplayName' => 'Servidor Dedicado Hostzone',
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
	$status = $params['status'];
	$duedate = $params['duedate'];
	$sql = $pdo->query('INSERT INTO tbltodolist (date, title, status, duedate) VALUES ("'.$date.'", "'.$title.'", "'.$status.'", "'.$duedate.'")');
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
	return 'success';
}

function dedicado_SuspendAccount($params){
	// Create TodoItem
	dedicado_CreateTodoItem([
		'date' => date('Y-m-d'),
		'title' => 'Suspensão de Servidor Dedicado - Client ID: '.$params['clientsdetails']['userid'].' - Service ID: '.$params['serviceid'],
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
	return 'success';
}

function dedicado_TerminateAccount($params){
	// Create TodoItem
	dedicado_CreateTodoItem([
		'date' => date('Y-m-d'),
		'title' => 'Cancelamento de Servidor Dedicado - Client ID: '.$params['clientsdetails']['userid'].' - Service ID: '.$params['serviceid'],
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
	return 'success';
}

function dedicado_ClientArea($params){ 
	if(!empty($params['customfields']['librenmsid'])){
		$imagem = file_get_contents('http://'.$params["configoption1"].'/graph.php?height=250&width=600&id='.$params['customfields']['librenmsid'].'&type=port_bits');
		$base64 = base64_encode($imagem);

		$criarHTML = '<center>
		<h3>Monitoramento de rede (últimas 24 horas)</h3>
		<img src="data:image/png;base64,'.$base64.'" alt="" />
		</center>';
		
		return $criarHTML;	
	}
}

function dedicado_AdminServicesTabFields($params){ 
	if(!empty($params['customfields']['librenmsid'])){
		$imagem = file_get_contents('http://'.$params["configoption1"].'/graph.php?height=250&width=600&id='.$params['customfields']['librenmsid'].'&type=port_bits');
		$base64 = base64_encode($imagem);

		$criarHTML = '<center>
		<h3>Monitoramento de rede (últimas 24 horas)</h3>
		<img src="data:image/png;base64,'.$base64.'" alt="" />
		</center>';
		
		$fieldsarray = array(
		 'Graph' => '<div style="width:100%" id="tab1"></div>'.$criarHTML,
		);
		
		return $fieldsarray;
	}
}
?>
