<?
	set_time_limit(0);
	require_once('./includes/constantes.php');
	define('EPSILON', 1E-6);
	require_once('./verif_facture_common.php');
	error_reporting(0);
	ini_set('display_errors','off');
	include('./forecast.php');
	
	$db_process = &ADONewConnection('mysql');
	$db_process->Connect('192.168.1.90', 'ecpsav', 'adminecp', 'PROCESS');
	
	error_reporting(E_ERROR); //desactive les warnings

	if( !$db_oracle)
	{
		$db_oracle = connect_bdd();
	//	@$db_oracle->Connect(false, "testecp", DB_PASSWORD_ORACLE, DB_ORACLE);//on ajoute le @ au cas ou il y aurait des erreurs de connection avec la BDD Oracle						
	}
	
	# Connexion base de donnée SAVECP
	$db_sav = &ADONewConnection(DB_DRIVER_SAV);
	$db_sav->Connect(DB_SERVER_SAV, DB_LOGIN_SAV, DB_PASSWORD_SAV, DBNAME_SAV);
	
	
	$db_stats = &ADONewConnection('mysql');
	$db_stats->Connect('localhost', 'ecpsav', 'adminecp', 'STATS');
	
	# Connexion base de donnée SAVECP
	$db_ecp = &ADONewConnection(DB_DRIVER);
	$db_ecp->Connect(DB_SERVER_DIST, DB_LOGIN, DB_PASSWORD, DBNAME);
	
	
?>
<?
	function date_fr_raccourci($date){
	$annee=substr($date,2,2);
	$mois=substr($date,5,2);
	$jour=substr($date,8,2);
	$retour=$jour."/".$mois."/".$annee;
	return $retour;
}
?>
<head>
</head>

<body>
<?
//pour que la réponse s'affiche comme du texte brut
header('Content-Type: text/plain');
 
/*partie à modifier*/
$name = '/v1/locations/search?';//nom du site
 
//pour ne pas devoir calculer à la main la longueur du corps, on le stocke dans une variable et la fonction strlen() nous la donne.
$data = 'access_token=ACCESS_TOKEN&lat=40.7127&lng=74.0059';
 
//la requête
$envoi  = "GET /v1/locations/search?access_token=ACCESS_TOKEN&lat=40.7127&lng=74.0059";
//$envoi .= "Host: ".$name."\r\n";
$envoi .= "Connection: Close\r\n";
$envoi .= "Content-type: application/x-www-form-urlencoded\r\n";
//$envoi .= "Content-Length: ".strlen($data)."\r\n\r\n";
//$envoi .= $data."\r\n";
/*/partie à modifier*/
 
/*ouverture socket*/
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if($socket < 0){
        die('FATAL ERROR: socket_create() : " '.socket_strerror($socket).' "');
}
 
if (socket_connect($socket,gethostbyname($name),80) < 0){
        die('FATAL ERROR: socket_connect()');
}
/*/ouverture socket*/
 
/*envoi demande*/
if(($int = socket_write($socket, $envoi, strlen($envoi))) === false){
        die('FATAL ERROR: socket_write() failed, '.$int.' characters written');
}
/*/envoi demande*/
 
/*lecture réponse*/
$reception = '';
while($buff = socket_read($socket, 2000)){
   $reception.=$buff;
}
echo $reception;
/*/lecture réponse*/
 
socket_close($socket);
?>

?>
</body>

