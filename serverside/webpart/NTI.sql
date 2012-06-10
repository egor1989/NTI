-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 02, 2012 at 05:04 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `NTI`
--

-- --------------------------------------------------------

--
-- Table structure for table `EmailApproveKey`
--

CREATE TABLE IF NOT EXISTS `EmailApproveKey` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `Key` varchar(64) NOT NULL,
  `Deleted` int(11) NOT NULL DEFAULT '0',
  `Insert_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Unixtimestamp` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Key` (`Key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIEntry`
--

CREATE TABLE IF NOT EXISTS `NTIEntry` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL DEFAULT '-3',
  `accx` double NOT NULL,
  `accy` double NOT NULL,
  `distance` double NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `direction` double NOT NULL,
  `compass` double NOT NULL,
  `speed` double NOT NULL,
  `utimestamp` double NOT NULL,
  `Insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FileId` int(11) NOT NULL,
  `Deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIFeedback`
--

CREATE TABLE IF NOT EXISTS `NTIFeedback` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `UID` int(11) NOT NULL DEFAULT '-3',
  `InsertTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIFile`
--

CREATE TABLE IF NOT EXISTS `NTIFile` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `Insert_Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `File` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Type` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=519 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIKeys`
--

CREATE TABLE IF NOT EXISTS `NTIKeys` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SID` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Deleted` int(11) NOT NULL DEFAULT '0',
  `Insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UID` int(11) NOT NULL,
  `Creation_Date` int(11) NOT NULL,
  `device` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `carrier` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `device` (`device`),
  KEY `model` (`model`),
  KEY `version` (`version`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=525 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIQuest`
--

CREATE TABLE IF NOT EXISTS `NTIQuest` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `InsertDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Deleted` int(11) NOT NULL DEFAULT '0',
  `UID` int(11) NOT NULL,
  `Company` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Age` int(11) NOT NULL,
  `Sex` int(11) NOT NULL,
  `Skill` int(11) NOT NULL,
  `Dtp` int(11) NOT NULL,
  `Autotype` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Autopower` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIRelations`
--

CREATE TABLE IF NOT EXISTS `NTIRelations` (
  `ExpertID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  PRIMARY KEY (`ExpertID`,`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `NTIRequests`
--

CREATE TABLE IF NOT EXISTS `NTIRequests` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ExpertId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `Insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIUserDrivingEntry`
--

CREATE TABLE IF NOT EXISTS `NTIUserDrivingEntry` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `accx` double NOT NULL,
  `accy` double NOT NULL,
  `distance` double NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `direction` double NOT NULL,
  `compass` double NOT NULL,
  `speed` double NOT NULL,
  `utimestamp` double NOT NULL,
  `Insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Deleted` int(11) NOT NULL DEFAULT '0',
  `DrivingID` int(11) NOT NULL,
  `Blat` double NOT NULL,
  `Blng` double NOT NULL,
  `sevAcc` int(11) NOT NULL,
  `TypeAcc` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sevTurn` int(11) NOT NULL,
  `TurnType` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `TypeSpeed` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sevSpeed` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `UID` (`UID`),
  KEY `accx` (`accx`),
  KEY `accy` (`accy`),
  KEY `lat` (`lat`,`lng`),
  KEY `lat_2` (`lat`),
  KEY `lng` (`lng`),
  KEY `speed` (`speed`),
  KEY `utimestamp` (`utimestamp`),
  KEY `Insert_timestamp` (`Insert_timestamp`),
  KEY `Blat` (`Blat`,`Blng`),
  KEY `Blat_2` (`Blat`),
  KEY `Blng` (`Blng`),
  KEY `sevAcc` (`sevAcc`),
  KEY `TypeAcc` (`TypeAcc`),
  KEY `sevTurn` (`sevTurn`),
  KEY `TurnType` (`TurnType`),
  KEY `TypeSpeed` (`TypeSpeed`),
  KEY `sevSpeed` (`sevSpeed`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73418 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIUserDrivingTrack`
--

CREATE TABLE IF NOT EXISTS `NTIUserDrivingTrack` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `TotalAcc1Count` int(11) NOT NULL DEFAULT '0',
  `TotalAcc2Count` int(11) NOT NULL DEFAULT '0',
  `TotalAcc3Count` int(11) NOT NULL DEFAULT '0',
  `TotalBrake1Count` int(11) NOT NULL DEFAULT '0',
  `TotalBrake2Count` int(11) NOT NULL DEFAULT '0',
  `TotalBrake3Count` int(11) NOT NULL DEFAULT '0',
  `TotalSpeed1Count` int(11) NOT NULL DEFAULT '0',
  `TotalSpeed2Count` int(11) NOT NULL DEFAULT '0',
  `TotalSpeed3Count` int(11) NOT NULL DEFAULT '0',
  `TotalTurn1Count` int(11) NOT NULL DEFAULT '0',
  `TotalTurn2Count` int(11) NOT NULL DEFAULT '0',
  `TotalTurn3Count` int(11) NOT NULL DEFAULT '0',
  `TimeStart` int(11) NOT NULL DEFAULT '0',
  `TimeEnd` int(11) NOT NULL DEFAULT '0',
  `TotalDistance` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`,`UID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Table structure for table `NTIUsers`
--

CREATE TABLE IF NOT EXISTS `NTIUsers` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Login` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Email` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `FName` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `SName` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Registration_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Region` int(11) NOT NULL,
  `Rights` int(11) NOT NULL DEFAULT '0',
  `Deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Login` (`Login`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `PasswordRecovery`
--

CREATE TABLE IF NOT EXISTS `PasswordRecovery` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Key` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `UserId` int(11) NOT NULL,
  `Deleted` int(11) NOT NULL DEFAULT '0',
  `Insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UnixTimeStamp` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;
