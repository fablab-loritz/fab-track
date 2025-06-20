<?php 
$host_bdd = 'localhost';
$user_bdd = 'root';
$pass_bdd = '';
$base_bdd = 'gestion';
$port_bdd = '3306';

try 
{
	$bdd = new PDO('mysql:host='.$host_bdd.';port='.
	$port_bdd.';dbname='.$base_bdd, $user_bdd, $pass_bdd);
}
catch (Exeption $e)
{
	die('erreur : ' . $e->getMessage());
}
?>