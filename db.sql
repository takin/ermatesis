-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: erma
-- ------------------------------------------------------
-- Server version	5.5.34-0ubuntu0.13.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bestextraction`
--

DROP TABLE IF EXISTS `bestextraction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestextraction` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `match` varchar(255) NOT NULL,
  `score` varchar(10) NOT NULL,
  `iterasi` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bestextraction`
--

LOCK TABLES `bestextraction` WRITE;
/*!40000 ALTER TABLE `bestextraction` DISABLE KEYS */;
/*!40000 ALTER TABLE `bestextraction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `occurences`
--

DROP TABLE IF EXISTS `occurences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `occurences` (
  `Match` varchar(255) DEFAULT NULL,
  `CountOfMatch` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `occurences`
--

LOCK TABLES `occurences` WRITE;
/*!40000 ALTER TABLE `occurences` DISABLE KEYS */;
/*!40000 ALTER TABLE `occurences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patternset`
--

DROP TABLE IF EXISTS `patternset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patternset` (
  `ID` int(11) DEFAULT NULL,
  `left` varchar(255) DEFAULT NULL,
  `match` varchar(255) DEFAULT NULL,
  `right` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patternset`
--

LOCK TABLES `patternset` WRITE;
/*!40000 ALTER TABLE `patternset` DISABLE KEYS */;
INSERT INTO `patternset` VALUES (4,'Thus begins','The Blair Witch Project',', one'),(6,'that','Titanic','is'),(9,NULL,'Titanic','in'),(1,'to','Titanic','in'),(2,'to','The Blair Witch Project','in'),(3,'that','the blair witch project','is'),(5,'that','The Blair Witch Project','is'),(7,'of','The Blair Witch Project','and'),(8,'of','The Matrix','and'),(14,'to','titanic .','in'),(10,'of','The Blair Witch Project','and'),(11,'to','The Matrix','in'),(12,'that','The Shawshank Redemption','is'),(13,'that','Episode 1','is'),(15,'that','The Matrix','is'),(16,'that','The Motorcycle Diaries','is'),(17,'to','The Shawshank Redemption','in'),(18,'of','Minority Report','and'),(19,'of','The Texas Chainsaw Massacre','and'),(20,'.','The Matrix','may');
/*!40000 ALTER TABLE `patternset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultoccurence`
--

DROP TABLE IF EXISTS `resultoccurence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultoccurence` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `no` int(11) NOT NULL,
  `left` varchar(255) NOT NULL,
  `match` varchar(255) NOT NULL,
  `right` varchar(255) NOT NULL,
  `iterasi` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultoccurence`
--

LOCK TABLES `resultoccurence` WRITE;
/*!40000 ALTER TABLE `resultoccurence` DISABLE KEYS */;
/*!40000 ALTER TABLE `resultoccurence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultpattern`
--

DROP TABLE IF EXISTS `resultpattern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultpattern` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(10) NOT NULL,
  `left` varchar(255) NOT NULL,
  `right` varchar(255) NOT NULL,
  `Fi` int(10) NOT NULL,
  `Ni` int(10) DEFAULT NULL,
  `RlogF` varchar(10) DEFAULT NULL,
  `iterasi` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultpattern`
--

LOCK TABLES `resultpattern` WRITE;
/*!40000 ALTER TABLE `resultpattern` DISABLE KEYS */;
/*!40000 ALTER TABLE `resultpattern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seedlexicon`
--

DROP TABLE IF EXISTS `seedlexicon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seedlexicon` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `seed` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seedlexicon`
--

LOCK TABLES `seedlexicon` WRITE;
/*!40000 ALTER TABLE `seedlexicon` DISABLE KEYS */;
INSERT INTO `seedlexicon` VALUES (43,'Titanic'),(42,'The Blair Witch Project');
/*!40000 ALTER TABLE `seedlexicon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skorekstraksi`
--

DROP TABLE IF EXISTS `skorekstraksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skorekstraksi` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `match` varchar(255) NOT NULL,
  `no` varchar(10) NOT NULL,
  `RlogF` varchar(10) NOT NULL,
  `iterasi` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skorekstraksi`
--

LOCK TABLES `skorekstraksi` WRITE;
/*!40000 ALTER TABLE `skorekstraksi` DISABLE KEYS */;
/*!40000 ALTER TABLE `skorekstraksi` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-02-06 13:40:29
