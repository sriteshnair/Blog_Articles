<?php
////////////////// Cas des méthodes GET et DELETE //////////////////
$result = file_get_contents(
    'http://www.kilya.biz/api/chuckn_facts.php',
    false,
    stream_context_create(array('http' => array('method' => 'GET'))) // ou DELETE
);

$r = json_decode($result , true);

if (isset($_POST['submit'])){
  ////////////////// Cas des méthodes POST et PUT //////////////////
  /// Déclaration des données à envoyer au Serveur
  $phrase = $_POST['phrase'];
  $data = array("phrase" => $phrase);
  $data_string = json_encode($data);
  /// Envoi de la requête
    $result = file_get_contents(
  'http://www.kilya.biz/api/chuckn_facts.php',
    false,
    stream_context_create(array(
  'http' => array('method' => 'POST', // ou PUT
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
    <title>Chuck Norris :)</title>
</head>
<body>

<h1><center>Ajouter une phrase !</center></h1>

<form method="post" action="clientAPIRest.php">
  <label for="phrase">Phrase:</label>
  <input type="text" name="phrase">
  <input type="submit" name = "submit" value="Submit">
</form>

<br>
    <h1><center>Chuck Norris REST API : Affichage</center></h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Phrase</th>
            <th>Vote</th>
            <th>Date Ajout</th>
            <th>Date Modifie</th>
            <th>Faute</th>
            <th>Signalement</th>
            <th>Modification</th>
            <th>Suppression</th>
        </tr>
    </thead>
    <tbody>
    <?php
	foreach($r['data'] as $chuck ){
		echo 
        '<tr>'.
            '<td>'.$chuck['id'].'</td>'.
            '<td>'.$chuck['phrase'].'</td>'.
			'<td>'.$chuck['vote'].'</td>'.
			'<td>'.$chuck['date_ajout'].'</td>'.
			'<td>'.$chuck['date_modif'].'</td>'.
			'<td>'.$chuck['faute'].'</td>'.
			'<td>'.$chuck['signalement'].'</td>'.
			'<td><a href="updateChucknFact.php?id='.$chuck['id'].'&phrase='.$chuck['phrase'].'">Modifier</a></td>'.
      '<td><a href="deleteChucknFact.php?id='.$chuck['id'].'">Supprimer</a></td>'.
			'<td>'.$chuck['signalement'].'</td>'.
        '</tr>';
	}?>
  </tbody>
</table>
</body>
</html>
<style>
    table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #999;
}

th {
  background-color: #ddd;
  text-align: left;
  padding: 8px;
  border-bottom: 1px solid #999;
}

td {
  background-color: #fff;
  text-align: left;
  padding: 8px;
  border-bottom: 1px solid #999;
  border-right: 1px solid #999;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

td:hover {
  background-color: #d9edf7;
}

form {
  width: 500px;
  margin: 50px auto;
  padding: 30px;
  background-color: #f2f2f2;
  border-radius: 10px;
}

label {
  font-weight: bold;
  margin-top: 10px;
  display: block;
}

input[type="text"], input[type="number"], input[type="date"] {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  font-size: 16px;
  border-radius: 5px;
  border: 1px solid #999;
}

input[type="submit"] {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #4CAF50;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}


</style>