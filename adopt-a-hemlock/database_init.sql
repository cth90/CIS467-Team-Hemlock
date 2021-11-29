drop table if exists `aah_transactions`;
drop table if exists `aah_trees`;
drop table if exists `aah_locations`;

CREATE TABLE `aah_trees`
(
    `id`          int AUTO_INCREMENT,
    `tag`         int NOT NULL,
    `dbh`         int,
    `latitude`    varchar(255),
    `longitude`   varchar(255),
    `location_id` int,
    `notes`       varchar(255),
    PRIMARY KEY (id),
    UNIQUE (tag)
);

CREATE TABLE `aah_locations`
(
    `id`      int AUTO_INCREMENT,
    `name`    varchar(255),
    `parcel`  varchar(255),
    `address` varchar(255),
    PRIMARY KEY (id)
);

CREATE TABLE `aah_transactions`
(
    `id`          int AUTO_INCREMENT,
    `name`        varchar(255),
    `email`       varchar(255),
    `payment_id`  varchar(30),
    `adoption_id` varchar(40),
    `amt_donated` DECIMAL(13, 2),
    `anonymous`   boolean,
    `completed`   boolean DEFAULT FALSE,
	`tree_id`     int,
	`pdf_link`    varchar(255),
    PRIMARY KEY (id),
	UNIQUE (tree_id),
	UNIQUE (adoption_id)
);

ALTER TABLE `aah_transactions`
    ADD FOREIGN KEY (`tree_id`) REFERENCES `aah_trees` (`id`);

ALTER TABLE `aah_trees`
    ADD FOREIGN KEY (`location_id`) REFERENCES `aah_locations` (`id`);

INSERT INTO aah_locations (name, parcel, address)
VALUES ('Duncan Woods', '70-03-29-200-010', 'Grand Haven, MI 49417');

INSERT INTO aah_locations (name, parcel, address)
VALUES ('Cemetery', '70-03-29-325-016', '1304 Lake Ave Cemetery, Grand Haven, MI 49417');

INSERT INTO aah_locations (name, parcel, address)
VALUES ('Mulligan''s Hollow', '70-03-29-160-004', '600 Mulligans Hollow Dr, Grand Haven, MI 49417');