<?php
session_start();
?>
<html lang="fr">
<head> <!-- codage en HTML 5 et CSS3 -->
<meta charset="utf-8" />
<title>Deconnexion !</title>
<link rel="stylesheet" href="Style/sql.css" />
</head>
<body>
<?php
if ( isset($_SESSION['log'])) // vérif si connexion existe avant de détruire la session 
{
$log = $_SESSION['log'];

unset ($_SESSION['log']);
session_destroy();

if ( isset($_SESSION['log']))
echo "<p> Pb deconnexion </p>";
else
echo "<p> Au revoir ".$log." Vous êtes déconnecté </p>";

}
else {echo "<p> Erreur pas de session ouverte </p>";
}

?>

<!-- Bouton de retour à la page menu -->
<form action="menu.php" method="post">
<p>
</p>
</form>
</body>
</html>