create database questionaireWeb;
use questionaireWeb;
create table questionaire (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        subject VARCHAR(500) NOT NULL,
        description VARCHAR(5000) NOT NULL,
        createTime datetime NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table question (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        questionaireId INT NOT NULL,
        title VARCHAR(5000) NOT NULL,
        isSingle TINYINT(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table questionOption (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        questionId INT NOT NULL,
        content VARCHAR(1000),
        isHasNext TINYINT(1),
        isCustomized TINYINT(1),
        isSkip TINYINT(1),
        isSkipOne TINYINT(1),
        skipIndex INT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table answer (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        questionaireId INT NOT NULL,
        content VARCHAR(5000) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table user (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(20) NOT NULL,
        password VARCHAR(30) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;