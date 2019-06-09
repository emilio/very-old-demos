CREATE TABLE searches (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`path` VARCHAR(60) NOT NULL,
	# Término formateado a lowercase
	`formatted_term` VARCHAR(60) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY `path` (`path`)
	# KEY formatted_term (formatted_term)
) ENGINE=InnoDB, CHARSET=UTF8;