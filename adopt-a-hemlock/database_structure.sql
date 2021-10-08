CREATE TABLE `aah_trees`
(
    `id`          int AUTO_INCREMENT,
    `tag`         varchar(8),
    `dbh`         int,
    `latitude`    float,
    `longitude`   float,
    `location_id` int,
    `notes`       varchar(255),
    PRIMARY KEY (id)
);

CREATE TABLE `aah_locations`
(
    `id`      int AUTO_INCREMENT,
    `name`    varchar(255),
    `parcel`  varchar(255),
    `address` varchar(255),
    PRIMARY KEY (id)
);

CREATE TABLE `aah_customers`
(
    `id`         int PRIMARY KEY,
    `first_name` varchar(255),
    `last_name`  varchar(255),
    `anonymous`  boolean
);

CREATE TABLE `aah_transactions`
(
    `id`          int PRIMARY KEY,
    `paypal_id`   int,
    `tree_id`     int,
    `customer_id` int,
    `anonymous`   boolean
);

ALTER TABLE `aah_transactions`
    ADD FOREIGN KEY (`customer_id`) REFERENCES `aah_customers` (`id`);

ALTER TABLE `aah_transactions`
    ADD FOREIGN KEY (`tree_id`) REFERENCES `aah_trees` (`tag`);

ALTER TABLE `aah_trees`
    ADD FOREIGN KEY (`location_id`) REFERENCES `aah_locations` (`id`);

INSERT INTO aah_locations (name, parcel, address)
VALUES ('Duncan Woods', '70-03-29-200-010', 'Grand Haven, MI 49417');

INSERT INTO aah_locations (name, parcel, address)
VALUES ('Cemetery', '70-03-29-325-016', '1304 Lake Ave Cemetery, Grand Haven, MI 49417');

INSERT INTO aah_locations (name, parcel, address)
VALUES ('Mulligan''s Hollow', '70-03-29-160-004', '600 Mulligans Hollow Dr, Grand Haven, MI 49417');