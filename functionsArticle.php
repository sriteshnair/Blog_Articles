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
        $matchingData = $select -> fetch(PDO::FETCH_ASSOC);
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
        $matchingData = $select -> fetch(PDO::FETCH_ASSOC);
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
            GROUP_CONCAT(DISTINCT u2.username) AS users_like,
            (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = -1) AS dislikes,
            GROUP_CONCAT(DISTINCT u3.username) AS users_dislike
            FROM article a
            INNER JOIN user u ON a.login = u.login
            LEFT JOIN Liker l1 ON a.id_article = l1.id_article AND l1.typeLike = 1
            LEFT JOIN user u2 ON l1.login = u2.login
            LEFT JOIN Liker l2 ON a.id_article = l2.id_article AND l2.typeLike = -1
            LEFT JOIN user u3 ON l2.login = u3.login
            WHERE a.id_article = ?
            GROUP BY a.id_article, u.username, a.date_pub, a.contenu";
    $select = $linkpdo->prepare($query);
    if (!$select) {
        $error = $linkpdo->errorInfo();
        echo "Erreur execution requete: " . $error[2];
        return false;
    } else {
        $select->execute(array($id));
        $matchingData = $select -> fetch(PDO::FETCH_ASSOC);
        return $matchingData;
    }
}

function getAllArticleMod($linkpdo){
        $req = $linkpdo->query("SELECT u.username, a.date_pub, a.contenu,
                                (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = 1) AS likes,
                                GROUP_CONCAT(DISTINCT u2.username) AS users_like,
                                (SELECT COUNT(*) FROM Liker WHERE id_article = a.id_article AND typeLike = -1) AS dislikes,
                                GROUP_CONCAT(DISTINCT u3.username) AS users_dislike
                                FROM article a
                                INNER JOIN user u ON a.login = u.login
                                LEFT JOIN Liker l1 ON a.id_article = l1.id_article AND l1.typeLike = 1
                                LEFT JOIN user u2 ON l1.login = u2.login
                                LEFT JOIN Liker l2 ON a.id_article = l2.id_article AND l2.typeLike = -1
                                LEFT JOIN user u3 ON l2.login = u3.login
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


function postPublisher($linkpdo,$contenu,$auteur){
    if ((!empty($contenu)) || (!empty($auteur))){
        /// Traitement
        $datepub = date('y/m/d h:i:s');
        $query = "INSERT INTO article VALUES (?,?,?,?,?)";
        $insert = $linkpdo->prepare($query);
        $insert->execute(array(0,$datepub,null,$contenu,$auteur));
        /// Envoi de la réponse au Client
        deliver_response(201, "INSERT POST OK", NULL);
    } else {
        deliver_response(411, "CHAMP(S) NON-RESEIGNE(S)", NULL);
    }
}

function putPublisher($linkpdo,$date_modif,$contenu,$id){
    $query = "UPDATE article SET date_modif = ?, contenu = ? WHERE id_article = ?";
    $update = $linkpdo->prepare($query);
    $update->execute(array($date_modif,$contenu,$id));
    /// Envoi de la réponse au Client
    if ($update){
        deliver_response(200, "UPDATE PUT OK".implode($update->errorInfo()), NULL);
    }else{
        deliver_response(400, "UPDATE ERROR", NULL);
    }
}

function deletePubMod($linkpdo,$id){
    $query = "DELETE FROM article WHERE id_article = ?";
    $delete = $linkpdo->prepare($query);
    $delete->execute(array($id));
    return $delete;
}

?>