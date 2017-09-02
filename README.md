# EveScoutRescue
Web site and data tools for [evescoutrescue.com](https://evescoutrescue.com/home/).

## Steps to setup a development system
* Create a new database scheme with the following structure (auto increment values may depend on current data dump):
```sql
CREATE TABLE `activity` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`ActivityDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`Pilot` varchar(255) COLLATE latin1_general_ci NOT NULL,
	`EntryType` varchar(25) COLLATE latin1_general_ci NOT NULL,
	`System` varchar(8) COLLATE latin1_general_ci NOT NULL,
	`AidedPilot` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
	`Note` text COLLATE latin1_general_ci,
	`IP` varchar(40) COLLATE latin1_general_ci NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=925 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

CREATE TABLE `cache` (
	`CacheID` int(11) NOT NULL,
	`InitialSeedDate` date DEFAULT NULL,
	`System` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
	`Location` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
	`AlignedWith` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
	`Distance` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
	`Password` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
	`Status` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
	`ExpiresOn` date DEFAULT NULL,
	`LastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`Note` text COLLATE latin1_general_ci,
	PRIMARY KEY (`CacheID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

CREATE TABLE `wh_systems` (
	`System` varchar(8) COLLATE latin1_general_ci NOT NULL,
	`Class` varchar(25) COLLATE latin1_general_ci NOT NULL,
	`Constellation` varchar(25) COLLATE latin1_general_ci NOT NULL,
	`Region` varchar(25) COLLATE latin1_general_ci NOT NULL,
	`DoNotSowUntil` date DEFAULT NULL,
	`Notes` text COLLATE latin1_general_ci NOT NULL,
	PRIMARY KEY (`System`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

CREATE TABLE `rescuerequest` (
	`id` int(11) not NULL AUTO_INCREMENT,
	`system` varchar(8) NOT NULL,
	`pilot` varchar(255) NOT NULL,
	`requestdate` date NOT null,
	`reminderdate` date,
	`canrefit` int not null default 0,
	`launcher` int not null default 0,
	`finished` int not null default 0,
	`lastcontact` date,
	`startagent` varchar(255) not null,
	`closeagent` varchar(255),
	`status` varchar(255) not null default 'new',
	`LastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

CREATE TABLE `rescuenote` (
	`id` int(11) not NULL AUTO_INCREMENT,
	`rescueid` int(11) NOT NULL,
	`notedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`agent` varchar(255) not null,
	`note` text COLLATE latin1_general_ci,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

CREATE TABLE pilots (
	`id` int NOT NULL AUTO_INCREMENT,
	`pilot` varchar(64) COLLATE latin1_general_ci NOT NULL,
	`task` varchar(64) COLLATE latin1_general_ci NOT NULL,
	`active` int NOT NULL default 1,
	primary key(`id`),
	unique index pilotnames (`pilot`, `task`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
```
* Copy the folders and files to the web site (ignore .git*, .settings, .project, .buildpath).
* Create a local "esr_dbconfig.ini" with the following entries:
```ini
hostname=<dbHostname>
username=<dbUsername>
password=<dbPassword>
dbname=<dbName>
```
* Place config file one level above the "esrc" directory (out of range for web server document root).
* Import the wormhole data.
* Import a data dump if you want to work with real data. Otherwise prepare some data on your own (we should provide a small demo data generate script). 

Mysql 5.6 required:
```sql
CREATE TABLE `rescuerequest` (
	`id` int(11) not NULL AUTO_INCREMENT,
	`system` varchar(8) NOT NULL,
	`pilot` varchar(255) NOT NULL,
	`requestdate` datetime default current_timestamp(),
	`reminderdate` datetime,
	`canrefit` int not null default 0,
	`launcher` int not null default 0,
	`finished` int not null default 0,
	`lastcontact` datetime default current_timestamp,
	`startagent` varchar(255) not null,
	`closeagent` varchar(255),
	`status` varchar(255) not null default 'new',
	`LastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

CREATE TABLE `rescuenote` (
	`id` int(11) not NULL AUTO_INCREMENT,
	`rescueid` int(11) NOT NULL,
	`notedate` datetime default current_timestamp(),
	`agent` varchar(255) not null,
	`note` text COLLATE latin1_general_ci,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
```
