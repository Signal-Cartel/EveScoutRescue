# EveScoutRescue
Web site and data tools for evescoutrescue.com

Steps to setup a development system
-----------------------------------

* create a new database scheme with the following structure (auto increment values may depend on current data dump)

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
      `requestdate` datetime default current_timestamp(),
      `canrefit` int,
      `finished` int default 0,
      `note` text COLLATE latin1_general_ci,
       PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

* copy the folders and files to the web site (ignore .git*, .settings, .project, .buildpath files and folders)

* create a local "esr_dbconfig.ini" with the following entries

    hostname=<dbHostname>
    username=<dbUserName>
    password=<dbPasswd>
    dbname=<databaseName>
    
* place the file one level above the "esrc" directory (out of range for web server document root)

* import the wormhole data ()

* import a data dump if you want to work with real data. Otherwise prepare some data on your own (We should provide a small demo data generate script). 
