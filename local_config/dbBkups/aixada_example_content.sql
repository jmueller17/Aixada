-- MySQL dump 10.11
--
-- Host: localhost    Database: toytic_aixada
-- ------------------------------------------------------
-- Server version	5.0.95-community

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
-- Table structure for table `aixada_account`
--

DROP TABLE IF EXISTS `aixada_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_account` (
  `id` int(11) NOT NULL auto_increment,
  `account_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `payment_method_id` tinyint(4) default '1',
  `currency_id` tinyint(4) default '1',
  `description` varchar(255) default NULL,
  `operator_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `balance` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`),
  KEY `ts` (`ts`),
  KEY `operator_id` (`operator_id`),
  KEY `payment_method_id` (`payment_method_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `aixada_account_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `aixada_user` (`id`),
  CONSTRAINT `aixada_account_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `aixada_payment_method` (`id`),
  CONSTRAINT `aixada_account_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `aixada_currency` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_account`
--

LOCK TABLES `aixada_account` WRITE;
/*!40000 ALTER TABLE `aixada_account` DISABLE KEYS */;
INSERT INTO `aixada_account` VALUES (1,-3,'0.00',11,1,'cashbox setup',1,'2012-10-18 16:07:54','0.00'),(2,-2,'0.00',11,1,'consum setup',1,'2012-10-18 16:07:54','0.00'),(3,-1,'0.00',11,1,'maintenance setup',1,'2012-10-18 16:07:54','0.00'),(4,1001,'0.00',11,1,'admin account setup',1,'2012-10-18 16:07:54','0.00'),(5,1002,'0.00',11,1,'account setup',1,'2012-10-18 17:02:29','0.00'),(6,1003,'0.00',11,1,'account setup',2,'2012-10-19 09:39:26','0.00');
/*!40000 ALTER TABLE `aixada_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_cart`
--

DROP TABLE IF EXISTS `aixada_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_cart` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `uf_id` int(11) NOT NULL,
  `date_for_shop` date NOT NULL,
  `operator_id` int(11) default NULL,
  `ts_validated` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uf_id` (`uf_id`,`date_for_shop`,`ts_validated`),
  KEY `date_for_shop` (`date_for_shop`),
  KEY `operator_id` (`operator_id`),
  CONSTRAINT `aixada_cart_ibfk_1` FOREIGN KEY (`uf_id`) REFERENCES `aixada_uf` (`id`),
  CONSTRAINT `aixada_cart_ibfk_2` FOREIGN KEY (`operator_id`) REFERENCES `aixada_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_cart`
--

LOCK TABLES `aixada_cart` WRITE;
/*!40000 ALTER TABLE `aixada_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `aixada_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_currency`
--

DROP TABLE IF EXISTS `aixada_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_currency` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `one_euro` decimal(10,4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_currency`
--

LOCK TABLES `aixada_currency` WRITE;
/*!40000 ALTER TABLE `aixada_currency` DISABLE KEYS */;
INSERT INTO `aixada_currency` VALUES (1,'Euro','1.0000'),(2,'Solidary Currency','1.0000');
/*!40000 ALTER TABLE `aixada_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_incident`
--

DROP TABLE IF EXISTS `aixada_incident`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_incident` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) NOT NULL,
  `incident_type_id` tinyint(4) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `details` text,
  `priority` int(11) default '3',
  `ufs_concerned` varchar(100) default NULL,
  `commission_concerned` varchar(100) default NULL,
  `provider_concerned` varchar(100) default NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` varchar(10) default 'Open',
  PRIMARY KEY  (`id`),
  KEY `incident_type_id` (`incident_type_id`),
  KEY `operator_id` (`operator_id`),
  KEY `ts` (`ts`),
  CONSTRAINT `aixada_incident_ibfk_1` FOREIGN KEY (`incident_type_id`) REFERENCES `aixada_incident_type` (`id`),
  CONSTRAINT `aixada_incident_ibfk_2` FOREIGN KEY (`operator_id`) REFERENCES `aixada_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_incident`
--

LOCK TABLES `aixada_incident` WRITE;
/*!40000 ALTER TABLE `aixada_incident` DISABLE KEYS */;
/*!40000 ALTER TABLE `aixada_incident` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_incident_type`
--

DROP TABLE IF EXISTS `aixada_incident_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_incident_type` (
  `id` tinyint(4) NOT NULL auto_increment,
  `description` varchar(255) NOT NULL,
  `definition` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_incident_type`
--

LOCK TABLES `aixada_incident_type` WRITE;
/*!40000 ALTER TABLE `aixada_incident_type` DISABLE KEYS */;
INSERT INTO `aixada_incident_type` VALUES (1,'internal','incidents are restricted to loggon in users.'),(2,'internal + email','like 1 + incidents are send out as email if possible'),(3,'internal + portal','like 1 + incidents are posted on the portal'),(4,'internal + email + portal','Incidents are posted internally, send out as email and posted on the portal');
/*!40000 ALTER TABLE `aixada_incident_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_iva_type`
--

DROP TABLE IF EXISTS `aixada_iva_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_iva_type` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `percent` decimal(10,2) NOT NULL,
  `description` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_iva_type`
--

LOCK TABLES `aixada_iva_type` WRITE;
/*!40000 ALTER TABLE `aixada_iva_type` DISABLE KEYS */;
INSERT INTO `aixada_iva_type` VALUES (1,'no tax','0.00','the best'),(2,'10 percent','10.00','group XYZ products'),(3,'Alcohol','21.00','for all alcoholic beverages');
/*!40000 ALTER TABLE `aixada_iva_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_member`
--

DROP TABLE IF EXISTS `aixada_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_member` (
  `id` int(11) NOT NULL auto_increment,
  `custom_member_ref` varchar(100) default NULL,
  `uf_id` int(11) default NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `nif` varchar(15) default NULL,
  `zip` varchar(10) default NULL,
  `city` varchar(255) NOT NULL,
  `phone1` varchar(50) default NULL,
  `phone2` varchar(50) default NULL,
  `web` varchar(255) default NULL,
  `picture` varchar(255) default NULL,
  `notes` text,
  `active` tinyint(4) default '1',
  `participant` tinyint(1) default '1',
  `adult` tinyint(1) default '1',
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `uf_id` (`uf_id`),
  CONSTRAINT `aixada_member_ibfk_1` FOREIGN KEY (`uf_id`) REFERENCES `aixada_uf` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_member`
--

LOCK TABLES `aixada_member` WRITE;
/*!40000 ALTER TABLE `aixada_member` DISABLE KEYS */;
INSERT INTO `aixada_member` VALUES (1,NULL,1,'admin','',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,1,1,1,'2012-10-18 16:07:54'),(2,'',1,'Jörg Müller','','','','','0','','',NULL,'',1,1,1,'2012-10-18 17:03:10'),(3,'',1,'Julian Pfeifle','','','','','0','','',NULL,'',1,1,1,'2012-10-18 17:03:54'),(4,'',2,'John English','','','','','0','','',NULL,'',1,1,1,'2012-10-18 17:05:48'),(5,'',2,'Jose Spanish','','','','','0','','',NULL,'',1,1,1,'2012-10-18 17:06:22'),(6,'',3,'Pau Petzl','','','','','0912309123','','',NULL,'',1,1,1,'2012-10-19 09:40:38');
/*!40000 ALTER TABLE `aixada_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_order`
--

DROP TABLE IF EXISTS `aixada_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_order` (
  `id` int(11) NOT NULL auto_increment,
  `provider_id` int(11) NOT NULL,
  `date_for_order` date NOT NULL,
  `ts_sent_off` timestamp NOT NULL default '0000-00-00 00:00:00',
  `date_received` date default NULL,
  `date_for_shop` date default NULL,
  `total` decimal(10,2) default '0.00',
  `notes` varchar(255) default NULL,
  `revision_status` int(11) default '1',
  `delivery_ref` varchar(255) default NULL,
  `payment_ref` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `date_for_order_2` (`date_for_order`,`provider_id`,`ts_sent_off`),
  KEY `date_for_order` (`date_for_order`),
  KEY `date_for_shop` (`date_for_shop`),
  KEY `ts_sent_off` (`ts_sent_off`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `aixada_order_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `aixada_provider` (`id`),
  CONSTRAINT `aixada_order_ibfk_2` FOREIGN KEY (`date_for_order`) REFERENCES `aixada_product_orderable_for_date` (`date_for_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_order`
--

LOCK TABLES `aixada_order` WRITE;
/*!40000 ALTER TABLE `aixada_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `aixada_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_order_item`
--

DROP TABLE IF EXISTS `aixada_order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_order_item` (
  `id` int(11) NOT NULL auto_increment,
  `uf_id` int(11) NOT NULL,
  `favorite_cart_id` int(11) default NULL,
  `order_id` int(11) default NULL,
  `unit_price_stamp` decimal(10,2) default '0.00',
  `date_for_order` date NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` float(10,4) default '0.0000',
  `ts_ordered` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `order_id` (`order_id`,`uf_id`,`product_id`),
  KEY `uf_id` (`uf_id`),
  KEY `favorite_cart_id` (`favorite_cart_id`),
  KEY `product_id` (`product_id`,`date_for_order`),
  CONSTRAINT `aixada_order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `aixada_order` (`id`),
  CONSTRAINT `aixada_order_item_ibfk_2` FOREIGN KEY (`uf_id`) REFERENCES `aixada_uf` (`id`),
  CONSTRAINT `aixada_order_item_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `aixada_product` (`id`),
  CONSTRAINT `aixada_order_item_ibfk_4` FOREIGN KEY (`favorite_cart_id`) REFERENCES `aixada_cart` (`id`),
  CONSTRAINT `aixada_order_item_ibfk_5` FOREIGN KEY (`product_id`, `date_for_order`) REFERENCES `aixada_product_orderable_for_date` (`product_id`, `date_for_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_order_item`
--

LOCK TABLES `aixada_order_item` WRITE;
/*!40000 ALTER TABLE `aixada_order_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `aixada_order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_order_to_shop`
--

DROP TABLE IF EXISTS `aixada_order_to_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_order_to_shop` (
  `order_item_id` int(11) NOT NULL,
  `uf_id` int(11) NOT NULL,
  `order_id` int(11) default NULL,
  `unit_price_stamp` decimal(10,2) default '0.00',
  `product_id` int(11) NOT NULL,
  `quantity` float(10,4) default '0.0000',
  `arrived` tinyint(1) default '1',
  `revised` tinyint(1) default '0',
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `uf_id` (`uf_id`),
  CONSTRAINT `aixada_order_to_shop_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `aixada_order` (`id`),
  CONSTRAINT `aixada_order_to_shop_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `aixada_product` (`id`),
  CONSTRAINT `aixada_order_to_shop_ibfk_3` FOREIGN KEY (`uf_id`) REFERENCES `aixada_uf` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_order_to_shop`
--

LOCK TABLES `aixada_order_to_shop` WRITE;
/*!40000 ALTER TABLE `aixada_order_to_shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `aixada_order_to_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_orderable_type`
--

DROP TABLE IF EXISTS `aixada_orderable_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_orderable_type` (
  `id` tinyint(4) NOT NULL auto_increment,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_orderable_type`
--

LOCK TABLES `aixada_orderable_type` WRITE;
/*!40000 ALTER TABLE `aixada_orderable_type` DISABLE KEYS */;
INSERT INTO `aixada_orderable_type` VALUES (1,'stock'),(2,'orderable');
/*!40000 ALTER TABLE `aixada_orderable_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_payment_method`
--

DROP TABLE IF EXISTS `aixada_payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_payment_method` (
  `id` tinyint(4) NOT NULL auto_increment,
  `description` varchar(50) NOT NULL,
  `details` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_payment_method`
--

LOCK TABLES `aixada_payment_method` WRITE;
/*!40000 ALTER TABLE `aixada_payment_method` DISABLE KEYS */;
INSERT INTO `aixada_payment_method` VALUES (1,'cash','cash payment'),(5,'stock','register gain or loss of stock'),(6,'validation','register validation of cart'),(7,'deposit','register the inpayment of cash'),(8,'bill','register withdrawal for bill payment to provider'),(9,'correction','by-hand correction of account balance'),(10,'withdrawal','default cash withdrawal'),(11,'setup','account setup');
/*!40000 ALTER TABLE `aixada_payment_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_product`
--

DROP TABLE IF EXISTS `aixada_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_product` (
  `id` int(11) NOT NULL auto_increment,
  `provider_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `barcode` varchar(50) default NULL,
  `active` tinyint(4) default '1',
  `responsible_uf_id` int(11) default NULL,
  `orderable_type_id` tinyint(4) default '2',
  `category_id` int(11) default '1',
  `rev_tax_type_id` tinyint(4) default '1',
  `iva_percent_id` smallint(6) default '1',
  `unit_price` decimal(10,2) default '0.00',
  `unit_measure_order_id` tinyint(4) default '1',
  `unit_measure_shop_id` tinyint(4) default '1',
  `stock_min` decimal(10,4) default '0.0000',
  `stock_actual` decimal(10,4) default '0.0000',
  `delta_stock` decimal(10,4) default '0.0000',
  `description_url` varchar(255) default NULL,
  `picture` varchar(255) default NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `provider_id` (`provider_id`),
  KEY `active` (`active`),
  KEY `responsible_uf_id` (`responsible_uf_id`),
  KEY `orderable_type_id` (`orderable_type_id`),
  KEY `category_id` (`category_id`),
  KEY `rev_tax_type_id` (`rev_tax_type_id`),
  KEY `iva_percent_id` (`iva_percent_id`),
  KEY `unit_measure_order_id` (`unit_measure_order_id`),
  KEY `unit_measure_shop_id` (`unit_measure_shop_id`),
  KEY `delta_stock` (`delta_stock`),
  CONSTRAINT `aixada_product_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `aixada_provider` (`id`) ON DELETE CASCADE,
  CONSTRAINT `aixada_product_ibfk_2` FOREIGN KEY (`responsible_uf_id`) REFERENCES `aixada_uf` (`id`),
  CONSTRAINT `aixada_product_ibfk_3` FOREIGN KEY (`orderable_type_id`) REFERENCES `aixada_orderable_type` (`id`),
  CONSTRAINT `aixada_product_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `aixada_product_category` (`id`),
  CONSTRAINT `aixada_product_ibfk_5` FOREIGN KEY (`rev_tax_type_id`) REFERENCES `aixada_rev_tax_type` (`id`),
  CONSTRAINT `aixada_product_ibfk_6` FOREIGN KEY (`iva_percent_id`) REFERENCES `aixada_iva_type` (`id`),
  CONSTRAINT `aixada_product_ibfk_7` FOREIGN KEY (`unit_measure_order_id`) REFERENCES `aixada_unit_measure` (`id`),
  CONSTRAINT `aixada_product_ibfk_8` FOREIGN KEY (`unit_measure_shop_id`) REFERENCES `aixada_unit_measure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_product`
--

LOCK TABLES `aixada_product` WRITE;
/*!40000 ALTER TABLE `aixada_product` DISABLE KEYS */;
INSERT INTO `aixada_product` VALUES (1,1,'Leche','','',1,2,2,4000,1,2,'1.90',13,13,'0.0000','0.0000','0.0000','','','2012-10-19 09:23:08'),(2,1,'Leche Semi','Semi desnatada','',1,2,2,4000,1,2,'2.00',13,13,'0.0000','0.0000','0.0000','','','2012-10-19 09:24:59'),(3,1,'Pollo ','','',1,2,2,5000,1,1,'7.80',2,4,'0.0000','0.0000','0.0000','','','2012-10-19 09:30:39'),(4,2,'Arroz','Arroz blanco al granel','',1,2,1,10000,1,1,'1.24',4,4,'10.0000','20.0000','20.0000','','','2012-10-19 09:32:03'),(5,2,'Cous Cous','Cous Cous Integral','',1,2,1,10000,1,2,'1.20',4,4,'10.0000','31.0000','31.0000','','','2012-10-19 09:33:01'),(6,3,'Ensalada','diferentes variedades','',1,2,2,1000,1,2,'1.10',2,2,'0.0000','0.0000','0.0000','','','2012-10-19 09:34:48'),(7,3,'Melon','','',0,2,2,1000,1,2,'1.99',2,4,'0.0000','0.0000','0.0000','','','2012-10-19 09:35:36'),(8,3,'Patatas','','',1,2,2,1000,1,1,'0.90',4,4,'0.0000','0.0000','0.0000','','','2012-10-19 09:36:28'),(9,3,'Zanahorias','','',1,2,2,1000,1,1,'1.30',12,12,'0.0000','0.0000','0.0000','','','2012-10-19 09:37:05');
/*!40000 ALTER TABLE `aixada_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_product_category`
--

DROP TABLE IF EXISTS `aixada_product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_product_category` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_product_category`
--

LOCK TABLES `aixada_product_category` WRITE;
/*!40000 ALTER TABLE `aixada_product_category` DISABLE KEYS */;
INSERT INTO `aixada_product_category` VALUES (1,'SET_ME'),(1000,'prdcat_vegies'),(2000,'prdcat_fruit'),(3000,'prdcat_mushrooms'),(4000,'prdcat_dairy'),(5000,'prdcat_meat'),(6000,'prdcat_bakery'),(7000,'prdcat_cheese'),(8000,'prdcat_sausages'),(9000,'prdcat_infant'),(10000,'prdcat_cereals_pasta'),(11000,'prdcat_canned'),(12000,'prdcat_cleaning'),(13000,'prdcat_body'),(14000,'prdcat_seasoning'),(15000,'prdcat_sweets'),(16000,'prdcat_drinks_alcohol'),(17000,'prdcat_drinks_soft'),(18000,'prdcat_drinks_hot'),(19000,'prdcat_driedstuff'),(20000,'prdcat_paper'),(21000,'prdcat_health'),(22000,'prdcat_misc');
/*!40000 ALTER TABLE `aixada_product_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_product_orderable_for_date`
--

DROP TABLE IF EXISTS `aixada_product_orderable_for_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_product_orderable_for_date` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `date_for_order` date NOT NULL,
  `closing_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_id` (`product_id`,`date_for_order`),
  KEY `date_for_order` (`date_for_order`),
  CONSTRAINT `aixada_product_orderable_for_date_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `aixada_product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_product_orderable_for_date`
--

LOCK TABLES `aixada_product_orderable_for_date` WRITE;
/*!40000 ALTER TABLE `aixada_product_orderable_for_date` DISABLE KEYS */;
/*!40000 ALTER TABLE `aixada_product_orderable_for_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_provider`
--

DROP TABLE IF EXISTS `aixada_provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_provider` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `nif` varchar(15) default NULL,
  `zip` varchar(10) default NULL,
  `city` varchar(255) default NULL,
  `phone1` varchar(50) default NULL,
  `phone2` varchar(50) default NULL,
  `fax` varchar(100) default NULL,
  `email` varchar(100) default NULL,
  `web` varchar(255) default NULL,
  `bank_name` varchar(255) default NULL,
  `bank_account` varchar(40) default NULL,
  `picture` varchar(255) default NULL,
  `notes` text,
  `active` tinyint(4) default '1',
  `responsible_uf_id` int(11) default NULL,
  `offset_order_close` int(11) default '4',
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `active` (`active`),
  KEY `responsible_uf_id` (`responsible_uf_id`),
  CONSTRAINT `aixada_provider_ibfk_1` FOREIGN KEY (`responsible_uf_id`) REFERENCES `aixada_uf` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_provider`
--

LOCK TABLES `aixada_provider` WRITE;
/*!40000 ALTER TABLE `aixada_provider` DISABLE KEYS */;
INSERT INTO `aixada_provider` VALUES (1,'LaFresca','Miguel Hernández','El Campo 5','','09123','El Pueblo','','','','','','','','','',1,2,3,'2012-10-19 09:20:52'),(2,'LaPasta','Jordi Sanchez','El campo ','','934234','El otro pueblo','','','','','','','','','',1,2,2,'2012-10-19 09:21:47'),(3,'El Huerto','Fernando Smith','','','','','','','','','','','','','',1,2,3,'2012-10-19 09:23:44');
/*!40000 ALTER TABLE `aixada_provider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_rev_tax_type`
--

DROP TABLE IF EXISTS `aixada_rev_tax_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_rev_tax_type` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(50) NOT NULL,
  `rev_tax_percent` decimal(10,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_rev_tax_type`
--

LOCK TABLES `aixada_rev_tax_type` WRITE;
/*!40000 ALTER TABLE `aixada_rev_tax_type` DISABLE KEYS */;
INSERT INTO `aixada_rev_tax_type` VALUES (1,'default revolutionary tax','what everybody pays','3.00'),(2,'no revolutionary tax','zero tax','0.00'),(3,'luxury','for capitalists','5.00');
/*!40000 ALTER TABLE `aixada_rev_tax_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_shop_item`
--

DROP TABLE IF EXISTS `aixada_shop_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_shop_item` (
  `id` int(11) NOT NULL auto_increment,
  `cart_id` int(11) NOT NULL,
  `order_item_id` int(11) default NULL,
  `unit_price_stamp` decimal(10,2) default '0.00',
  `product_id` int(11) NOT NULL,
  `quantity` float(10,4) default '0.0000',
  `iva_percent` decimal(10,2) default '0.00',
  `rev_tax_percent` decimal(10,2) default '0.00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cart_id` (`cart_id`,`product_id`,`order_item_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `aixada_shop_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `aixada_cart` (`id`),
  CONSTRAINT `aixada_shop_item_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `aixada_order_item` (`id`),
  CONSTRAINT `aixada_shop_item_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `aixada_product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_shop_item`
--

LOCK TABLES `aixada_shop_item` WRITE;
/*!40000 ALTER TABLE `aixada_shop_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `aixada_shop_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_stock_movement`
--

DROP TABLE IF EXISTS `aixada_stock_movement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_stock_movement` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `amount_difference` decimal(10,4) default NULL,
  `description` varchar(255) default NULL,
  `resulting_amount` decimal(10,4) default NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `product_id` (`product_id`),
  KEY `operator_id` (`operator_id`),
  KEY `ts` (`ts`),
  CONSTRAINT `aixada_stock_movement_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `aixada_product` (`id`),
  CONSTRAINT `aixada_stock_movement_ibfk_2` FOREIGN KEY (`operator_id`) REFERENCES `aixada_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_stock_movement`
--

LOCK TABLES `aixada_stock_movement` WRITE;
/*!40000 ALTER TABLE `aixada_stock_movement` DISABLE KEYS */;
INSERT INTO `aixada_stock_movement` VALUES (1,4,2,'20.0000','stock added','20.0000','2012-10-19 09:38:02'),(2,5,2,'31.0000','stock added','31.0000','2012-10-19 09:38:11');
/*!40000 ALTER TABLE `aixada_stock_movement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_uf`
--

DROP TABLE IF EXISTS `aixada_uf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_uf` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` tinyint(4) default '1',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `mentor_uf` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_uf`
--

LOCK TABLES `aixada_uf` WRITE;
/*!40000 ALTER TABLE `aixada_uf` DISABLE KEYS */;
INSERT INTO `aixada_uf` VALUES (1,'Admin',1,'2012-10-18 16:07:54',NULL),(2,'The Testers',1,'2012-10-18 17:02:29',-1),(3,'Modern Family',1,'2012-10-19 09:39:26',-1);
/*!40000 ALTER TABLE `aixada_uf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_unit_measure`
--

DROP TABLE IF EXISTS `aixada_unit_measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_unit_measure` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_unit_measure`
--

LOCK TABLES `aixada_unit_measure` WRITE;
/*!40000 ALTER TABLE `aixada_unit_measure` DISABLE KEYS */;
INSERT INTO `aixada_unit_measure` VALUES (1,'unit is not set','SET_ME'),(2,'unit','u'),(3,'grams','g'),(4,'kilograms','kg'),(5,'unit of 250g','250g'),(6,'unit of half kilo','500g'),(7,'mililiters','ml'),(8,'liter','l'),(9,'quarter of a liter','250ml'),(10,'half a liter','500ml'),(11,'three quarters of a liter','750ml'),(12,'bunch','bunch'),(13,'','1 l');
/*!40000 ALTER TABLE `aixada_unit_measure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_user`
--

DROP TABLE IF EXISTS `aixada_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_user` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `uf_id` int(11) default NULL,
  `member_id` int(11) default NULL,
  `provider_id` int(11) default NULL,
  `language` char(5) default 'en',
  `gui_theme` varchar(50) default NULL,
  `last_login_attempt` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `last_successful_login` timestamp NOT NULL default '0000-00-00 00:00:00',
  `created_on` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `uf_id` (`uf_id`),
  KEY `member_id` (`member_id`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `aixada_user_ibfk_1` FOREIGN KEY (`uf_id`) REFERENCES `aixada_uf` (`id`),
  CONSTRAINT `aixada_user_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `aixada_member` (`id`),
  CONSTRAINT `aixada_user_ibfk_3` FOREIGN KEY (`provider_id`) REFERENCES `aixada_provider` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_user`
--

LOCK TABLES `aixada_user` WRITE;
/*!40000 ALTER TABLE `aixada_user` DISABLE KEYS */;
INSERT INTO `aixada_user` VALUES (1,'admin','axoX7oy6Vqtk.','',1,1,NULL,'en',NULL,'2012-10-18 17:07:41','2012-10-18 17:07:41','2012-10-18 16:07:54'),(2,'joerg','axWegIWZyUBjU','joerg@toytic.com',1,2,NULL,'en','smoothness','2012-10-19 09:41:10','2012-10-19 09:41:10','2012-10-18 17:03:10'),(3,'julian','axtkjFCF3g5NY','julian.pfeifle@upc.edu',1,3,NULL,'en','smoothness','2012-10-18 17:22:21','2012-10-18 17:22:21','2012-10-18 17:03:54'),(4,'test-en','axtkjFCF3g5NY','j@f.com',2,4,NULL,'en','start','2012-10-18 17:09:09','2012-10-18 17:09:09','2012-10-18 17:05:48'),(5,'test-es','axtkjFCF3g5NY','j@f.com',2,5,NULL,'es','start','2012-10-18 17:10:34','2012-10-18 17:10:34','2012-10-18 17:06:22'),(6,'test-cat','axtkjFCF3g5NY','p@p.com',3,6,NULL,'ca-va','ui-lightness','2012-10-19 09:41:00','2012-10-19 09:41:00','2012-10-19 09:40:38');
/*!40000 ALTER TABLE `aixada_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aixada_user_role`
--

DROP TABLE IF EXISTS `aixada_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aixada_user_role` (
  `user_id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL,
  PRIMARY KEY  (`user_id`,`role`),
  CONSTRAINT `aixada_user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `aixada_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aixada_user_role`
--

LOCK TABLES `aixada_user_role` WRITE;
/*!40000 ALTER TABLE `aixada_user_role` DISABLE KEYS */;
INSERT INTO `aixada_user_role` VALUES (1,'Hacker Commission'),(2,'Checkout'),(2,'Consumer'),(2,'Hacker Commission'),(3,'Checkout'),(3,'Consumer'),(3,'Hacker Commission'),(4,'Checkout'),(4,'Consumer'),(5,'Checkout'),(5,'Consumer'),(6,'Checkout'),(6,'Consumer');
/*!40000 ALTER TABLE `aixada_user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-10-19 12:02:19
