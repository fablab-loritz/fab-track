<?php
session_start();
?> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Page créée par Matheo Pereira -->
    <meta charset="utf-8">
    <title>page login </title>
    <link rel="stylesheet" href="../tp1/styleTp1.css"/>
</head>
<body>
	<div aligne ="center">
	<h1> Formulaire d'inscription</h1>
<?php 
if (isset($_SESSION['log']))
{
$log = $_SESSION['log'];
echo "<p> Bonjour ".$log." Vous etes deja connecté </p>";
}
else
{
if (isset($_POST['log']))
	{	$log = $_POST['log'];
		$mdp = $_POST['mdp'];
		echo "<h1> Bonjour vous etes connecté </h1>";
		?>
		
	<a href="deconnexion.php"
><button>deconnexion</button></a>
	<?php 
		if (($log == 'pommes') AND($mdp == 'pommes')) //mot de passe en dur a modifier 
		{
		$_SESSION['log'] = $log;
		$_SESSION['droit'] = 1;
		}
	}
	else 
	{ echo "<p> erreur lors de l'enregistrement !</p>";
?>
<h1> Page de connection <br> Bonjour !</h1>
<form method="post" >
Entrez  votre pseudo : <input type="text" name = "log"><br>
Entrez votre mot de passe : <input type="password" name = "mdp">
<br><input type="submit" value="OK">
</form>
<?php
	}
}
?> 
<form action="menu.php" method="post">
	<p>
<a href="menu.php"><button>retour</button></a>
</body>
</html>