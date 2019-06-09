
-- Importar etiquetas
INSERT INTO `tags` 
SELECT 
	`wp_terms`.`term_id` AS `id`,
	`wp_terms`.`slug` AS `slug`,
	`wp_terms`.`name` AS `name`,
	'' AS `description` 
FROM `wp_terms`, `wp_term_taxonomy`
WHERE `wp_term_taxonomy`.`taxonomy` = 'post_tag' AND `wp_terms`.`term_id` = `wp_term_taxonomy`.`term_id`;

-- Importar categorías
INSERT INTO `categories`
SELECT 
	(`wp_terms`.`term_id` + 1) AS `id`,
	`wp_terms`.`slug` AS `slug`,
	`wp_terms`.`name` AS `name`,
	'' AS `description`
FROM `wp_terms`, `wp_term_taxonomy`
WHERE `wp_term_taxonomy`.`taxonomy` = 'category' AND `wp_terms`.`term_id` = `wp_term_taxonomy`.`term_id` AND `wp_terms`.`slug` != 'sin-categoria';
-- Importar usuario
INSERT INTO authors (`id`, `username`, `name`, `email`, `url`, `password`) 
SELECT 
	`wp_users`.`ID` AS `id`,
	`wp_users`.`user_login` AS `username`,
	`wp_users`.`display_name` AS `name`,
	`wp_users`.`user_email` AS `email`,
	`wp_users`.`user_url` AS `url`,
	'$2a$08$oC/0hioNZwZFvsqCA7sTb.vfIzmZQf2TZ1FOHsAHfwbXGAJpg8pgy' AS `password` # La contraseña para todos es 'temp'. Deberán cambiarla al instante!
FROM `wp_users`;

UPDATE `authors` SET `role` = 'superadmin' LIMIT 1;
-- Importar posts
INSERT INTO `posts` (`id`, `slug`, `title`, `description`, `content`, `category_id`, `author_id`, `format`, `type`, `status`, `comment_count`, `published_at`, `updated_at`)
SELECT 
	`wp_posts`.`ID` AS `id`,
	`wp_posts`.`post_name` AS `slug`,
	`wp_posts`.`post_title` AS `title`,
	'' AS `description`,
	`wp_posts`.`post_content` AS `content`,
	NULL AS `category_id`,
	`wp_posts`.`post_author` AS `author_id`,
	'html' AS `format`,
	`wp_posts`.`post_type` AS `type`,
	`wp_posts`.`post_status` AS `status`,
	`wp_posts`.`comment_count` AS `comment_count`,
	`wp_posts`.`post_date` AS `published_at`,
	`wp_posts`.`post_modified` AS `updated_at`
FROM `wp_posts` WHERE `wp_posts`.`post_type` IN ('post', 'link', 'page') AND `wp_posts`.`post_status` = 'publish';

-- Importar descripciones
UPDATE `posts`, `wp_postmeta`
	SET `posts`.`description` = `wp_postmeta`.`meta_value`
WHERE `wp_postmeta`.`meta_key` = '_aioseop_description' AND `wp_postmeta`.`post_id` = `posts`.`id`;

-- Importar imagenes
-- INSERT INTO `media` 
-- SELECT
-- 	`wp_posts`.`post_mime_type` AS `type`,
-- 	`wp_posts`.`post_title` AS `title`,
-- 	SELECT 
-- 		`meta_value` AS `route` 
-- 		FROM `wp_postmeta` 
-- 		WHERE `wp_postmeta`.`post_id` = `wp_posts`.`post_id` AND `wp_postmeta`.`meta_key` = '_wp_attached_file' AS `route`,
-- FROM `wp_posts` WHERE `wp_posts`.`post_type` = 'attachment' AND `wp_posts`.`post_parent` = `posts`.`id`;



-- Categorías
UPDATE `posts`, `wp_term_relationships`, `categories`, `wp_term_taxonomy`
	SET `posts`.`category_id` = `categories`.`id`
WHERE `wp_term_relationships`.`object_id` = `posts`.`id` AND `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id` AND `wp_term_taxonomy`.`term_id` = (`categories`.`id` - 1);

-- Importar etiquetas y posts
INSERT INTO `post_tags` SELECT `wp_term_relationships`.`object_id` AS `post_id`, `wp_terms`.`term_id` AS `tag_id` FROM `wp_terms`, `wp_term_relationships`, `wp_term_taxonomy` WHERE `wp_term_taxonomy`.`taxonomy` = 'post_tag' AND `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id` AND `wp_terms`.`term_id` = `wp_term_taxonomy`.`term_id`;

-- Importar comentarios
INSERT INTO `comments` (`id`,`post_id`,`author_id`,`author_email`,`author_name`,`author_url`,`author_ip`,`approved`,`content`,`type`,`created_at`,`comment_parent`,`karma`)
SELECT
	`wp_comments`.`comment_ID` AS `id`,
	`wp_comments`.`comment_post_ID` AS `post_id`,
	`wp_comments`.`user_id` AS `author_id`,
	`wp_comments`.`comment_author_email` AS `author_email`,
	`wp_comments`.`comment_author` AS `author_name`,
	`wp_comments`.`comment_author_url` AS `author_url`,
	`wp_comments`.`comment_author_IP` AS `author_ip`,
	`wp_comments`.`comment_approved` AS `approved`,
	`wp_comments`.`comment_content` AS `content`,
	`wp_comments`.`comment_type` AS `type`,
	`wp_comments`.`comment_date` AS `created_at`,
	`wp_comments`.`comment_parent` AS `comment_parent`,
	`wp_comments`.`comment_karma` AS `karma`
FROM `wp_comments` WHERE `comment_post_ID` IN (SELECT `id` FROM `posts`); # Just comments from posts that exists
