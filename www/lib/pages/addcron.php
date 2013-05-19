<?
/*------------------------------------------------------------------------------
  Función para agregar tarea al cron
------------------------------------------------------------------------------*/

/*---- Cargar Funciones ----*/
require_once(FUNCTIONS.'cron.php');


if(isset($_POST['add']) && isset($_POST['croncommand']))
{
	go_to("cron", commands::add($_POST['croncommand'], @$_POST['d1']));
}

?>