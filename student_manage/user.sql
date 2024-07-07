CREATE DATABASE IF NOT EXISTS `student_manage`;
USE `student_manage`;
DROP TABLE IF EXISTS `user_db`;
CREATE TABLE IF NOT EXISTS `user_db` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(250) NOT NULL UNIQUE,
    `password` varchar(250) NOT NULL,
    `email` varchar(250) NOT NULL,
    `phone_number` varchar(250) NOt NULL,
    `role` ENUM('student', 'teacher'),
);
DROP TABLE IF EXISTS `user_post`;
CREATE TABLE IF NOT EXISTS `user_post` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` varchar(250) NOT NULL,
    `content` TEXT,
    `user_id` INT, FOREIGN KEY (`user_id`) REFERENCES `user_db`(`id`)
);
INSERT INTO `user_db` (`username`, `password`, `email`, `phone_number`, `role`)
VALUES ('teacherA', '123456789', 'teacher@example.com', '0123456789', 'teacher');
