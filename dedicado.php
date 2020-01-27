<?php

include_once(dirname(__FILE__).'/functions.php');

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function dedicado_MetaData()
{
    return array(
        'DisplayName' => 'Servidor Dedicado Hostzone',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => false, // Set true if module requires a server to work
    );
}

function dedicado_ConfigOptions() {
    return [
        "librenms" => [
            "FriendlyName" => "Hostname do LibreNMS",
            "Type" => "text", # Textarea
            "Description" => "Hostname completo para sua instalação do LibreNMS",
            "Default" => "meulibrenms.meudominio.com",
        ],
    ];
}

function dedicado_CreateAccount($params){
		
		$serviceid = $params['serviceid'];
		$username = "sql".$params['serviceid'];
		$senha = $params['password'];
		
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
		
		
	return 'success';
}

function dedicado_SuspendAccount($params){
	
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
		
		
	return 'success';
}
function dedicado_TerminateAccount($params){
	
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
		
		
	return 'success';
}

function dedicado_ClientArea($params) {  
	if (!empty($params['customfields']['librenmsid'])) {
		$imagem=file_get_contents('http://'.$params["configoption1"].'/graph.php?height=250&width=600&id='.$params['customfields']['librenmsid'].'&type=port_bits');
		$base64 = base64_encode($imagem);

		$criarHTML = '<center>
		<h3>Monitoramento de rede (últimas 24 horas)</h3>
		<img src="data:image/png;base64,'.$base64.'" alt="" />
		</center>';
		
		return $criarHTML;	
	}
}
?>
