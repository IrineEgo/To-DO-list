<?php
    require 'core.php';

    $pdo = createPDO();

    //Создаем таблицу task в БД
    $sql = "CREATE TABLE `task` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `assigned_user_id` int(11) DEFAULT NULL,
      `description` varchar(500) NOT NULL,
      `is_done` tinyint(1) NOT NULL DEFAULT '0',
      `date_added` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    )";
    $pdo->exec($sql);

    //Создаем таблицу user в БД
    $sql = "CREATE TABLE `user` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `login` varchar(50) NOT NULL,
      `password` varchar(255) NOT NULL,
      PRIMARY KEY (`id`)
    )";
    $pdo->exec($sql);
?>
