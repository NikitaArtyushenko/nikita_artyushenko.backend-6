<?php
header('Content-Type: text/html; charset=UTF-8');
$user = 'u52858'; 
  $pass = '6454527'; 
  $db = new PDO('mysql:host=localhost;dbname=u52858', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
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

  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['year'] = empty($_COOKIE['year_value']) ? '' : $_COOKIE['year_value'];
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
  $values['limbs'] = empty($_COOKIE['limb_value']) ? '' : $_COOKIE['limb_value'];
  $values['abilities'] = empty($_COOKIE['ab_value']) ? '' : $_COOKIE['ab_value'];
  $values['biography'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  $values['check'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    try{
      /*
      $stm = $db->prepare("SELECT r.name n, r.year y , r.email e, r.gender g, r.limbs l, r.biography b  FROM req r, log l, log_Conn c WHERE l.login = :login AND l.pass = :pass AND l.logID = c.logID AND c.reqID = r.reqID");
      $stm->bindParam(':login', $_COOKIE['login']);
      $stm->bindParam(':pass', $_COOKIE['pass']);
      $stmt->execute();
      */
      mysql_query("SET NAMES cp1251");
      $result=mysql_query("SELECT r.name n, r.year y , r.email e, r.gender g, r.limbs l, r.biography b  FROM req r, log l, log_Conn c WHERE l.login = :login AND l.pass = :pass AND l.logID = c.logID AND c.reqID = r.reqID", $db);

      if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
          $values['fio'] = empty(mysql_result($result,0,"name")) ? '' : mysql_result($result,0,"name");
          $values['email'] = empty(mysql_result($result,0,"email")) ? '' : mysql_result($result,0,"email");
          $values['year'] = empty(mysql_result($result,0,"year")) ? '' : mysql_result($result,0,"year");
          $values['gender'] = empty(mysql_result($result,0,"gender")) ? '' : mysql_result($result,0,"gender");
          $values['limbs'] = empty(mysql_result($result,0,"limbs")) ? '' : mysql_result($result,0,"limbs");
          $values['abilities'] = empty($_COOKIE['ab_value']) ? '' : $_COOKIE['ab_value'];
          $values['biography'] = empty(mysql_result($result,0,"biography")) ? '' : mysql_result($result,0,"biography");
          $values['check'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];
        }
      }
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }

  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty($_POST['fio']) || preg_match("/^[А-ЯЁ][а-яё]*$/", $_POST['fio'])) {
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    $f_name = htmlspecialchars($_POST['fio']);
    $f_name = strip_tags($f_name);
    setcookie('fio_value', $f_name, time() + 365 * 24 * 60 * 60);
  }

  if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    $f_mail = strip_tags($_POST['email']);
    setcookie('email_value', $f_mail, time() + 365 * 24 * 60 * 60);
  }

  if (empty($_POST['year']) || !is_numeric($_POST['year']) || !preg_match('/^\d+$/', $_POST['year'])) {
    setcookie('year_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    $f_year = htmlspecialchars($_POST['year']);
    $f_year = strip_tags($f_year);
    setcookie('year_value', $f_year, time() + 365 * 24 * 60 * 60);
  }

  if (empty($_POST['gender'])){
    if($_POST['gender'] != "Мужской" && $_POST['gender'] != "Женский"){
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
    }
  }
  else {
    $f_gen = htmlspecialchars($_POST['gender']);
    $f_gen = strip_tags($f_gen);
    setcookie('gender_value', $f_gen, time() + 365 * 24 * 60 * 60);
  }

  if (empty($_POST['limbs']) || !is_numeric($_POST['limbs']) || $_POST['limbs'] < 0 || $_POST['limbs'] > 4) {
    setcookie('limb_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    $f_limbs = strip_tags($_POST['limbs']);
    setcookie('limb_value', $f_limbs, time() + 365 * 24 * 60 * 60);
  }

  $ids = array();
  foreach ($_POST['abilities'] as $ability){
    if ($ability != "1" && $ability != "2" && $ability != "3"){
      setcookie('ab_error', '1', time() + 24 * 60 * 60);
      $errors = TRUE;
    }
    else{
      $ids[] = strip_tags($ability);
    }
  }
  setcookie('ab_value', serialize($ids), time() + 365 * 24 * 60 * 60);

  if (strlen($_POST['biography'])==0) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    $f_biography = htmlspecialchars($_POST['biography']);
    $f_biography = strip_tags($f_biography);
    setcookie('bio_value', $f_biography, time() + 365 * 24 * 60 * 60);
  }

  if (!isset($_POST['check'])) {
    setcookie('check_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('check_value', $_POST['check'], time() + 365 * 24 * 60 * 60);
  }

  if ($errors) {
    header('Location: index.php');
    exit();
  }
  else {
    setcookie('fio_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('year_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('limb_error', '', 100000);
    setcookie('ab_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('check_error', '', 100000);
  }

  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
  
  try {
    $stmt3 = $db->prepare("UPDATE req r, log l, log_Conn c SET r.name = :name, r.year = :year, r.email = :email, r.gender = :gender, r.limbs = :limbs, r.biography = :biography WHERE l.login = :login AND l.pass = :pass AND l.logID = c.logID AND c.reqID = r.reqID");
    $stmt3->bindParam(':name', $_POST['fio']);
    $stmt3->bindParam(':year', $_POST['year']);
    $stmt3->bindParam(':email', $_POST['email']);
    $stmt3->bindParam(':gender', $_POST['gender']);
    $stmt3->bindParam(':limbs', $_POST['limbs']);
    $stmt3->bindParam(':biography', $_POST['biography']);
    $stmt3->bindParam(':login', $_COOKIE['login']);
    $stmt3->bindParam(':pass', $_COOKIE['pass']);
    $stmt3->execute();
    $temp = $db->lastInsertId();
    
  }
  catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }
  }
  else {
    try{
      $login = md5('ArtyushenkoNikitaLogin');
      $pass = md5('PASSWORD');
  
      setcookie('login', $login);
      setcookie('pass', $pass);
  
      $stmt2 = $db->prepare("INSERT INTO log (login, pass) VALUES (:login, :pass)");
      $stmt2->bindParam(':login', $login);
      $stmt2->bindParam(':pass', $pass);
      $stmt2->execute();
      $temper = $db->lastInsertId();
  
      $stmt3 = $db->prepare("INSERT INTO req (name, year, email, gender, limbs, biography) VALUES (:name, :year, :email, :gender, :limbs, :biography)");
      $stmt3->bindParam(':name', $_POST['fio']);
      $stmt3->bindParam(':year', $_POST['year']);
      $stmt3->bindParam(':email', $_POST['email']);
      $stmt3->bindParam(':gender', $_POST['gender']);
      $stmt3->bindParam(':limbs', $_POST['limbs']);
      $stmt3->bindParam(':biography', $_POST['biography']);
      $stmt3->execute();
    
      $temp = $db->lastInsertId();
    
      foreach ($_POST['abilities'] as $ability){
        $stmt4 = $db->prepare("INSERT INTO conn (reqID, abId) VALUES (:reqID, :abId)");
        $stmt4->bindParam(':reqID', $temp);
        $stmt4->bindParam(':abId', $ability);
        
        $stmt4->execute();
    }
    $stmt5 = $db->prepare("INSERT INTO log_Conn (reqID, logID) VALUES (:reqID, :logID)");
    $stmt5->bindParam(':reqID', $temp);
    $stmt5->bindParam(':logID', $temper);
        
    $stmt5->execute();
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
    


  }
  setcookie('save', '1');

  header('Location: ./');
}