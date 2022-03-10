SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

USE `USERNAME`;

DROP TABLE IF EXISTS `ccd_contenucommande`;
DROP TABLE IF EXISTS `ccd_commande`;
DROP TABLE IF EXISTS `ccd_boite`;
DROP TABLE IF EXISTS `ccd_produit`;
DROP TABLE IF EXISTS `ccd_categorie`;
DROP TABLE IF EXISTS `ccd_users`;

CREATE TABLE `ccd_boite`
(
    `id`       int(11) NOT NULL,
    `taille`   text    NOT NULL,
    `poidsmax` float   NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `ccd_boite` (`id`, `taille`, `poidsmax`)
VALUES (1, 'petite', 0.7),
       (2, 'moyenne', 1.5),
       (3, 'grande', 3.2);

CREATE TABLE `ccd_categorie`
(
    `id`  int(11) NOT NULL,
    `nom` text    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `ccd_categorie` (`id`, `nom`)
VALUES (1, 'Beauté'),
       (2, 'Bijoux'),
       (3, 'Décoration'),
       (4, 'Produit ménager'),
       (5, 'Upcycling');

CREATE TABLE `ccd_produit`
(
    `id`          int(11) NOT NULL,
    `titre`       text    NOT NULL,
    `description` text    NOT NULL,
    `categorie`   int(11) NOT NULL,
    `poids`       float   NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `ccd_produit` (`id`, `titre`, `description`, `categorie`, `poids`)
VALUES (1, 'Crème', 'Une crème hydratante et parfumée qui rend la peau douce', 1, 0.3),
       (2, 'Savon', 'Un savon qui respecte la peau', 1, 0.2),
       (3, 'Shampoing', 'Shampoing doux et démêlant', 1, 0.4),
       (4, 'Bracelet', 'Un bracelet en tissu aux couleurs plaisantes', 2, 0.15),
       (5, 'Tableau', 'Aquarelle ou peinture à l\'huile', 3, 0.6),
       (6, 'Essuie-main', 'Utile au quotidien', 4, 0.45),
       (7, 'Gel', 'Gel hydroalcoolique et Antibactérien', 4, 0.25),
       (8, 'Masque', 'masque chirurgical jetable categorie 1', 4, 0.35),
       (9, 'Gilet', 'Gilet décoré par nos couturières', 5, 0.55),
       (10, 'Marque page', 'Joli marque page pour accompagner vos lectures favorites', 5, 0.1),
       (11, 'Sac', 'Sac éco-responsable avec décorations variées', 5, 0.26),
       (12, 'Surprise', 'Pochette surprise pour faire plaisir aux petits et grands', 5, 0.7),
       (13, 'T-shirt', 'T-shirt peint à la main et avec pochoir', 5, 0.32);

CREATE TABLE `ccd_users`
(
    `user_id`    int(11)      NOT NULL,
    `username`   varchar(20)  NOT NULL,
    `lastname`   varchar(40)  NOT NULL,
    `firstname`  varchar(40)  NOT NULL,
    `password`   varchar(255) NOT NULL,
    `mail`       varchar(100) NOT NULL,
    `phone`      varchar(100) NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated`    timestamp    NULL     DEFAULT NULL,
    `last_login` timestamp    NULL     DEFAULT NULL,
    `last_ip`    varchar(80)  NOT NULL DEFAULT '',
    `is_admin`   tinyint(1)   NOT NULL DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `ccd_users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `username` (`username`),
    ADD UNIQUE KEY `mail` (`mail`);

ALTER TABLE `ccd_users`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

CREATE TABLE `ccd_commande`
(
    `id`       int(11) NOT NULL,
    `id_user`  int(11) NOT NULL,
    `id_boite` int(11) NOT NULL,
    `couleur_boite` varchar(255) NOT NULL DEFAULT '#000000',
    `paye`     BOOLEAN
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;



ALTER TABLE `ccd_boite`
    ADD PRIMARY KEY (`id`);
ALTER TABLE `ccd_categorie`
    ADD PRIMARY KEY (`id`);
ALTER TABLE `ccd_produit`
    ADD PRIMARY KEY (`id`),
    ADD KEY `categorie` (`categorie`);

ALTER TABLE `ccd_commande`
    ADD PRIMARY KEY (`id`);
ALTER TABLE `ccd_commande`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ccd_commande`
    ADD CONSTRAINT `commande_fk_user` FOREIGN KEY (`id_user`) REFERENCES `ccd_users` (`user_id`);
ALTER TABLE `ccd_commande`
    ADD CONSTRAINT `commande_fk_boite` FOREIGN KEY (`id_boite`) REFERENCES `ccd_boite` (`id`);

CREATE TABLE `ccd_contenucommande`
(
    `id_commande` int(11) NOT NULL,
    `id_produit`  int(11) NOT NULL,
    `qte` int(11) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `ccd_contenucommande`
    ADD CONSTRAINT `contenucommande_fk_commande` FOREIGN KEY (`id_commande`) REFERENCES `ccd_commande` (`id`);
ALTER TABLE `ccd_contenucommande`
    ADD CONSTRAINT `contenucommande_fk_boite` FOREIGN KEY (`id_produit`) REFERENCES `ccd_produit` (`id`);
ALTER TABLE `ccd_contenucommande`
    ADD PRIMARY KEY (`id_commande`, `id_produit`);

ALTER TABLE `ccd_boite`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 4;
ALTER TABLE `ccd_categorie`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 6;
ALTER TABLE `ccd_produit`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 14;
ALTER TABLE `ccd_produit`
    ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`categorie`) REFERENCES `ccd_categorie` (`id`);
COMMIT;