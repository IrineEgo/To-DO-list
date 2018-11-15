<?php
    require 'core.php';
    
    if (isRegistration())  {
        $pdo = createPDO();
        $sql = "INSERT INTO user (login, password) VALUES (?, ?)";
        $statement = $pdo->prepare($sql);
        $userPassword = getHashPassword($_POST['password']);
        $statement->execute(["{$_POST['login']}", "{$userPassword}"]);
    }
    
    if (isAuthorization()) {
        $pdo = createPDO();
        $sql = "SELECT * FROM user WHERE login = ? AND password = ?";
        $statement = $pdo->prepare($sql);
        $userPassword = getHashPassword($_POST['password']);
        $statement->execute(["{$_POST['login']}", "{$userPassword}"]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        if (!empty($user)) {
            session_start();
            unset($user['password']);
            $_SESSION['user'] = $user;
            header('Location: ./index.php');
        }
    } 
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Вход/Регистрация</title>
    <link rel="stylesheet" href=" ./style.css">
  </head>
  <body>
    <fieldset>
      <legend>
        <?php if (isRegistration()) {
			echo 'Спасибо! Войдите, используя свой логин и пароль!'; } else if (isAuthorization() && empty($user)) { echo 'Неправильный логин и/или пароль!'; } else { echo 'Войдите или зарегистрируйтесь'; } 
        ?>
      </legend>   
      <form method="POST">
        <label>Логин:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="login" id="login"></label><br><br>
        <label>Пароль:&nbsp;&nbsp;<input type="text" name="password" id="password"></label><br>
          <button type="submit" name="authorization" class="enter">Вход</button>
		  <button type="submit" name="registration" class="enter">Регистрация</button>
      </form>
    </fieldset>
  </body>
</html>
