//Ce fichier permet d'avoir un changement asynchrone de mot de passe.
//pour cela il y a 2 phases : d'abord la vérification du mail et du login par une requête Ajax
//Et une deuxième qui vérifie si la réponse de la question de départ de l'utilisateur est correcte, par exemple
//quel était le prénom de votre premier chien? et si c'est le cas on change de mot de passe

let buttonValid = document.querySelector('#buttonRecup');

buttonValid.addEventListener('click', verifyInfo);

function verifyInfo(){
    let login = document.querySelector('#login').value;
    let mail = document.querySelector('#mail').value;
    
    //envoie de la requête
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "verifyMailAndLogin.php", true);
   
    var formData = new FormData();
    formData.append("login", login);
    formData.append("mail", mail);
    
    xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let erreur = document.querySelector('#Erreur');
                if (erreur != null)
                    erreur.remove();
                console.log(xhr.responseText);
                
		//si pas d'erreur on génère le html de la question / réponse et du nouveau mot de passe
                if (xhr.responseText != "Erreur"){
                    let div = document.createElement('div');
                    div.id='newPassWord';
                    document.querySelector('#wrapper').appendChild(div);
                    
                    
                    //creation des questions
                    
                    let question = document.createElement('p');
                    let tab = ['Quel est le prénom de jeune fille de votre mère ?', 'Quel était le nom de votre premier animal de compagnie ?', 'Quel est le nom de votre prof préféré ?', 'Quel est est le nom de votre premier amour ?', 'Quel est le deuxième prénom de votre père ?', 'Quel était le nom de votre lycée ?'];
                    console.log();
                    question.innerText = tab[parseInt(xhr.responseText.split(',')[1])-1];
                    
                    //création des inputs
                    
                    let reponse = document.createElement('input');
                    reponse.type='text';
                    reponse.name='question';
                    reponse.id='question';
                    
                    let passWord1 = document.createElement('input');
                    let passWord2 = document.createElement('input');
                    passWord1.type='password';
                    passWord2.type='password';
                    passWord1.name='password1';
                    passWord2.name='password2';
                    passWord1.id='password1';
                    passWord2.id='password2';
                    
                    //création des labels
                    let label1 = document.createElement('label');
                    let label2 = document.createElement('label');
                    label1.setAttribute("for", 'passWord1');
                    label2.setAttribute("for", 'passWord2');
                    label1.innerText = 'Nouveau mot de passe';
                    label2.innerText = 'Confirmation du mot de passe';
                    
                    //valider button
                    let changeMDPButton = document.createElement('button');
                    changeMDPButton.innerText = 'Changer le mot de passe';
                    changeMDPButton.id = 'changeMDPBUtton';
                    
                    
                    
                    //Ajout au DOM
                    div.appendChild(question);
                    div.appendChild(reponse);
                    div.appendChild(document.createElement('br'));
                    div.appendChild(label1);
                    div.appendChild(document.createElement('br'));
                    div.appendChild(passWord1);
                    div.appendChild(document.createElement('br'));
                    div.appendChild(label2);
                    div.appendChild(document.createElement('br'));
                    div.appendChild(passWord2);
                    div.appendChild(document.createElement('br'));
                    div.appendChild(changeMDPButton);
                    
                    changeMDPBUtton.addEventListener('click', changeMdp);
                    //supprimer ancien boutton valider
                    let buttonValidMdp = document.querySelector('#buttonRecup');

                    document.querySelector('#buttonRecup').remove();
                    
                }
		//sinon on affiche l'erreur
                else{//erreur
                    
                    let err = document.createElement('p');
                    err.innerText = 'Erreur :  valeurs incorrectes !';
                    err.id = 'Erreur';
                    document.querySelector('#wrapper').appendChild(err);
                   
                }
            }
        };
    
    xhr.send(formData);
    
}


//bouton de fin, on vérifie si la réponse est correcte
function changeMdp(){
    let login = document.querySelector('#login').value;
    let mail = document.querySelector('#mail').value;
    let mdp1 = document.querySelector('#password1').value;
    let mdp2 = document.querySelector('#password2').value;
    let reponse = document.querySelector('#question').value;
    
    if (mdp1.value != mdp2.value){//si les deux mdp sont diff
        let diff = document.createElement('p');
        p.id = 'erreur';
        p.innerText = 'Les mots de passe sont différents';
        document.querySelector('#newPassWord').appendChild(p);
    }else{
        
        //envoie de la requête
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "changePassword.php", true);
   
        var formData = new FormData();
        formData.append("login", login);
        formData.append("mail", mail);
        formData.append("question", reponse);
        formData.append("password", mdp1);
    
        xhr.send(formData);
    
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let err2 = document.querySelector('#Erreur2');
                if (err2 != null)
                    err2.remove();
                console.log(xhr.responseText);
                if (xhr.responseText == 'Success'){
                    let succes = document.createElement('p');
                    succes.innerText = 'Mot de passe changé';
                    document.getElementById('newPassWord').appendChild(succes);
                    
                    let retourConnexion = document.createElement('a');
                    retourConnexion.href = '/compte/connexion';
                    retourConnexion.innerText = 'Retour à la page de connexion';
                    document.querySelector('#newPassWord').appendChild(retourConnexion);
                    let err2TuSup = document.querySelector('#Erreur2');
                    if (err2TuSup != null)
                        err2TuSup.remove();
                }else{//erreur
                    let err2 = document.createElement('p');
                    err2.id = 'Erreur2';
                    err2.innerText = xhr.responseText;
                    document.querySelector('#newPassWord').appendChild(err2);
                }
            }
        };
        
    }
}