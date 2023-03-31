<?php

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

function putLikePub($linkpdo,$typeLike,$id_article,$login){
    if ((!empty($typeLike)) || (!empty($id_article)) || (!empty($login))){
        /// Traitement
        $query = "INSERT INTO liker VALUES (?,?,?)";
        $insert = $linkpdo->prepare($query);
        $insert->execute(array($login,$id_article,$typeLike));
        /// Envoi de la réponse au Client
        deliver_response(201, "INSERT POST OK", NULL);
    }else{
        deliver_response(411, "CHAMP(S) NON-RESEIGNE(S)", NULL);
    }
}

function deletePubLike($linkpdo,$id,$login){
    $query = "DELETE FROM liker WHERE id_article = ? and login = ?";
    $delete = $linkpdo->prepare($query);
    $delete->execute(array($id,$login));
    return $delete;
}

?>