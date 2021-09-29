CREATE TABLE `aah_trees` (
  `tag` int PRIMARY KEY,
  `surveyor_id` int,
  `survey_date` datetime,
  `county_id` int,
  `acreage` float,
  `parcel` varchar(255),
  `address` varchar(255),
  `dbh` int,
  `latitude` float,
  `longitude` float,
  `location_id` int,
  `notes` varchar(255)
);

CREATE TABLE `aah_counties` (
  `id` int PRIMARY KEY,
  `county` varchar(255)
);

CREATE TABLE `aah_surveyor` (
  `id` int PRIMARY KEY,
  `name` varchar(255)
);

CREATE TABLE `aah_locations` (
  `id` int PRIMARY KEY,
  `name` varchar(255)
);

CREATE TABLE `aah_customers` (
  `id` int PRIMARY KEY,
  `first_name` varchar(255),
  `last_name` varchar(255)
);

CREATE TABLE `aah_transactions` (
  `id` int PRIMARY KEY,
  `paypal_id` int,
  `tree_id` int,
  `customer_id` int,
  `anonymous` boolean
);

ALTER TABLE `aah_surveyor` ADD FOREIGN KEY (`id`) REFERENCES `aah_trees` (`surveyor_id`);

ALTER TABLE `aah_counties` ADD FOREIGN KEY (`id`) REFERENCES `aah_trees` (`county_id`);

ALTER TABLE `aah_customers` ADD FOREIGN KEY (`id`) REFERENCES `aah_transactions` (`customer_id`);

ALTER TABLE `aah_trees` ADD FOREIGN KEY (`tag`) REFERENCES `aah_transactions` (`tree_id`);

ALTER TABLE `aah_locations` ADD FOREIGN KEY (`id`) REFERENCES `aah_trees` (`location_id`);
