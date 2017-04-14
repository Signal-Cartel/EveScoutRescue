# EveScoutRescue
Web site and data tools for evescoutrescue.com

CREATE TABLE `activity` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `ActivityDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `Pilot` varchar(255) COLLATE latin1_general_ci NOT NULL,
 `EntryType` varchar(25) COLLATE latin1_general_ci NOT NULL,
 `System` varchar(8) COLLATE latin1_general_ci NOT NULL,
 `AidedPilot` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
 `Note` text COLLATE latin1_general_ci,
 `IP` varchar(25) COLLATE latin1_general_ci NOT NULL,
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
