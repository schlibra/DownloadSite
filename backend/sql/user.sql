DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(30) NOT NULL,
    `password` LONGTEXT NOT NULL,
    `nickname` VARCHAR(50) NOT NULL,
    `sex` INT NOT NULL,
    `admin` INT NOT NULL,
    `ban` INT NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
)engine = innoDB, charset = "UTF8";