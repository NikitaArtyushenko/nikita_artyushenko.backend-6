<?php
    $del_id = $_GET['id_record'];
    $user = 'u52858';
    $password = '6454527';
    $db = new PDO('mysql:host=localhost;dbname=u52858', $user, $password,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 
    
    $res1 = $db -> exec("DELETE FROM conn WHERE reqID = '$del_id'");
    $res2 = $db -> exec("DELETE FROM log_Conn WHERE reqID = '$del_id'");
    $res3 = $db -> exec("DELETE FROM req WHERE reqID = '$del_id'");
    header('Location: ./admin.php');
?>