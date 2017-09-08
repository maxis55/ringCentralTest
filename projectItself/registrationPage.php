<?php
require_once "database.php";
if (isset($_REQUEST['registration_name'])) {
    $allnull = true;
    $login =$_REQUEST['registration_name'];
//    $password = intval($_REQUEST['login_pass']); ??
    $password = $_REQUEST['registration_pass'];
    $query = "INSERT INTO user(
            login,
            password) VALUES (
            :login, 
            :password)";
    $stmt = $dbconnection->prepare($query);
    $stmt->execute(array(':login' => $login,':password'=>$password));
    $result=$stmt->fetch();
}
?>