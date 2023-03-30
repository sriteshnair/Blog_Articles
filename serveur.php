<?php
/// Librairies éventuelles (pour la connexion à la BDD, etc.)
include('connexion.php');
include('jwt_utils.php');

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
}

if(!$verification){
    deliver_response(498, "INVALID TOKEN", NULL);
}else{
    switch ($http_method) {
            /// Cas de la méthode GET
        case "GET":
            /// Récupération des critères de recherche envoyés par le Client
            if (!empty($_GET['id'])) {
                $id = $_GET['id'];
                $query = "SELECT * FROM chuckn_facts WHERE id=?";
                $select = $linkpdo->prepare($query);
                $select->execute(array($id));
                $matchingData = $select -> fetch();
            }else{
                $matchingData = getAll($linkpdo);
            }
            /// Envoi de la réponse au Client
            deliver_response(200, "GET OK : DATA SENT !", $matchingData);
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
                    putPublisher($linkpdo,$contenu,$auteur);
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
                    $datepub = $postedData['date_pub'];
                    $date_modif = date('y/m/d h:i:s');
                    $contenu = $postedData['contenu'];

                    if(empty($id) || empty($datepub) || empty($contenu)){
                        deliver_response(403, "ERREUR : Les champs doivent tous être renseignés", NULL);
                    }else{
                        // Traitement
                        $query = "UPDATE article SET date_pub = ?, date_modif = ?, contenu = ? WHERE id_article = ?";
                        $update = $linkpdo->prepare($query);
                        $update->execute(array($datepub,$date_modif,$contenu,$id));
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
                    deliver_response(403, "ERREUR : Pas de droit de UPDATE", NULL);
                break;
        }
        break;
    }
}


    /// Envoi de la réponse au Client
function deliver_response($status, $status_message, $data)
{
    /// Paramétrage de l'entête HTTP, suite
    header("HTTP/1.1 $status $status_message");
    /// Paramétrage de la réponse retournée
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    echo $json_response;
}

function getAll($linkpdo){
    $req = $linkpdo->query("SELECT * FROM chuckn_facts");
    $req->execute(array());
    $matchingData = $req->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}

function getOne(){
    
}

function putPublisher($linkpdo,$contenu,$auteur){
    if ((!empty($contenu)) && (!empty($auteur))){
        /// Traitement
        $datepub = date('y/m/d h:i:s');
        $query = "INSERT INTO article VALUES (?,?,?,?,?)";
        $insert = $linkpdo->prepare($query);
        $insert->execute(array(0,$datepub,null,$contenu,$auteur));
        /// Envoi de la réponse au Client
        deliver_response(201, "INSERT POST OK", NULL);
    }else{
        deliver_response(411, "CHAMP(S) NON-RESEIGNE(S)", NULL);
    }
}

function deletePubMod($linkpdo,$id){
    $query = "DELETE FROM article WHERE id_article = ?";
    $delete = $linkpdo->prepare($query);
    $delete->execute(array($id));
    return $delete;
}
/*
switch($role){
    case "Moderator":
        //Traitement moderator
        break;
    case "Publisher":
        //Traitement publisher
        break;
    default:
        //Traitement anonymous
        break;
}
*/
?>