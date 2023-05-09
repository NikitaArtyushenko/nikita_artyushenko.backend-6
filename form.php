<html>
  <head>
    <style>
/* Сообщения об ошибках и поля с ошибками выводим с красным бордюром. */
.error {
  border: 2px solid red;
}
    </style>
  </head>
  <body>

<?php
if (!empty($messages)) {
  print('<div id="messages">');
  // Выводим все сообщения.
  foreach ($messages as $message) {
    print($message);
  }
  print('</div>');
}

// Далее выводим форму отмечая элементы с ошибками классом error
// и задавая начальные значения элементов ранее сохраненными.
?>

<div id="forms">
<form action="" method="POST">

  <label>
    Имя:<br />
    <input name="fio" <?php if ($errors['fio']) {print 'class="error"';}else{print 'class="formContent"';} ?> value="<?php print $values['fio']; ?>" placeholder="Ваше имя (по русски)" />
  </label><br /><br />

  <label>
    Еmail:<br />
    <input name="email"  type="email" <?php if ($errors['email']) {print 'class="error"';}else{print 'class="formContent"';} ?> value="<?php print $values['email']; ?>" placeholder="Введите вашу почту" />
  </label><br /><br />

  <label>
    Год рождения:<br />
  <select name="year" <?php if ($errors['year']) {print 'class="error"';} ?> value="<?php print $values['year']; ?>">
    <?php 
    for ($i = 1922; $i <= 2022; $i++) {
      printf('<option value="%d">%d год</option>', $i, $i);
    }
    ?>
  </select>
  </label><br /><br />

  <label>
    Пол:<br/>
    <label><input type="radio" name="gender" <?php if ($errors['gender']) {print 'class="error"';}else{print 'class="formContent"';} ?> <?php if ($values['gender'] == "Мужской"){print 'checked="checked"';}?> value="Мужской" />Мужской</label><br />
    <label><input type="radio" name="gender" <?php if ($errors['gender']) {print 'class="error"';}else{print 'class="formContent"';} ?> <?php if ($values['gender'] == "Женский"){print 'checked="checked"';}?> value="Женский" />Женский</label><br />
  </label>
          
  <label>
            Количество конечностей:<br />
            <label><input type="radio" name="limbs" value="4" <?php if ($errors['limbs']) {print 'class="error"';}else{print 'class="formContent"';} ?> <?php if ($values['limbs'] == "4"){print 'checked="checked"';}?> />4 конечности</label><br />
            <label><input type="radio" name="limbs" value="3" <?php if ($errors['limbs']) {print 'class="error"';}else{print 'class="formContent"';} ?> <?php if ($values['limbs'] == "3"){print 'checked="checked"';}?>/>3 конечности</label><br />
            <label><input type="radio" name="limbs" value="2" <?php if ($errors['limbs']) {print 'class="error"';}else{print 'class="formContent"';} ?> <?php if ($values['limbs'] == "2"){print 'checked="checked"';}?>/>2 конечности</label><br />
            <label><input type="radio" name="limbs" value="1" <?php if ($errors['limbs']) {print 'class="error"';}else{print 'class="formContent"';} ?> <?php if ($values['limbs'] == "1"){print 'checked="checked"';}?>/>1 конечность</label><br />
            <label><input type="radio" name="limbs" value="0" <?php if ($errors['limbs']) {print 'class="error"';}else{print 'class="formContent"';} ?> <?php if ($values['limbs'] == "0"){print 'checked="checked"';}?>/>Нет конечностей</label><br />
            <br />
  </label>

            <label>
                Сверхспособности:<br />
                <select name="abilities[]" <?php if ($errors['abilities']) {print 'class="error"';}else{print 'class="formContent"';}  $ids = unserialize($values['abilities']); assert(is_array($ids)); ?> multiple="multiple">
                  <option value="Бессмертие" <?php if (in_array("Бессмертие", $ids)){print 'selected="selected""';}?>>Бессмертие</option>
                  <option value="Прохождение сквозь стены" <?php if (in_array("Прохождение сквозь стены", $ids)){print 'selected="selected"';}?>>Прохождение сквозь стены</option>
                  <option value="Левитация"<?php if (in_array("Левитация", $ids)){print 'selected="selected"';}?>>Левитация</option>
                </select>
            </label><br /><br />

            <label>
              Биография:<br />
              <textarea name="biography" <?php if ($errors['biography']) {print 'class="error"';}else{print 'class="formContent"';} ?> value="<?php print $values['biography']; ?>" cols="100" rows="20" placeholder="Напишите вашу биографию (по русски)"></textarea>
            </label><br /><br />

            <label><input type="checkbox" checked="checked" <?php if ($errors['check']) {print 'class="error"';} ?> value="<?php print $values['check']; ?>" name="check" />
              С контрактом ознакомлен(а)</label><br /><br />

              <input type="submit" value="Отправить" />
</form>
  </div>
  </body>
</html>