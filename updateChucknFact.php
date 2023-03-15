<?php
$id = $_GET['id'];
$result = file_get_contents(
  'http://localhost/r401/jeton/serveur.php?id='.$id,
  false,
  stream_context_create(array('http' => array('method' => 'GET')))
);

$r = json_decode($result, true);
$r = array_filter($r, 'ctype_alnum', ARRAY_FILTER_USE_KEY);

print_r($r);

if (isset($_POST['submit'])){
////////////////// Cas des méthodes POST et PUT //////////////////
  /// Déclaration des données à envoyer au Serveur
  $phrase = $_POST['phrase'];
  $data = array("id" => $id, "phrase" => $phrase);
  $data_string = json_encode($data);
  /// Envoi de la requête
    $result = file_get_contents(
  'http://localhost/r401/jeton/serveur.php?id='.$id,
    false,
    stream_context_create(array(
  'http' => array('method' => 'PUT', // ou PUT
    'content' => $data_string,
    'header' => array('Content-Type: application/json'."\r\n"
    .'Content-Length: '.strlen($data_string)."\r\n"))))
  );
  /// Dans tous les cas, affichage des résultats
  echo '<pre>' . htmlspecialchars($result) . '</pre>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification phrase ChucknFacts</title>
</head>
<body>
    <form action="" method="post">
    <label for="id">ID:</label>
    <input type="number" name="id" value="<?php echo $r['data']['id']?>" readonly>
    <label for="phrase">Phrase:</label>
    <input type="text" name="phrase" value="<?php echo $r['data']['phrase']?>">
    <input type="submit" name = "submit" value="Submit">
    </form>

    <input type="button" value="Retour" onclick="window.location='clientAPIRest_Serveur.php';">
</body>
</html>

<style>
    body {
            font-family: Arial, sans-serif;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
</style>