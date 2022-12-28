CREATE DATABASE IF NOT EXISTS `listr_app`;
USE `listr_app`;

-- ************************************** `app_users`
CREATE TABLE `app_users`
  (
     `username`          VARCHAR(45) NOT NULL,
     `password_hash`     VARCHAR(255) NOT NULL,
     `email`             VARCHAR(255) NOT NULL,
     `first_name`        VARCHAR(45) NOT NULL,
     `last_name`         VARCHAR(45) NOT NULL,
     `verified`          BIT NOT NULL DEFAULT 0,
     `verification_code` VARCHAR(45) NOT NULL,
     PRIMARY KEY (`username`)
  );

-- ************************************** `lists`
CREATE TABLE `lists`
  (
     `id`           INT NOT NULL AUTO_INCREMENT,
     `name`         VARCHAR(45) NOT NULL,
     `last_updater` VARCHAR(45) NOT NULL,
     `owner`        VARCHAR(45) NOT NULL,
     `description`  VARCHAR(255) NOT NULL,
     `archived`     BIT NOT NULL DEFAULT 0,
     `last_updated` TIMESTAMP NOT NULL,
     PRIMARY KEY (`id`),
     FOREIGN KEY (`owner`) REFERENCES `app_users` (`username`),
     FOREIGN KEY (`last_updater`) REFERENCES `app_users` (`username`)
  );

-- ************************************** `item_states`
CREATE TABLE `item_states`
  (
     `state` INT NOT NULL,
     `name`  VARCHAR(45) NOT NULL,
     PRIMARY KEY (`state`)
  );

INSERT INTO `item_states` (`state`, `name`) VALUES (1, "UNCHECKED");
INSERT INTO `item_states` (`state`, `name`) VALUES (2, "CHECKED");
INSERT INTO `item_states` (`state`, `name`) VALUES (3, "ARCHIVED");

-- ************************************** `items`
CREATE TABLE `items`
  (
     `id`      INT NOT NULL AUTO_INCREMENT,
     `state`   INT NOT NULL DEFAULT 1,
     `list_id` INT NOT NULL,
     `name`    VARCHAR(45) NOT NULL,
     PRIMARY KEY (`id`),
     FOREIGN KEY (`state`) REFERENCES `item_states` (`state`),
     FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`));

-- ************************************** `favorites`
CREATE TABLE `favorites`
  (
     `user`    VARCHAR(45) NOT NULL,
     `list_id` INT NOT NULL,
     PRIMARY KEY (`user`, `list_id`),
     FOREIGN KEY (`user`) REFERENCES `app_users` (`username`),
     FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`));

-- ************************************** `friends`
CREATE TABLE `friends`
  (
     `friender` VARCHAR(45) NOT NULL,
     `friended` VARCHAR(45) NOT NULL,
     `accepted` BIT NOT NULL DEFAULT 0,
     PRIMARY KEY (`friender`, `friended`),
     FOREIGN KEY (`friended`) REFERENCES `app_users` (`username`),
     FOREIGN KEY (`friender`) REFERENCES `app_users` (`username`)
  );

-- ************************************** `shared`
CREATE TABLE `shared`
  (
     `user`     VARCHAR(45) NOT NULL,
     `list_id`  INT NOT NULL,
     `accepted` BIT NOT NULL DEFAULT 0,
     PRIMARY KEY (`user`, `list_id`),
     FOREIGN KEY (`user`) REFERENCES `app_users` (`username`),
     FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`));


-- ************************************** `notification_types`
CREATE TABLE `notification_types`
  (
     `type`  INT NOT NULL,
     `name`  VARCHAR(45) NOT NULL,
     PRIMARY KEY (`type`)
  );

INSERT INTO `notification_types` (`type`, `name`) VALUES (0, "LIST_RENAMED");
INSERT INTO `notification_types` (`type`, `name`) VALUES (1, "LIST_UPDATED");
INSERT INTO `notification_types` (`type`, `name`) VALUES (5, "UNFRIENDED");
INSERT INTO `notification_types` (`type`, `name`) VALUES (6, "FRIEND_REQUESTED");

-- ************************************** `notifications`
CREATE TABLE `notifications`
  (
     `id`      INT NOT NULL AUTO_INCREMENT,
     `type`    INT NOT NULL,
     `message` VARCHAR(255) NOT NULL,
     `user`    VARCHAR(45) NOT NULL,
     `data`    VARCHAR(255) NOT NULL,
     `date`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (`id`),
     FOREIGN KEY (`user`) REFERENCES `app_users` (`username`),
     FOREIGN KEY (`type`) REFERENCES `notification_types` (`type`)
  );

-- ************************************** `sessions`
CREATE TABLE `sessions`
  (
     `id`         INT NOT NULL AUTO_INCREMENT,
     `session_id` VARCHAR(255) NOT NULL,
     `username`   VARCHAR(45) NOT NULL,
     `last_ping`  DATETIME NOT NULL,
     PRIMARY KEY (`id`),
     FOREIGN KEY (`username`) REFERENCES `app_users` (`username`)
  );

-- ************************************** `email_change`
CREATE TABLE `email_change`
  (
     `user`  VARCHAR(45) NOT NULL,
     `email` VARCHAR(255) NOT NULL,
     `code`  VARCHAR(255) NOT NULL,
     PRIMARY KEY (`user`, `email`),
     FOREIGN KEY (`user`) REFERENCES `app_users` (`username`)
  );