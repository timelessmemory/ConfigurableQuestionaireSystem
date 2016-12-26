create database questionaireWeb;
use questionaireWeb;
create table questionaire (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        isProvicy TINYINT(1) NOT NULL,
        brand VARCHAR(100) NOT NULL,
        subject VARCHAR(500) NOT NULL,
        description VARCHAR(5000),
        agree_fst VARCHAR(500),
        required_fst TINYINT(1),
        agree_snd VARCHAR(500),
        required_snd TINYINT(1),
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
        isCustomized TINYINT(1) NOT NULL,
        isHasNext TINYINT(1) NOT NULL,
        isSkip TINYINT(1) NOT NULL,
        isSkipOne TINYINT(1),
        skipIndex INT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table answer (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        questionaireId INT NOT NULL,
        content VARCHAR(5000) NOT NULL,
        answerTime datetime NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table user (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        role VARCHAR(20) NOT NULL,
        brand VARCHAR(100),
        name VARCHAR(20) NOT NULL,
        password VARCHAR(30) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;