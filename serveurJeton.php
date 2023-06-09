<?php
/// Librairies éventuelles (pour la connexion à la BDD, etc.)
include('connexion.php');
include('jwt_utils.php');
include('functions.php');

/// Paramétrage de l'entête HTTP (pour la réponse au Client)
header("Content-Type:application/json");

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method) {
    case "POST":
        ini_set("allow_url_fopen", true);
        /// Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');
        $data = (array) json_decode($postedData,True);
        $id = $data['id'];
        $mdp = $data['mdp'];
        $login = FALSE;

        if(!empty($id) && !empty($mdp)){
            $login = verifyPasswordDB($id,$mdp);
            if($login) {
                $role = getRole($id);
                /// Traitement
                $headers = array('alg' =>'HS256','typ'=>'JWT');
                $payload = array('login'=>$id, 'role' => $role,'exp'=>(time()+300));
                $jeton = generate_jwt($headers,$payload);
                /// Envoi de la réponse au Client
                deliver_response(201, "AUTHENTIFICATION OK !", $jeton);
            }else{
                deliver_response(411, "AUTHENTIFICATION ERROR ! ID/MOT DE PASSE NE SONT PAS BONS !", NULL);
            }  

        }else{
            deliver_response(411, "ID ET MOT DE PASSE DOIT ETRE RENSEIGNE(S)", NULL);
        }
        break;
    default:
        echo "Rien ne s'est passé";
        break;
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

?>