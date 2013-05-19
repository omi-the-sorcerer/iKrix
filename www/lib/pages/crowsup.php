<?
/*----------------------------------------------------------------------------
	Cargar el contenido para envio de Whatsapps
----------------------------------------------------------------------------*/
if(isset($_POST['name']))
{
	$_SESSION['name'] = $_POST['name'];

	if(isset($_POST['phone']))
		$_SESSION['phone'] = $_POST['phone'];
}

if(isset($_POST['logout']))
{
	unsets('name');
	unsets('running');
	
	if(isset($_POST['phone']))
		unsets('phone');
}

$content .= view('modules/crowsup');