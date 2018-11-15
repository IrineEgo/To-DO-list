<?php
    require 'core.php';

    if (empty($_SESSION['user']['id'])) {
            header('Location: ./register.php');
    }

    $pdo = createPDO();

    if (!empty($_GET['id']) && !empty($_GET['action'])) {
            if (($_GET['action'] == 'edit') && !empty($_POST['description'])) {
                    $sql = "UPDATE task SET description = ? WHERE id = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(["{$_POST['description']}", "{$_GET['id']}"]);
                    header('Location: ./index.php');	
            } else {
                    $sql = "SELECT * FROM tasks";
            }
            if ($_GET['action'] == 'done') {
                    $sql = "UPDATE task SET is_done = 1 WHERE id = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(["{$_GET['id']}"]);
                    header( 'Location: ./index.php');		
            }
            if ($_GET['action'] == 'delete') {
                    $sql = "DELETE FROM task WHERE id = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(["{$_GET['id']}"]);
                    header( 'Location: ./index.php');
            } 
    }
    
    if (!empty($_POST['description']) && empty($_GET['action'])) {
            $date = date('Y-m-d H:i:s');
            $sql = "INSERT INTO task (description, date_added, assigned_user_id, user_id) VALUES (?, ?, ?, ?)";
            $statement = $pdo->prepare($sql);
            $statement->execute(["{$_POST['description']}", "{$date}", "{$_SESSION['user']['id']}", "{$_SESSION['user']['id']}"]);
    }
    
    if (!empty($_POST['assign']) && !empty($_POST['assigned_user_id'])) {
            $sql = "UPDATE task SET assigned_user_id = ? WHERE id = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute(["{$_POST['assigned_user_id']}", "{$_POST['id_description']}"]);
    }
    
    if (!empty($_POST['sort']) && !empty($_POST['sort_by'])) {
            $sql = "SELECT *, task.id AS id_description, user.login AS user_login FROM task 
            INNER JOIN user ON task.user_id=user.id 
            INNER JOIN user AS us ON task.assigned_user_id=us.id 
            ORDER BY {$_POST['sort_by']} ASC";
            $statement = $pdo->prepare($sql);
            $statement->execute();
    } else {
            $sql = "SELECT *, task.id AS id_description, user.login AS user_login FROM task 
            INNER JOIN user ON task.user_id=user.id 
            INNER JOIN user AS us ON task.assigned_user_id=us.id";
            $statement = $pdo->prepare($sql);
        $statement->execute();
    }
    
    $sqlUsers = "SELECT * FROM user";
    $statementUsers = $pdo->prepare($sqlUsers);
    $statementUsers->execute();
    while ($rowUser = $statementUsers->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $rowUser;
    }
    $sqlMyList = "SELECT *, task.id AS id_description, user.login AS user_login FROM task 
    INNER JOIN user ON task.user_id=user.id 
    INNER JOIN user AS us ON task.assigned_user_id=us.id
    WHERE assigned_user_id = ?";
    $statementMyList = $pdo->prepare($sqlMyList);
    $statementMyList->execute(["{$_SESSION['user']['id']}"]);
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Список дел</title>
    <link rel="stylesheet" href=" ./style.css">
  </head>
  <body>
    <header>
	  <h1 class="title">Здравствуйте, <?php echo $_SESSION['user']['login']; ?>! Ваш список дел:</h1>
    </header>
    <section>
  	  <form method="POST">
		<input type="text" name="description" placeholder="Новая задача" value="<?php if (!empty($_GET['description'])) echo $_GET['description']; ?>">
        <input type="submit" name="save" value="Сохранить">
	  </form>

      <form method="POST">
		<label for="sort">Сортировать по:</label>
		  <select name="sort_by">
			<option value="date_added">Дате добавления</option>
			  <option value="is_done">Статусу</option>
			  <option value="description">Описанию</option>
		  </select>
		    <input type="submit" name="sort" value="Отсортировать">
      </form>

	  <table class="table-index">
	    <tr>
		  <th>Описание задачи</th>
		  <th>Дата добавления</th>
		  <th>Статус</th>
		  <th>Выбрать действие</th>
		  <th>Делегировал задачу</th>
		  <th>Ответственный</th>
          <th>Выбрать пользователя для делегирования</th>
	    </tr>
		  <?php foreach ($statement as $row) { ?>
	    <tr>
		  <td><?php echo htmlspecialchars($row['description']); ?></td>
		  <td><?php echo htmlspecialchars($row['date_added']); ?></td>
		  <td <?php if ($row['is_done'] == 1) echo 'style="color: green;"'; ?>>
			  <?php if ($row['is_done'] == 0) {
				  echo 'В процессе';
			  }                   
                  else {
				      echo 'Выполнено';
				  } ?>
		  </td>
		  <td>
			<a href="?id=<?php echo $row['id_description']; ?>&action=edit&description=<?php echo $row['description']; ?>">Изменить</a><br>
			<a href="?id=<?php echo $row['id_description']; ?>&action=done">Выполнить</a><br>
			<a href="?id=<?php echo $row['id_description']; ?>&action=delete">Удалить</a><br>
		  </td>
		  <td style="text-align:center;"><?php if ($row['login'] == $_SESSION['user']['login']) { echo 'Вы'; } else { echo htmlspecialchars($row['login']); } ?></td>
		  <td style="text-align:center;"><?php echo htmlspecialchars($row['user_login']); ?></td>
		  <td>
		    <form method="POST">
			  <select name="assigned_user_id">
				<?php foreach ($users as $user) { ?>
					<option value="<?php echo $user['id']; ?>"><?php echo $user['login']; ?></option>
					<?php } ?>					
			  </select>
				<input type="hidden" name="id_description" value="<?php echo $row['id_description']; ?>">
				<input type="submit" name="assign" class="assign" value="Делегировать">
			</form>
		  </td>
	    </tr>
		  <?php } ?>
	  </table>
	  <h3>Делегированные Вам задачи:</h3>
	  <table class="table-index">
	    <tr>
		  <th>Описание задачи</th>
		  <th>Дата добавления</th>
		  <th>Статус</th>
		  <th>Выбрать действие</th>
		  <th>Делегировал задачу</th>
          <th>Ответственный</th>
	    </tr>
		    <?php foreach ($statementMyList as $rowMyList) { ?>
	    <tr>
		  <td><?php echo htmlspecialchars($rowMyList['description']); ?></td>
		  <td><?php echo htmlspecialchars($rowMyList['date_added']); ?></td>
		  <td <?php if ($rowMyList['is_done'] == 1) echo 'style="color: green;"'; ?>>
			  <?php if ($rowMyList['is_done'] == 0) {
				  echo 'В процессе';
		      } else {
				echo 'Выполнено';
			  } ?>
		  </td>
		  <td>
			<a href="?id=<?php echo $rowMyList['id_description']; ?>&action=edit&description=<?php echo $rowMyList['description']; ?>">Изменить</a><br>
			<a href="?id=<?php echo $rowMyList['id_description']; ?>&action=done">Выполнить</a><br>
			<a href="?id=<?php echo $rowMyList['id_description']; ?>&action=delete">Удалить</a><br>
		  </td>
		  <td style="text-align:center;"><?php if ($rowMyList['login'] == $_SESSION['user']['login']) { echo 'Вы'; } else { echo htmlspecialchars($row['login']); } ?></td>
		  <td style="text-align:center;"><?php echo htmlspecialchars($rowMyList['user_login']); ?></td>
	    </tr>
			<?php } ?>
	  </table>
      <div class="wrap">
        <a class="logout" href="logout.php">Выход из системы</a>
      </div>
    </section>         
  </body>
</html>
