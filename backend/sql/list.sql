DROP TABLE IF EXISTS `list`;
CREATE TABLE IF NOT EXISTS `list` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `link` VARCHAR(50) NOT NULL ,
    `filename` VARCHAR(200) NOT NULL ,
    `description` LONGTEXT NOT NULL ,
    `filesize` LONG NOT NULL ,
    `hash` VARCHAR(512) NOT NULL ,
    `tsinbei` VARCHAR(128) NOT NULL ,
    `tsinbei_pwd` VARCHAR(50) NOT NULL ,
    `123` VARCHAR(128) NOT NULL ,
    `123_pwd` VARCHAR(50) NOT NULL ,
    `lanzou` VARCHAR(128) NOT NULL ,
    `lanzou_pwd` VARCHAR(50) NOT NULL ,
    `username` VARCHAR(50) NOT NULL ,
    `createTime` DATETIME NOT NULL ,
    `downloadCount` LONG NOT NULL ,
    `password` LONGTEXT NOT NULL ,
    PRIMARY KEY (`id`)
)engine = innoDB, charset = utf8;