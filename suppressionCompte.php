//ce morceau de code permet de supprimer le compte de la personne qui l'a demandé
//le code a été modifié de l'original à titre explicatif

<?php
    session_start();

    if (!isset($_POST["compteSup"])){//ici on n'a l'ID du compte a supprimé.
        echo "<h1>Vous n'avez rien à faire là.</h1>";
        exit();
    }
    

    //on vérifie si le propriétaire du compte est bien celui connecté
    if ($_SESSION['id'] == $_POST["compteSup"]){
   	 include('connexionPDO.php');

    	//ici on supprime tous avec des requêtes préparées

	$result = $pdoUsers->prepare('delete from InfosUsers where ID=?');
    	$result->execute([$_SESSION['id']]);
    	$result = $pdoUsers->prepare('delete from Users where ID=?');
    	$result->execute([$_SESSION['id']]);
	
	//cela permet de confirmer à la requête Ajax que tout est bon
    	echo "true";

    }else

        echo "<h1>Vous n'avez rien à faire là.</h1>";
    
?>