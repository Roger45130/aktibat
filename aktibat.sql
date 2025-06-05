-- Création de la base de données
CREATE DATABASE IF NOT EXISTS aktibat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE aktibat;

-- Structure de la table `user`
CREATE TABLE user (
  id INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(180) NOT NULL,
  roles JSON NOT NULL,
  password VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (id),
  UNIQUE KEY UNIQ_IDENTIFIER_EMAIL (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `blog`
CREATE TABLE blog (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  content LONGTEXT NOT NULL,
  excerpt LONGTEXT DEFAULT NULL,
  slug VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  published_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime)',
  image VARCHAR(255) DEFAULT NULL,
  is_approved TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `commentaire`
CREATE TABLE commentaire (
  id INT NOT NULL AUTO_INCREMENT,
  blog_id INT DEFAULT NULL,
  name VARCHAR(255) DEFAULT NULL,
  message LONGTEXT NOT NULL,
  created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  is_approved TINYINT(1) DEFAULT NULL,
  is_testimonial TINYINT(1) DEFAULT NULL,
  username VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY IDX_67A068BCDAE07E97 (blog_id),
  CONSTRAINT FK_67A068BCDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `contact`
CREATE TABLE contact (
  id INT NOT NULL AUTO_INCREMENT,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message LONGTEXT NOT NULL,
  created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `cookie_consent`
CREATE TABLE cookie_consent (
  id INT NOT NULL AUTO_INCREMENT,
  type VARCHAR(30) NOT NULL,
  statistiques TINYINT(1) NOT NULL DEFAULT 0,
  marketing TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;