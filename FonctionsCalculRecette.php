//ici on a une fonction qui s'exécute à intervalles réguliers pour mettre les recettes à jour.
//le code a été modifié de l'original.

function miseAJourCalculRecette(){
        include('connexionPDO.php');
        
        
        $result = $pdoRecette->prepare('select ID, calculMacro,ingredients from Recette');
        $result->execute([]);
        $result = $result->fetchAll();
        
        $nbPersonne = $pdoRecette->prepare('select nbPers from generalInfoRecette');
        $nbPersonne->execute([]);
        $nbPersonne = $nbPersonne->fetchAll();
        
        $ingNonConnus = "";
        for ($i = 0; $i < count($result); $i++){
            $ingredientsArray = explode('[', $result[$i]['ingredients']);
            array_shift($ingredientsArray);
            $kcal = 0;
            $prot = 0;
            $lipide = 0;
            $glu = 0;
            $sucre = 0;
            $sel = 0;
            
            //on va ajouter pour chaque ingrédient
            foreach ($ingredientsArray as $ingredient) {
                $parts = explode(':', $ingredient);
                $valeursIng = $pdoRecette->prepare('select UnEnGramme, glucides,lipides,proteines,sucres,sel 
                from ingredient where nom = ?');
                $valeursIng->execute([$parts[2]]);
                $valeursIng = $valeursIng->fetchAll();
                if (count($valeursIng) != 0){
                    $quantitesGramme = $parts[0];
                    //c. à soupe
                    if ($parts[1] == 2)
                        $quantitesGramme = $parts[0]*15;
                    //c. à café
                    else if ($parts[1] == 3)
                        $quantitesGramme = $parts[0]*5;
                    //aucune unité
                    else if ($parts[1] == 4)
                        $quantitesGramme = floatval($parts[0])*floatval($valeursIng[0]['UnEnGramme']);
                    //pincée
                    else if ($parts[1] == 6)
                        $quantitesGramme = $parts[0]*0.4;
                    

		    //le calcul se faits ici
                    if ($valeursIng[0]['proteines'] != 0)$prot+=floatval($quantitesGramme)*floatval($valeursIng[0]['proteines'])/100;
                    if ($valeursIng[0]['glucides'] != 0)$glu+=floatval($quantitesGramme)*floatval($valeursIng[0]['glucides'])/100;
                    if ($valeursIng[0]['lipides'] != 0)$lipide+=floatval($quantitesGramme)*floatval($valeursIng[0]['lipides'])/100;
                    if ($valeursIng[0]['sel'] != 0)$sel+=floatval($quantitesGramme)*floatval($valeursIng[0]['sel'])/100;
                    if ($valeursIng[0]['sucres'] != 0)$sucre+=floatval(floatval($quantitesGramme)*floatval($valeursIng[0]['sucres']))/100;
                }else{
                    $ingNonConnus.="\n".$parts[2]." Recette : ".$result[$i]['ID'];
                }
            }
            $nbPers = floatval($nbPersonne[$i]['nbPers']);
            $kcal = floatval(4*floatval($prot)+4*floatval($glu)+9*floatval($lipide))/$nbPers;
            
            $prot/=$nbPers;
            $lipide/=$nbPers;
            $glu/=$nbPers;
            $sucre/=$nbPers;
            $sel/=$nbPers;
            
            $nutri = "$kcal;$prot;$lipide;$glu;$sucre;$sel;";
           if ($result[$i]['calculMacro'] != 'F'){
            $buffer = $pdoRecette->prepare('UPDATE `Recette` SET `ValeursNutri` = ? WHERE `Recette`.`ID` = ?'); 
            $buffer->execute([$nutri,$result[$i]['ID']]);
           }
        }

	//on mets dans un fichier txt tous les ingrédients non connus, pour leur rajout plus tard.
        file_put_contents("ingredientsNonConnus.txt", $ingNonConnus);
    }