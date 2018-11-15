<?php 
    function isRegistration() {
            return (isset($_POST['registration']) && !empty($_POST['login']) && !empty($_POST['password']));
    }
    function isAuthorization() {
            return (isset($_POST['authorization']) && !empty($_POST['login']) && !empty($_POST['password']));
    }
    function createPDO() {
            $pdo = new PDO("mysql:host=localhost;dbname=iegorenkova;charset=utf8", "iegorenkova", "neto1897", [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
            return $pdo;
    }
    function getSalt() {
        return 'S76Mwq2ZwWQXo8sri';
    }
    function getHashPassword($password) {
        return md5($password . getSalt());
    }
?>
