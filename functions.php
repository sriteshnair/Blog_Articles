<?php 
    function verifyPasswordDB($id,$password){
        include('connexion.php');
        $query = 'SELECT * FROM user WHERE (login = :name)';
        $values = [':name' => $id];
        try{
            $res = $linkpdo->prepare($query);
            $res->execute($values);
        }
        catch (PDOException $e){
            echo 'Query error.';
            die();
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $row['mdp']))
        {
            return true;
        }else{
            return false;
        }
    }

    function getRole($id){
        include('connexion.php');
        $query = 'SELECT * FROM user WHERE (login = :name)';
        $values = [':name' => $id];
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
        return $row['role'];
    }

?> 
