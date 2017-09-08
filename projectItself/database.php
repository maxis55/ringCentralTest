<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.07.2017
 * Time: 11:48
 */

    // database credentials
    $host = "localhost";
    $db_name = "ringcentraltest";
    $username = "root";
    $password = "123456";


    // get the database connection
        try{
            //require once everytime and make a lot of connections or make static property?
            $dbconnection = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
