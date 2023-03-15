<?php
        $server='localhost';
        $db ='chucknorris';
        $login='root';
        $mdp='';
        
        try {
            $linkpdo = new PDO("mysql:host=$server;dbname=$db;charset=UTF8", $login, $mdp);
            }
            catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
            }
?>