<?php

if(isset($_GET) && !empty($_GET['id'])){
    $id = $_GET['id'];
    ////////////////// Cas des méthodes GET et DELETE //////////////////
    $result = file_get_contents(
        'http://localhost/r401/jeton/serveur.php?id='.$id,
        false,
        stream_context_create(array('http' => array('method' => 'DELETE'))) // ou DELETE
    );

    if($result){
        echo $result;
    }
}
?>
<input type="button" value="Retour" onclick="window.location='clientAPIRest_Serveur.php';">