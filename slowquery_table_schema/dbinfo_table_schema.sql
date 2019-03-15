CREATE TABLE `dbinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) DEFAULT NULL,
  `dbname` varchar(100) DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `pwd` varchar(100) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
