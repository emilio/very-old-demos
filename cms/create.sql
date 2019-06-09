# DROP DATABASE IF EXISTS blog;
# CREATE DATABASE blog;
# USE blog;
# SET NAMES 'utf8';


# DROP TABLE IF EXISTS authors;
CREATE TABLE authors (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(25) NOT NULL,
	`email` VARCHAR(45) NOT NULL,
	`name` VARCHAR(50) NOT NULL,
	`role` ENUM('superadmin', 'admin', 'publisher') NOT NULL DEFAULT 'publisher',
	`description` TEXT NOT NULL, # 250 is more than enough for a SEO description
	`password` VARCHAR(64) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`url` VARCHAR(512) NOT NULL,
	`hash` VARCHAR(64) NOT NULL,
	`twitter_user` VARCHAR(30) NOT NULL,
	`facebook_user` VARCHAR(40) NOT NULL,
	`gplus_id` CHAR(21) NOT NULL,


	PRIMARY KEY (`id`),
	UNIQUE KEY `email`(`email`),
	UNIQUE KEY `username`(`username`)
)
Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND

# DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`slug` VARCHAR(30) NOT NULL,
	`name` VARCHAR(30) NOT NULL,
	`description` TEXT NOT NULL,

	PRIMARY KEY (`id`),
	UNIQUE KEY `slug`(`slug`)
)
Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND

INSERT INTO categories (`slug`, `name`, `description`) VALUES ('sin-categoria', 'Sin categoría', 'Los posts sin ninguna categoría');
-- SENTENCEEND

# DROP TABLE IF EXISTS tags;

CREATE TABLE tags (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`slug` VARCHAR(30) NOT NULL,
	`name` VARCHAR(30) NOT NULL,
	`description` TEXT NOT NULL,

	PRIMARY KEY (`id`),
	UNIQUE KEY `slug`(`slug`),
	UNIQUE KEY `name`(`name`)
) Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND


# DROP TABLE IF EXISTS media;
CREATE TABLE media (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(250) NOT NULL,
	`description` TEXT NOT NULL,
	`type` VARCHAR(20) NOT NULL,
	`route` VARCHAR(250) NOT NULL,
	PRIMARY KEY (`id`)
) Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND
# DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`slug` VARCHAR(200) NOT NULL,
	`title` VARCHAR(250) NOT NULL,
	`description` VARCHAR(250) NOT NULL, # 250 is more than enough for a SEO description
	`format` ENUM('markdown', 'html') NOT NULL DEFAULT 'html',
	`content` LONGTEXT NOT NULL,
	`author_id` INT UNSIGNED NOT NULL,
	# Default 1 -> No category
	`category_id` INT UNSIGNED DEFAULT 1,
	`type` ENUM('post', 'link', 'page') NOT NULL DEFAULT 'post',
	`status` ENUM('publish', 'draft') NOT NULL DEFAULT 'draft',
	`comment_count` INT UNSIGNED NOT NULL DEFAULT 0,
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`published_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',

	PRIMARY KEY (`id`),
	UNIQUE KEY `slug`(`slug`),
	KEY `status_type`(`status`, `type`),
	# If author deleted -> posts deleted
	FOREIGN KEY (`author_id`) REFERENCES `authors`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) # ON DELETE SET DEFAULT
) Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND
CREATE TABLE post_media (
	`post_id` INT UNSIGNED NOT NULL,
	`media_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`post_id`),
	FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE
) Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND
DROP TABLE IF EXISTS post_tags;
-- SENTENCEEND
CREATE TABLE post_tags (
	`post_id` INT UNSIGNED NOT NULL,
	`tag_id` INT UNSIGNED NOT NULL,

	# Delete the row if the tag or the post stops existing
	FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND
# DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
	`id` INT UNSIGNED NOT NULL,
	`post_id` INT UNSIGNED NOT NULL,
	`author_email` VARCHAR(45) NOT NULL, 
	`author_name` TINYTEXT NOT NULL,
	`author_url` VARCHAR(512) NOT NULL,
	`author_ip` VARCHAR(15) NOT NULL,
	`author_id` INT UNSIGNED NOT NULL DEFAULT 0,
	`approved` BOOLEAN DEFAULT 1,
	`content` TEXT NOT NULL,
	`type` ENUM('pingback', 'trackback', 'comment') DEFAULT 'comment',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`comment_parent` INT UNSIGNED NOT NULL DEFAULT 0,
	`karma` INT UNSIGNED NOT NULL,

	PRIMARY KEY (`id`),
	KEY `comment_parent` (`comment_parent`),
	# If post disappears, comments too
	FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE
) Engine=InnoDB DEFAULT CHARSET utf8;
-- SENTENCEEND

# Triggers
CREATE TRIGGER author_update BEFORE UPDATE ON `authors`
FOR EACH ROW SET NEW.updated_at = NOW(), NEW.created_at = OLD.created_at;
-- SENTENCEEND
CREATE TRIGGER author_create BEFORE INSERT ON `authors`
FOR EACH ROW SET NEW.created_at = NOW(), NEW.updated_at = NOW();
-- SENTENCEEND
CREATE TRIGGER post_update BEFORE UPDATE ON `posts`
FOR EACH ROW SET NEW.updated_at = NOW(), NEW.created_at = OLD.created_at, NEW.published_at = IF(NEW.status = 'publish', IF(OLD.status != 'publish', NOW(), OLD.published_at), OLD.published_at);
-- SENTENCEEND
CREATE TRIGGER post_create BEFORE INSERT ON `posts`
FOR EACH ROW SET NEW.created_at = NOW(), NEW.updated_at = NOW(), NEW.published_at = IF(NEW.status = 'publish', IF(NEW.published_at = '0000-00-00 00:00:00', NOW(), NEW.published_at), NEW.published_at);
-- SENTENCEEND
