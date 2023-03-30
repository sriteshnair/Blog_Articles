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
} else {
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
                        $login = $_GET['login'];
                        $matchingData = getMyArticlePub($linkpdo, $login);
                    // Consulter tous les articles
                    } else {
                        $matchingData = getAllArticlePub($linkpdo);
                    }
                    deliver_response(200, "GET OK : DATA SENT !", $matchingData);
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
                    deliver_response(200, "GET OK : DATA SENT !", $matchingData);
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
                    deliver_response(200, "GET OK : DATA SENT !", $matchingData);
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

function getOneArticleAnon($linkpdo, $id){
    $query = "SELECT u.username, a.date_pub, a.contenu 
              FROM article a, user u
              WHERE a.login = u.login
              AND a.id_article = ?";
    $select = $linkpdo->prepare($query);
    if (!$select) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $select->execute(array($id));
        $matchingData = $select -> fetch();
        return $matchingData;
    }
}

function getAllArticleAnon($linkpdo){
    $req = $linkpdo->query("SELECT u.username, a.date_pub, a.contenu 
                            FROM article a, user u
                            WHERE a.login = u.login");
    if (!$req) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $req->execute(array());
        $matchingData = $req->fetchAll(PDO::FETCH_ASSOC);
        return $matchingData;
    }
}

function getOneArticlePub($linkpdo, $id){
    $query = "SELECT u.username, a.date_pub, a.contenu,
              (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = 1) AS likes,
              (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = -1) AS dislikes
              FROM article a, user u
              WHERE a.login = u.login
              AND a.id_article = ?";
    $select = $linkpdo->prepare($query);
    if (!$select) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $select->execute(array($id));
        $matchingData = $select -> fetch();
        return $matchingData;
    }
}

function getAllArticlePub($linkpdo){
    $req = $linkpdo->query("SELECT u.username, a.date_pub, a.contenu,
                            (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = 1) AS likes,
                            (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = -1) AS dislikes
                            FROM article a, user u
                            WHERE a.login = u.login");
    if (!$req) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $req->execute(array());
        $matchingData = $req->fetchAll(PDO::FETCH_ASSOC);
        return $matchingData;
    }
}

function getMyArticlePub($linkpdo, $login){
    $query = "SELECT u.username, a.date_pub, a.contenu,
              (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = 1) AS likes,
              (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = -1) AS dislikes
              FROM article a, user u
              WHERE a.login = u.login
              AND a.login = ?";
    $select = $linkpdo->prepare($query);
    if (!$select) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $select->execute(array($login));
        $matchingData = $select -> fetchAll(PDO::FETCH_ASSOC);
        return $matchingData;
    }
}

function getOneArticleMod($linkpdo, $id){
    $query = "SELECT u.username, a.date_pub, a.contenu,
              (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = 1) AS likes,
              GROUP_CONCAT(DISTINCT l1.login) AS users_like,
              (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = -1) AS dislikes
              GROUP_CONCAT(DISTINCT l2.login) AS users_dislike
              FROM article a
              INNER JOIN user u ON a.login = u.login
              LEFT JOIN Liker l1 ON a.id_article = l1.id_article AND l1.typeLike = 1
              LEFT JOIN Liker l2 ON a.id_article = l2.id_article AND l2.typeLike = -1
              WHERE a.id_article = ?
              GROUP BY a.id_article, u.username, a.date_pub, a.contenu";
    $select = $linkpdo->prepare($query);
    if (!$select) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $select->execute(array($id));
        $matchingData = $select -> fetch();
        return $matchingData;
    }
}

function getAllArticleMod($linkpdo){
    $req = $linkpdo->query("SELECT u.username, a.date_pub, a.contenu,
                            (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = 1) AS likes,
                            GROUP_CONCAT(DISTINCT l1.login) AS users_like,
                            (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = -1) AS dislikes,
                            GROUP_CONCAT(DISTINCT l2.login) AS users_dislike
                            FROM article a
                            INNER JOIN user u ON a.login = u.login
                            LEFT JOIN Liker l1 ON a.id_article = l1.id_article AND l1.typeLike = 1
                            LEFT JOIN Liker l2 ON a.id_article = l2.id_article AND l2.typeLike = -1
                            GROUP BY a.id_article, u.username, a.date_pub, a.contenu");
    if (!$req) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $matchingData = $req->fetchAll(PDO::FETCH_ASSOC);
        return $matchingData;
    }
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