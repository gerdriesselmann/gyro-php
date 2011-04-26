CREATE TABLE tokens (
	token CHAR(40) NOT NULL,
	creationdate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,	
	PRIMARY KEY(token),
	INDEX `i_tokens_creationdate`(creationdate)
) ENGINE=InnoDB;

