<?php
require_once "database.php";
if (isset($_REQUEST['login_name'])) {
    $allnull = true;
    $login =$_REQUEST['login_name'];
//    $password = intval($_REQUEST['login_pass']); ??
    $password = $_REQUEST['login_pass'];
    $query = "SELECT * FROM user WHERE login=:login";
    $stmt = $dbconnection->prepare($query);
    $stmt->execute(array(':login' => $login));
    $result=$stmt->fetch();
    if ($result['password']===$password) //simple auth, no need for hash+salt in test project
        echo "success";
    else
        echo "fail";
}
?>