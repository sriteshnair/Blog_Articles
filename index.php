<?php
    session_start();
    include "connexion.php";
    include "functions.php";

if(!empty($_POST["login"]) && !empty($_POST["mdp"])){ 
    $login = FALSE;
    $username = $_POST['login'];
    $password = $_POST['mdp'];
    $query = 'SELECT * FROM user WHERE (login = :name)';
    $values = [':name' => $username];
    try
    {
      $res = $linkpdo->prepare($query);
      $res->execute($values);
    }
    catch (PDOException $e)
    {
      echo 'Query error.';
      die();
    }
    $row = $res->fetch(PDO::FETCH_ASSOC);
    if (is_array($row))
    {
      if (password_verify($password, $row['mdp']))
      {
        $login = TRUE;
        #$_SESSION['active'] = time() + 60;
      }
    }
        
    if($login) {
              $_SESSION['valid'] = true;
              $_SESSION['role'] = $role;
              $newURL = 'clientAPIRest_Serveur.php';
              header('Location: '.$newURL);
        }
}
    ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleIndex.css">
    <title>Accueil</title>
</head>
<body>
    
    <center><h1>Login : Blog</h1></center>
    <form action="" method="post">
        <div class="loginbox">
            <center><h2>Connectez-vous</h2></center>
            <label for="login">Login</label><br>
            <input type="text" name="login"><br>
            <label for="mdp">Mot de Passe</label><br>
            <input type="password" name="mdp"><br>
            <input type="submit" value="Envoyer"><br>
            <h3 style="color:red;"></h3>
        </div>  
    </form>

</body>
</html>

<style>   
Body {  
    font-family: Calibri, Helvetica, sans-serif;
}  
input[type=submit] {   
       background-color: #4CAF50;     
        padding: 15px;   
        margin: 10px 0px;   
        border: none;   
        cursor: pointer;   
         }   
 form {   
        border: 3px solid #f1f1f1;   
    }   
 input[type=text], input[type=password] {   
        width: 25%;   
        margin: 8px 0;  
        padding: 12px 20px;   
        display: inline-block;   
        border: 2px solid green;   
        box-sizing: border-box;   
    }  
    input[type=submit]:hover {   
        opacity: 0.8;   
    }   

 .container {
        padding: 25px;   
        background-color: lightblue;  
    }   
</style>

<!-- 
    moderator1 : $iutinfo
    moderator2 : mod2
    publisher1 : pub1
    publisher2 : pub2
-->