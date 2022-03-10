CREATE TABLE `ccd_commande`
(
    `id`       int(11) NOT NULL,
    `id_user`  int(11) NOT NULL,
    `id_boite` int(11) NOT NULL,
    `paye`     BOOLEAN
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `ccd_commande`
    ADD PRIMARY KEY (`id`);
ALTER TABLE `ccd_commande`
    ADD CONSTRAINT `commande_fk_user` FOREIGN KEY (`id_user`) REFERENCES `ccd_user` (`id`);
ALTER TABLE `ccd_commande`
    ADD CONSTRAINT `commande_fk_boite` FOREIGN KEY (`id_boite`) REFERENCES `ccd_boite` (`id`);

CREATE TABLE `ccd_contenucommande`
(
    `id_commande` int(11) NOT NULL,
    `id_produit`  int(11) NOT NULL,
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `ccd_contenucommande`
    ADD CONSTRAINT `contenucommande_fk_commande` FOREIGN KEY (`id_commande`) REFERENCES `ccd_commande` (`id`);
ALTER TABLE `ccd_contenucommande`
    ADD CONSTRAINT `contenucommande_fk_boite` FOREIGN KEY (`id_produit`) REFERENCES `ccd_produit` (`id`);
ALTER TABLE `ccd_contenucommande`
    ADD PRIMARY KEY (`id-commande,id_produit`);