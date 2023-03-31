<?php
/// Librairies éventuelles (pour la connexion à la BDD, etc.)
include('connexion.php');
include('jwt_utils.php');
include('functionLike.php');

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
        /// Cas de la méthode POST
        case "POST":
            switch($role){
                case "Publisher":
                    //Traitement Publisher
                    ini_set("allow_url_fopen", true);
                    /// Récupération des données envoyées par le Client
                    $postedData = file_get_contents('php://input');
                    $data = (array) json_decode($postedData,True);
                    $typeLike = $data['typeLike'];
                    $id_article = $data['id_article'];
                    putLikePub($linkpdo,$typeLike,$id_article,$login);
                    break;
                default:
                    //Traitement Moderator et Anonymous
                    deliver_response(403, "ERREUR : PAS DE DROIT DE PUBLICATION (LIKE)", NULL);
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
                    $data = (array) json_decode($postedData,True);

                    $typeLike = $data['typeLike'];
                    $id_article = $data['id_article'];

                    if(empty($id_article) || empty($typeLike)){
                        deliver_response(403, "ERREUR : Les champs doivent tous être renseignés", NULL);
                    }else{
                        // Traitement
                        $query = "UPDATE liker SET typeLike = ? WHERE id_article = ? and login = ?";
                        $update = $linkpdo->prepare($query);
                        $update->execute(array($typeLike,$id_article,$login));
                        /// Envoi de la réponse au Client
                        if ($update){
                            deliver_response(200, "UPDATE PUT OK".implode($update->errorInfo()), NULL);
                        }else{
                            deliver_response(400, "UPDATE ERROR", NULL);
                        }
                    }
                    break;
                default:
                    //Traitement d'autres roles
                    deliver_response(403, "ERREUR : Pas de droit de PUT", NULL);
                    break;
            }
            break;
            /// Cas de la méthode DELETE
        case "DELETE":
            switch($role){
                case "Publisher":
                    //Traitement des roles Publisher et Moderator
                    if (!empty($_GET['id'])){
                        /// Traitement
                        $id = $_GET['id'];
                        $delete = deletePubLike($linkpdo,$id,$login);
                    }else{
                        deliver_response(404, "DELETE ERREUR : DATA NOT FOUND", NULL);
                    }
                    /// Envoi de la réponse au Client
                    if($delete){
                        deliver_response(200, "DELETE OK : ".implode($delete->errorInfo()), NULL);
                    }else{
                        deliver_response(400, "DELETE ERREUR : ".implode($delete->errorInfo()), NULL);
                    }
                    break;
                default:
                    //Traitement d'autres roles
                    deliver_response(403, "ERREUR : Pas de droit de UPDATE", NULL);
                    break;
        }
        break;
    }

?>