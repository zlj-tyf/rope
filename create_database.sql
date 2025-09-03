CREATE DATABASE IF NOT EXISTS rope;
use rope;
CREATE TABLE `matches` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `class_a` VARCHAR(100) NOT NULL,
  `class_b` VARCHAR(100) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `result` VARCHAR(100) DEFAULT NULL
);

CREATE TABLE `classes` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `class_name` VARCHAR(100) NOT NULL
);

CREATE TABLE `students` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `class_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`)
);

CREATE TABLE `attendance` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `match_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL,
  `status` ENUM('未签到', '已签到') DEFAULT '未签到',
  FOREIGN KEY (`match_id`) REFERENCES `matches`(`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);
