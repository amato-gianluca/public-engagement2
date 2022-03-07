CREATE TABLE `keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `lang` char(2) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `researchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idab` bigint NOT NULL UNIQUE,
  `keywords_en` text NOT NULL DEFAULT '',
  `interests_en` text NOT NULL DEFAULT '',
  `demerging_en` text NOT NULL DEFAULT '',
  `awards_en` text NOT NULL DEFAULT '',
  `curriculum_en` text NOT NULL DEFAULT '',
  `keywords_it` text NOT NULL DEFAULT '',
  `interests_it` text NOT NULL DEFAULT '',
  `demerging_it` text NOT NULL DEFAULT '',
  `awards_it` text NOT NULL DEFAULT '',
  `curriculum_it` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `fidx` (`keywords_en`,`interests_en`,`demerging_en`,`awards_en`,`curriculum_en`,`keywords_it`,`interests_it`,`demerging_it`,`awards_it`,`curriculum_it`)
);

CREATE TABLE `researcher_keywords` (
  `id_researcher` int(11) NOT NULL,
  `id_keyword` int(11) NOT NULL,
  `pos` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id_researcher`,`id_keyword`)
);
