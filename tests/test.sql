USE test;

DROP TABLE peeps;
DROP TABLE contacts;
DROP TABLE tests;

CREATE TABLE contacts (
	emailAddress VARCHAR(150) NOT NULL PRIMARY KEY,
	street VARCHAR(150) NULL,
	phone CHAR(14) NOT NULL
) ENGINE=INNODB;

CREATE TABLE peeps (
	`code` CHAR(8) NOT NULL PRIMARY KEY,
	fname VARCHAR(150) NOT NULL,
	lname VARCHAR(150) NOT NULL,
	email VARCHAR(150) NULL,
	CONSTRAINT `fk_contact_peep` FOREIGN KEY (email) REFERENCES contacts(emailAddress) ON DELETE SET NULL
) ENGINE=INNODB;

CREATE TABLE tests (
	id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	testName VARCHAR(255) NOT NULL,
	started TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	completed TIMESTAMP NULL,
	flagged BOOL NOT NULL DEFAULT 0
) ENGINE=INNODB;