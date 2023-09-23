<?php

/*ce fichier est appelé quand le js envoie une requête ajax en asynchrone pour changer
le mot de passe, donc on récupère les informations de la page pour modifier le mot de passe 
le changement de mot de passe, se fait par une question à répondre sur l'utilisateur, son mail et son pseudo
(le code a été modifié a des fins d'explications pour le site)
*/
    //on vérifie si les informations ne sont pas vide
    if (array_key_exists('login', $_POST) && array_key_exists('mail', $_POST) && array_key_exists('question', $_POST) && array_key_exists('password', $_POST)){
        
	//on regarde si le nouveau mot de passe n'est pas trop long
        if (strlen($_POST['password']) < 6 && strlen($_POST['password']) > 25){
            echo 'Le mot de passe doit faire entre 6 et 25 caractères';
            exit;
        }
        
	//on encode les caractères avec une méthode spéciale pour enlever les failles XSS et SQL
        $login = encodage($_POST['login']);
        $mail = encodage($_POST['mail'], ENT_QUOTES, 'UTF-8');
        $question = encodage($_POST['question'], ENT_QUOTES, 'UTF-8');
        

	//on hache le mot de passe pour ne pas l'avoir en brupte
        $passwordE= hashageSecurise(encodage($_POST['password']));
        
        
        //récupération de l'id, chaque compte possède un ID unique 
        include('connexionPDO.php');
        
        $requete = 'select Identifiant from Accounts where Login=? and Mail=?';
        $id = $pdoUsers->prepare($requete);
        $work = $id->execute([$login, $mail]);

	//si erreur dans la requête ne marche pas
        if (!$work){
            echo "Connexion impossible";
            exit;
        }
        
        $id = $id->fetchAll();
	//aucun compte avec ces logins et mails
        if (count($id) == 0){
            echo "Compte introuvable";
            exit;
        }
        $id = $id[0]['ID'];
        
        //vérification de la question
        $requete = 'select reponse from securitesQuestions where ID=?';
        $result = $pdoUsers->prepare($requete);
        $work = $result->execute([$id]);
        if (!$work){
            echo "Compte introuvable";
            exit;
        }

        $result = $result->fetchAll();
        if (count($result) == 0){
            echo "Réponse introuvable";
            exit;
        }
       
        $reponse = $result[0]['reponse'];

	//ici si la réponse est == a la reponse (insensible à la casse=
        if (strtolower($reponse) == strtolower($question)){//on peut changer le mdp
            $requete = 'update Accounts set Password = ? where ID=?';
            $result = $pdoUsers->prepare($requete);
            
            $work = $result->execute([$passwordE, $id]);
            
           
            if (!$work){
                echo "Impossible de changer le mot de passe";
                exit;
            }else {//réussite
                echo "Success";
            }
        }else{
            echo "Réponse incorrecte";
        }
        
    }else 
        echo "Toutes les valeurs ne sont pas remplies";



?>