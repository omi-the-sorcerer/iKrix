<?
session_start();
require_once '../whatsapp/whatsprot.class.php';


$upload_folder ='/var/www/lib/whatsapp/pictures';
$nombre_archivo = $_FILES['archivo']['name'];
$tipo_archivo = $_FILES['archivo']['type'];
$tamano_archivo = $_FILES['archivo']['size'];
$tmp_archivo = $_FILES['archivo']['tmp_name'];
$archivador = $upload_folder . '/' . $nombre_archivo;
if(move_uploaded_file($tmp_archivo, $archivador))
{
	echo $archivador;
}
else
{
	echo $tipo_archivo;
}
?>