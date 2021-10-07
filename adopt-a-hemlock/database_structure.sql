CREATE TABLE `aah_trees` (
  `tag` int PRIMARY KEY,
  `dbh` int,
  `latitude` float,
  `longitude` float,
  `location_id` int,
  `notes` varchar(255)
);

CREATE TABLE `aah_locations` (
  `id` int AUTO_INCREMENT,
  `name` varchar(255),
  `parcel` varchar(255),
  `address` varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE `aah_customers` (
  `id` int PRIMARY KEY,
  `first_name` varchar(255),
  `last_name` varchar(255),
  `anonymous` boolean
);

CREATE TABLE `aah_transactions` (
  `id` int PRIMARY KEY,
  `paypal_id` int,
  `tree_id` int,
  `customer_id` int,
  `anonymous` boolean
);

ALTER TABLE `aah_transactions` ADD FOREIGN KEY (`customer_id`) REFERENCES `aah_customers` (`id`);

ALTER TABLE `aah_transactions` ADD FOREIGN KEY (`tree_id`) REFERENCES `aah_trees` (`tag`);

ALTER TABLE `aah_trees` ADD FOREIGN KEY (`location_id`) REFERENCES `aah_locations` (`id`);