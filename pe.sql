CREATE TABLE `keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) DEFAULT NULL,
  `lang` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `researchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(6) DEFAULT NULL,
  `keywords_en` text DEFAULT NULL,
  `interests_en` text DEFAULT NULL,
  `demerging_en` text DEFAULT NULL,
  `position_en` text DEFAULT NULL,
  `awards_en` text DEFAULT NULL,
  `curriculum_en` text DEFAULT NULL,
  `keywords_it` text DEFAULT NULL,
  `interests_it` text DEFAULT NULL,
  `demerging_it` text DEFAULT NULL,
  `position_it` text DEFAULT NULL,
  `awards_it` text DEFAULT NULL,
  `curriculum_it` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `fidx` (`keywords_en`,`interests_en`,`demerging_en`,`position_en`,`awards_en`,`curriculum_en`,`keywords_it`,`interests_it`,`demerging_it`,`position_it`,`awards_it`,`curriculum_it`)
);

CREATE TABLE `researcher_keywords` (
  `id_researcher` int(11) NOT NULL,
  `id_keyword` int(11) NOT NULL,
  `pos` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_researcher`,`id_keyword`)
);
