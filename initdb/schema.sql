CREATE DATABASE IF NOT EXISTS blog;
USE blog;

CREATE TABLE IF NOT EXISTS articles (
  id int NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  url varchar(255) NOT NULL,
  timestamp timestamp NOT NULL,
  content longtext NOT NULL,
  hidden tinyint(1) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY url (url)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS article_tags (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  url varchar(255) NOT NULL,
  tag_id smallint UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS tags (
  tag_id smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  tag_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (tag_id),
  UNIQUE KEY tag_name (tag_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
