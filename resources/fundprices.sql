

/*!40101 SET NAMES utf8 */;

#
# Structure for table "f_prices"
#

CREATE TABLE `f_prices` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `fid` (`fid`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=21793694 DEFAULT CHARSET=utf8;

#
# Structure for table "index_assets"
#

CREATE TABLE `index_assets` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `assets` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `fid` (`fid`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=482 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='List All Fund Reports';

#
# Structure for table "index_fc"
#

CREATE TABLE `index_fc` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(20) NOT NULL DEFAULT '',
  `downloaded` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `fid` (`fid`,`filename`)
) ENGINE=InnoDB AUTO_INCREMENT=3358 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='List All Fund Cards';

#
# Structure for table "index_funds"
#

CREATE TABLE `index_funds` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `sname` varchar(50) NOT NULL DEFAULT '',
  `lname` varchar(100) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `currency` varchar(5) NOT NULL DEFAULT '',
  `family` int(11) NOT NULL DEFAULT '0',
  `umbrella` int(11) NOT NULL DEFAULT '0',
  `aclass` varchar(30) NOT NULL DEFAULT '',
  `risk` int(11) NOT NULL DEFAULT '0',
  `kiid` varchar(20) NOT NULL DEFAULT '',
  `fc` varchar(20) NOT NULL DEFAULT '',
  `report` varchar(20) NOT NULL DEFAULT '',
  `rdate` date NOT NULL DEFAULT '0000-00-00',
  `assets` decimal(10,2) NOT NULL DEFAULT '0.00',
  `adate` date NOT NULL DEFAULT '0000-00-00',
  `price` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `pdate` date NOT NULL DEFAULT '0000-00-00',
  `policy` text NOT NULL,
  `class` varchar(5) NOT NULL DEFAULT '',
  `isin` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `fid` (`fid`)
) ENGINE=InnoDB AUTO_INCREMENT=41881 DEFAULT CHARSET=utf8;

#
# Structure for table "index_kiids"
#

CREATE TABLE `index_kiids` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(30) NOT NULL DEFAULT '',
  `downloaded` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `fid` (`fid`,`filename`)
) ENGINE=InnoDB AUTO_INCREMENT=9148 DEFAULT CHARSET=utf8 COMMENT='Lista all Funds Kiids';

#
# Structure for table "index_reports"
#

CREATE TABLE `index_reports` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(20) NOT NULL DEFAULT '',
  `downloaded` enum('N','Y') NOT NULL DEFAULT 'N',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `fid` (`fid`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=8104 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='List All Fund Reports';

#
# Structure for table "index_umbrella"
#

CREATE TABLE `index_umbrella` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=995 DEFAULT CHARSET=utf8 COMMENT='Umbrella List';

#
# Structure for table "log"
#

CREATE TABLE `log` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(100) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `level` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin2;

#
# Trigger "NewTrigger"
#

CREATE TRIGGER `NewTrigger`
  AFTER UPDATE ON `index_funds`
  FOR EACH ROW
BEGIN
  IF `NEW`.`kiid` <> `OLD`.`kiid` THEN
    INSERT
      INTO `index_kiids`
      (`filename`, `fid`)
    VALUES
      (`NEW`.`kiid`, `NEW`.`fid`) ON DUPLICATE KEY UPDATE `filename` = `NEW`.`kiid`;
  END IF;
  IF `NEW`.`report` <> `OLD`.`report` THEN
    INSERT
      INTO `index_reports`
      (`filename`, `fid`, `date`)
    VALUES
      (`NEW`.`report`, `NEW`.`fid`, `NEW`.`rdate`) ON DUPLICATE KEY UPDATE `filename` = `NEW`.`report`;
  END IF;
  IF `NEW`.`fc` <> `OLD`.`fc` THEN
    INSERT
      INTO `index_fc`
      (`filename`, `fid`)
    VALUES
      (`NEW`.`fc`, `NEW`.`fid`) ON DUPLICATE KEY UPDATE `filename` = `NEW`.`fc`;
  END IF;
  IF `NEW`.`assets` <> `OLD`.`assets` OR `NEW`.`adate` <> `OLD`.`adate` THEN
    INSERT
      INTO `index_assets`
      (`assets`, `fid`, `date`)
    VALUES
      (`NEW`.`assets`, `NEW`.`fid`, `NEW`.`adate`) ON DUPLICATE KEY UPDATE `assets` = `NEW`.`assets`, `date` = `NEW`.`adate`;
  END IF;
END;
#
# Trigger "NewTrigger2"
#

CREATE TRIGGER `NewTrigger2`
  AFTER INSERT ON `index_funds`
  FOR EACH ROW
BEGIN
  IF LENGTH(`NEW`.`kiid`) > 0 THEN
    INSERT
      INTO `index_kiids`
      (`filename`, `fid`)
    VALUES
      (`NEW`.`kiid`, `NEW`.`fid`) ON DUPLICATE KEY UPDATE `filename` = `NEW`.`kiid`;
  END IF;
  IF LENGTH(`NEW`.`report`) > 0 THEN
    INSERT
      INTO `index_reports`
      (`filename`, `fid`, `date`)
    VALUES
      (`NEW`.`report`, `NEW`.`fid`, `NEW`.`rdate`) ON DUPLICATE KEY UPDATE `filename` = `NEW`.`report`;
  END IF;
  IF LENGTH(`NEW`.`fc`) > 0 THEN
    INSERT
      INTO `index_fc`
      (`filename`, `fid`)
    VALUES
      (`NEW`.`fc`, `NEW`.`fid`) ON DUPLICATE KEY UPDATE `filename` = `NEW`.`fc`;
  END IF;
  IF LENGTH(`NEW`.`assets`) > 0 AND `NEW`.`adate` > 0 THEN
    INSERT
      INTO `index_assets`
      (`assets`, `fid`, `date`)
    VALUES
      (`NEW`.`assets`, `NEW`.`fid`, `NEW`.`adate`) ON DUPLICATE KEY UPDATE `assets` = `NEW`.`assets`, `date` = `NEW`.`adate`;
  END IF;
END;