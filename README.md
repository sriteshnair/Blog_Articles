Ceci s'agit d'un API REST qui traite la gestion des articles de blogs

# Notice d'utilisation

## Liste de Logins et MDP
1.  login : moderator1
    mdp : $iutinfo
2.  login : moderator2
    mdp : $iutinfo2
3.  login : moderator3
    mdp : $iutinfo3
4.  login : publisher1
    mdp : pub1
5.  login : publisher2
    mdp : pub2
6.  login : publisher3
    mdp : pub3

## Projet
Ce projet est composé de 3 serveurs API :
1. *serveur.php* qui gère les requetes sur les articles
2. *serveurLike.php* qui gère les requetes sur les likes
3. *serveurJeton.php* qui gère la création des jetons JWT

## Serveurs
### Le serveur d'*Article* permet aux utilisateurs d'avoir acces (Selon les roles spécifiés):
- à 3 requetes *GET*
    --> En précisant l'ID pour les données d'un article
    --> En précisant son login pour les articles propres à un publisher
    --> Sans ID : Toutes les articles
- à la requete *POST*. Le champ à préciser est : "contenu"
- à la requete *PUT*. Les champs obligatoires à préciser sont : "id", "date_pub", "contenu"
- à la requete *DELETE*. En précisant l'ID pour supprimer l'article

### Le serveur *Like* permet aux utilisateurs d'avoir acces (Selon les roles spécifiés):
- à la requete *POST*. Les champs obligatoires à préciser sont : "typeLike" et "id_article"
- à la requete *PUT*. Les champs obligatoires à préciser sont : "typeLike" et "id_article" (pour changer de *Like à Dislike* ou *Dislike --> Like*)
- à la requete *DELETE*. En précisant ID pour supprimer le like (ni like ni dislike)
> ***Pour typeLike : Like = 1, Dislike = -1***

### Le serveur *Jeton* permet aux utilisateurs de récuperer son jeton après l'identification :
- Requete *POST*, Les champs obligatoires à préciser sont : "id" et "mdp"
Le payload d'un jeton est composé des champs :
- login : login de l'utilisateur (e.g publisher1) 
- role : son role (e.g Publisher)
- exp : temps d'expiration de 5 minutes

# Exemples d'URLs et parametres:

## serveur.php (Articles)

### *GET* :
URLs : 
- http://localhost/R4.01/blog/serveur.php
- http://localhost/R4.01/blog/serveur.php/?id=3
- http://localhost/R4.01/blog/serveur.php?login=publisher1

### *POST* :
- URL : http://localhost/R4.01/blog/serveur.php
- Donnée : {"contenu" : "Ceci est un test"}

### *PUT* :
- URL : http://localhost/R4.01/blog/serveur.php
- Donnée : {"id" : 1,"date_pub" : "2022-02-01","contenu" : "Cet article est edité"}
//À changer : TAK BOLEH TUKAR DATE PUB

### *DELETE* :
- URL : http://localhost/R4.01/blog/serveur.php/?id=3


## serveurLike.php (Likes)

### *POST* :
- URL : http://localhost/R4.01/blog/serveurLike.php
- Donnée : {"typeLike" : 1,"id_article" : 6}

### *PUT* :
- URL : http://localhost/R4.01/blog/serveurLike.php
- Donnée : {"typeLike" : -1,"id_article" : 6}

### *DELETE* :
- URL : http://localhost/R4.01/blog/serveurLike.php/?id=3

## Tips
Pour tester chaque requete, il faut recuperer son jeton du serveurJeton en envoyant une requete POST avec son id et mot de passe. Copier le jeton et coller dans la partie Authorization sur Postman API. Les fichiers Postman API sont également disponibles dans le projet.

Le lien GITHUB pour ce projet : ...

# Bonne utilisation!