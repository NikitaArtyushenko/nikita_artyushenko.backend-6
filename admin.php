<?php
  header('Content-Type: text/html; charset=UTF-8');

  session_start();

  if (!empty($_SESSION['login']))
  {
    session_destroy();
    header('Location: ./');
  }

  $user = 'u52858'; 
  $pass = '6454527'; 
  $db = new PDO('mysql:host=localhost;dbname=u52858', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 

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
    ?>
<html>
        <head>
        <link rel="stylesheet" href="styles.css">
        </head>
        <body>
                <?php
                  $result = $db -> query("SELECT * FROM req");
                  ?>
                  <table class="table_price">
                  <caption>Данные</caption>
                    <thead>
                      <tr >
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Дата</th>
                        <th>Email</th>
                        <th>Пол</th>
                        <th>Кол-во конечностей</th>
                        <th>Способности</th>
                        <th>Биография</th>
                        <th></th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      while ($row = $result -> fetch())
                      {
                        echo '<tr>
                        <td>'.$row["reqID"].'</td>
                        <td>'.$row["name"].'</td>
                        <td>'.$row["year"].'</td>
                        <td>'.$row["email"].'</td>
                        <td>'.$row["gender"].'</td>
                        <td>'.$row["limbs"].'</td>
                        <td>';
                        $abil = $db -> query("SELECT c.abId FROM conn c WHERE c.reqID = '".$row["reqID"]."'");
                        while ($row1 = $abil -> fetch()){
                          echo '<br/>';
                          if ($row1["abId"] == 1){echo'<p>Бессмертие</p><br/>';}
                          else{
                            if ($row1["abId"] == 2){echo '<p>Прохождение сквозь стены</p><br/>';}
                            else{echo '<p>Левитация</p><br/>';}
                          }
                        }
                        echo '</td>
                        <td>'.$row["biography"].'</td>
                        <td><a href = "delete.php?id_record='.$row["reqID"].'">Удалить</a></td>
                        <td><a href = "change.php?id_record='.$row["reqID"].'">Изменить</a></td>
                        </tr>';
                      }
                      echo '</tr>';
                      ?>
                    </tbody>
                  </table><br /><br />
              <?php
              $stats = $db -> query("SELECT c.abId ID, ab.ability AB, count(DISTINCT c.reqID) counting FROM conn c JOIN abilities ab ON c.abId = ab.abId GROUP BY c.abId ");
              ?>
              <table class="table_price">
                  <caption>Статистика по способностям</caption>
                    <thead>
                      <tr >
                        <th>ID</th>
                        <th>Способность</th>
                        <th>Количество пользователей</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                      while ($row = $stats -> fetch())
                      {
                        echo '<tr>
                        <td>'.$row["ID"].'</td>
                        <td>'.$row["AB"].'</td>
                        <td>'.$row["counting"].'</td>
                        </tr>';
                      }
                      echo '</tr>';
                      ?>
                      </tbody>
                  </table><br /><br />
        </body>
</html>
<?php
    if (!empty($_GET['none']))
    {
      $message = "Неверные данные!";
      print($message);
    }
  }
  else
  {
    $user_record = $_POST['User_Record'];
    $_SESSION['login'] = 'Admin';
    $_SESSION['uid'] = $user_record;
    header('Location: ./');
  }
?>