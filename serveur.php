<?php
/// Librairies éventuelles (pour la connexion à la BDD, etc.)
include('connexion.php');
include('jwt_utils.php');
include('functionsArticle.php');

/// Paramétrage de l'entête HTTP (pour la réponse au Client)
header("Content-Type:application/json");

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

$verification = false;
$bearer_token = '';
$bearer_token = get_bearer_token();

if(is_jwt_valid($bearer_token)){
    $verification = true;
    #Get User's Role from Payload
    $payload = getPayloadFromToken($bearer_token);
    $role = $payload->role;
    $login = $payload->login;
}else{
    $role = "Anonymous";
}

    switch ($http_method) {
        /// Cas de la méthode GET
        case "GET":
            switch($role){
                case "Publisher":
                    /// Consulter un article
                    if (!empty($_GET['id'])) {
                        $id = $_GET['id'];
                        $matchingData = getOneArticlePub($linkpdo, $id);
                    // Consulter mes articles
                    } elseif (!empty($_GET['login'])){
                        if ($_GET['login']==$login) {
                            $matchingData = getMyArticlePub($linkpdo, $login);
                        } else {
                            deliver_response(403, "GET ERROR : PLEASE ENTER YOUR LOGIN", null);
                            break;
                        }
                        
                    // Consulter tous les articles
                    } else {
                        $matchingData = getAllArticlePub($linkpdo);
                    }
                    
                    if ($matchingData == false){
                        deliver_response(404, "GET ERROR : NO DATA FOUND !", null);
                    }else{
                        deliver_response(200, "GET OK : DATA SENT !", $matchingData);
                    }
                    break;

                case "Moderator":
                    /// Consulter un article
                    if (!empty($_GET['id'])) {
                        $id = $_GET['id'];
                        $matchingData = getOneArticleMod($linkpdo, $id);
                    // Consulter tous les articles
                    } else {
                        $matchingData = getAllArticleMod($linkpdo);
                    }
                    if ($matchingData == false){
                        deliver_response(404, "GET ERROR : NO DATA FOUND !", null);
                    }else{
                        deliver_response(200, "GET OK : DATA SENT !", $matchingData);
                    }
                    break;

                default:
                    /// Consulter un article
                    if (!empty($_GET['id'])) {
                        $id = $_GET['id'];
                        $matchingData = getOneArticleAnon($linkpdo, $id);
                    // Consulter tous les articles
                    }else{
                        $matchingData = getAllArticleAnon($linkpdo);
                    }
                    if ($matchingData == false){
                        deliver_response(404, "GET ERROR : NO DATA FOUND !", null);
                    }else{
                        deliver_response(200, "GET OK : DATA SENT !", $matchingData);
                    }
                    break;
            }
            break;

        /// Cas de la méthode POST
        case "POST":
            switch($role){
                case "Publisher":
                    //Traitement Publisher
                    ini_set("allow_url_fopen", true);
                    /// Récupération des données envoyées par le Client
                    $postedData = file_get_contents('php://input');
                    $data = (array) json_decode($postedData,True);
                    $contenu = $data['contenu'];
                    $auteur = $login;
                    postPublisher($linkpdo,$contenu,$auteur);
                    break;
                default:
                    //Traitement Moderator et Anonymous
                    deliver_response(403, "ERREUR : Pas de droit de publication", NULL);
                    break;
            }
            break;
        /// Cas de la méthode PUT
        case "PUT":

            switch($role){
                case "Publisher":
                    //Traitement moderator
                    ini_set("allow_url_fopen", true);
                    /// Récupération des données envoyées par le Client
                    $postedData = file_get_contents('php://input');
                    $postedData = (array) json_decode($postedData,True);

                    $id = $postedData['id'];
                    $date_modif = date('y/m/d h:i:s');
                    $contenu = $postedData['contenu'];

                    if(empty($id) || empty($contenu)){
                        deliver_response(403, "ERREUR : Les champs doivent tous être renseignés", NULL);
                    }else{
                        // Traitement
                        putPublisher($linkpdo,$date_modif,$contenu,$id);
                    }

                    break;
                default:
                    //Traitement d'autres roles
                    deliver_response(403, "ERREUR : Pas de droit de UPDATE", NULL);
                    break;
            }

            break;
            /// Cas de la méthode DELETE
        case "DELETE":
            switch($role){
                case "Publisher" || "Moderator":
                    //Traitement des roles Publisher et Moderator
                    if (!empty($_GET['id'])) {
                        /// Traitement
                        $id = $_GET['id'];
                        $delete = deletePubMod($linkpdo,$id);
                    }
                    /// Envoi de la réponse au Client
                    if($delete){
                        deliver_response(200, "DELETE OK : ".implode($delete->errorInfo()), NULL);
                    }
                    break;
                default:
                    //Traitement d'autres roles
                    deliver_response(403, "ERREUR : Pas de droit de DELETE", NULL);
                    break;
        }
        break;
    }

?>