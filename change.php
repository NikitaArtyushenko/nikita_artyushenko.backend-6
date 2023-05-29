<?php
header('Content-Type: text/html; charset=UTF-8');
$change_id = $_GET['id_record'];

$user = 'u52858'; 
$pass = '6454527'; 
$db = new PDO('mysql:host=localhost;dbname=u52858', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Пример HTTP-аутентификации.
    // PHP хранит логин и пароль в суперглобальном массиве $_SERVER.
    if (empty($_SERVER['PHP_AUTH_USER']) ||
        empty($_SERVER['PHP_AUTH_PW']) ||
        $_SERVER['PHP_AUTH_USER'] != 'admin' ||
        md5($_SERVER['PHP_AUTH_PW']) != md5('123')) {
      header('HTTP/1.1 401 Unanthorized');
      header('WWW-Authenticate: Basic realm="mattakvshi"');
      print('<h1>401 Требуется авторизация</h1>');
      exit();
    }
    $messages = array();
  if (!empty($_GET['save'])) {
    print('Спасибо, результаты сохранены.');
  }

  $errors = array();

  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['year'] = !empty($_COOKIE['year_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['limbs'] = !empty($_COOKIE['limb_error']);
  $errors['abilities'] = !empty($_COOKIE['ab_error']);
  $errors['biography'] = !empty($_COOKIE['bio_error']);
  $errors['check'] = !empty($_COOKIE['check_error']);

  if ($errors['fio']) {
    setcookie('fio_error', '', 100000);
    $messages[] = '<div class="error">Заполните имя.</div>';
  }

  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Заполните email.</div>';
  }

  if ($errors['year']) {
    setcookie('year_error', '', 100000);
    $messages[] = '<div class="error">Заполните год.</div>';
  }

  if ($errors['gender']) {
    setcookie('gender_error', '', 100000);
    $messages[] = '<div class="error">Выберите пол.</div>';
  }

  if ($errors['limbs']) {
    setcookie('limb_error', '', 100000);
    $messages[] = '<div class="error">Выберите кол-во конечностей.</div>';
  }

  if ($errors['abilities']) {
    setcookie('ab_error', '', 100000);
    $messages[] = '<div class="error">Заполните абилки.</div>';
  }

  if ($errors['biography']) {
    setcookie('bio_error', '', 100000);
    $messages[] = '<div class="error">Заполните биографию.</div>';
  }

  if ($errors['check']) {
    setcookie('check_error', '', 100000);
    $messages[] = '<div class="error">Примите условия контракта.</div>';
  }

  $result = $db -> query("SELECT * FROM req WHERE reqID = '$change_id'");
  $values = array();
  while ($row = $result -> fetch()){
    $values['fio'] = $row["name"];
    $values['email'] = $row["email"];
    $values['year'] = $row["year"];
    $values['gender'] = $row["gender"];
    $values['limbs'] = $row["limbs"];
    $abres = $db -> query("SELECT * FROM conn WHERE reqID = '$change_id'");
    $val = array();
    $i = 0;
    while ($rows = $abres -> fetch()){
        $val[$i] = $rows['abId'];
        $i++;
    }
    }  
  include('form.php');
}
else{
    $errors = FALSE;

    if (empty($_POST['fio']) || preg_match("/^[А-ЯЁ][а-яё]*$/", $_POST['fio'])) {
      print('Корректно заполните имя.<br/>');
      $errors = TRUE;
    }
    
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      print('Корректно заполните email.<br/>');
      $errors = TRUE;
    }
    
    if (empty($_POST['year']) || !is_numeric($_POST['year']) || !preg_match('/^\d+$/', $_POST['year'])) {
      print('Корректно заполните год.<br/>');
      $errors = TRUE;
    }
    
    if (empty($_POST['gender'])){
        if($_POST['gender'] != "Мужской" && $_POST['gender'] != "Женский")
        print('Выберите свой пол.<br/>');
        $errors = TRUE;
    }
    
    if (empty($_POST['limbs']) || !is_numeric($_POST['limbs']) || $_POST['limbs'] < 0 || $_POST['limbs'] > 4){
      print('Выберите количество конечностей.<br/>');
      $errors = TRUE;
    }
    
    foreach ($_POST['abilities'] as $ability){
        if ($ability != "1" && $ability != "2" && $ability != "3"){
            print('Выберите свои способности.<br/>');
            $errors = TRUE;
        }
    }
    
    if (strlen($_POST['biography'] || preg_match("/^[А-ЯЁ][а-яё]*$/", $_POST['fio']))==0){
      print('Корректно заполните биографию.<br/>');
      $errors = TRUE;
    }
    
    if (!isset($_POST['check'])){
      print('Примите условия контракта!<br/>');
      $errors = TRUE;
    }
    
    if ($errors) {
      header('Location: ./');
      exit();
    }
    
    
    try {
      $res1 = $db -> exec("DELETE FROM conn WHERE reqID = '$change_id'"); 
      $stmt = $db->prepare("UPDATE req SET name = :name, year = :year, email = :email, gender = :gender, limbs = :limbs, biography = :biography WHERE reqID = '$change_id'");
      $stmt->bindParam(':name', $_POST['fio']);
      $stmt->bindParam(':year', $_POST['year']);
      $stmt->bindParam(':email', $_POST['email']);
      $stmt->bindParam(':gender', $_POST['gender']);
      $stmt->bindParam(':limbs', $_POST['limbs']);
      $stmt->bindParam(':biography', $_POST['biography']);
      $stmt->execute();
    
    
      foreach ($_POST['abilities'] as $ability){
        $stmt4 = $db->prepare("INSERT INTO conn (reqID, abId) VALUES (:reqID, :abId)");
        $stmt4->bindParam(':reqID', $change_id);
        $stmt4->bindParam(':abId', $ability);
        $stmt4->execute();
    }
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
    
    header('Location: ./admin.php');
}

?>
