-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 20, 2015 at 08:02 
-- Server version: 5.5.36
-- PHP Version: 5.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `calendar_tool`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_event`
--

CREATE TABLE IF NOT EXISTS `academic_event` (
  `Id_Event` int(11) NOT NULL,
  `Feedback` text NOT NULL,
  `Workload` int(11) DEFAULT NULL,
  `Practical_Details` text,
  PRIMARY KEY (`Id_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `academic_event`
--

INSERT INTO `academic_event` (`Id_Event`, `Feedback`, `Workload`, `Practical_Details`) VALUES
(1, '', 2, 'Bring your laptop'),
(2, '', 1, 'Bring your courage'),
(4, '', 35, 'It will be fun'),
(6, '', 30, 'C&#039;est pratique ce champ'),
(11, '', 5555, '#BringUrLaptop'),
(14, 'Bientot', 30, ''),
(15, '', 30, '#BringUrLaptop RFC 25421'),
(40, '', 30, ''),
(41, '', 30, ''),
(42, '', 30, ''),
(43, '', 30, '');

-- --------------------------------------------------------

--
-- Table structure for table `academic_event_category`
--

CREATE TABLE IF NOT EXISTS `academic_event_category` (
  `Id_Category` int(11) NOT NULL,
  PRIMARY KEY (`Id_Category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `academic_event_category`
--

INSERT INTO `academic_event_category` (`Id_Category`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(19),
(20),
(21);

-- --------------------------------------------------------

--
-- Table structure for table `academic_event_file`
--

CREATE TABLE IF NOT EXISTS `academic_event_file` (
  `Id_File` int(11) NOT NULL,
  `Id_Event` int(11) NOT NULL,
  PRIMARY KEY (`Id_File`,`Id_Event`),
  KEY `Id_Event` (`Id_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `Id_Activity` int(11) NOT NULL AUTO_INCREMENT,
  `Action` text NOT NULL,
  `Id_User` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id_Activity`),
  KEY `Id_User` (`Id_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `date_range_event`
--

CREATE TABLE IF NOT EXISTS `date_range_event` (
  `Id_Event` int(11) NOT NULL,
  `Start` date NOT NULL,
  `End` date NOT NULL,
  PRIMARY KEY (`Id_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `date_range_event`
--

INSERT INTO `date_range_event` (`Id_Event`, `Start`, `End`) VALUES
(3, '2015-05-01', '2015-05-06'),
(19, '2015-03-01', '2015-03-01'),
(20, '2015-03-02', '2015-03-08');

-- --------------------------------------------------------

--
-- Table structure for table `deadline_event`
--

CREATE TABLE IF NOT EXISTS `deadline_event` (
  `Id_Event` int(11) NOT NULL,
  `Limit` datetime NOT NULL,
  PRIMARY KEY (`Id_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deadline_event`
--

INSERT INTO `deadline_event` (`Id_Event`, `Limit`) VALUES
(4, '2015-04-26 23:59:00'),
(14, '2015-03-12 23:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `dynamic_export`
--

CREATE TABLE IF NOT EXISTS `dynamic_export` (
  `Id_Export` int(11) NOT NULL,
  PRIMARY KEY (`Id_Export`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `Id_Event` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Id_Recurrence` int(11) NOT NULL,
  `Place` varchar(255) DEFAULT NULL,
  `Id_Category` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`),
  KEY `Id_Recurrence` (`Id_Recurrence`),
  KEY `Id_Category` (`Id_Category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`Id_Event`, `Name`, `Description`, `Id_Recurrence`, `Place`, `Id_Category`) VALUES
(1, 'Cours introductif', 'Premier cours', 1, 'R18/B28', 1),
(2, 'Footcheball', 'Match de foot au Brésil', 1, 'Sao Polo', 8),
(3, 'Conférence sur le theme de l''école', 'Jean Phillibert nous racontera son parcours scolaire', 1, '604/B4', 4),
(4, 'Premier projet', 'Projet de programmation de folie', 1, NULL, 5),
(6, 'Premier Cours', 'Bring your laptop', 1, 'Amphi de Meandre', 1),
(11, 'Cours pour 2e uniquement', 'On prend de l&#039;avance', 1, 'Amphi de Meandre', 3),
(14, 'Préparation au cours préparatoire d''introduction', 'Il faut travailler', 1, 'mailto:montirroir@ulg.ac.be', 6),
(15, 'This Is SCHEME', 'Lisp is over', 1, 'R7', 1),
(16, 'Je me la coule douce', 'Boire de la biere', 1, 'Partout', 3),
(19, 'Voyage en Terre du Milieu', 'Un voyage inattendu....', 1, 'Terre du Milieu, Arda', 3),
(20, 'Grosse guindaille', '', 1, 'ChapiChapo', 3),
(40, 'Repet Scheme', 'This lesson will be ok', 5, 'R3', 3),
(41, 'Repet Scheme', 'This lesson will be ok', 5, 'R3', 3),
(42, 'Repet Scheme', 'This lesson will be ok', 5, 'R3', 3),
(43, 'Repet Scheme', 'This lesson will be ok', 5, 'R3', 3);

-- --------------------------------------------------------

--
-- Table structure for table `event_annotation`
--

CREATE TABLE IF NOT EXISTS `event_annotation` (
  `Id_Student` int(11) NOT NULL,
  `Id_Event` int(11) NOT NULL,
  `Annotation` text NOT NULL,
  PRIMARY KEY (`Id_Event`,`Id_Student`),
  KEY `Id_Student` (`Id_Student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event_annotation`
--

INSERT INTO `event_annotation` (`Id_Student`, `Id_Event`, `Annotation`) VALUES
(16, 20, 'Waar is dat feestje ?');

-- --------------------------------------------------------

--
-- Table structure for table `event_category`
--

CREATE TABLE IF NOT EXISTS `event_category` (
  `Id_Category` int(11) NOT NULL AUTO_INCREMENT,
  `Name_FR` varchar(255) NOT NULL,
  `Name_EN` varchar(255) NOT NULL,
  `Description_FR` text NOT NULL,
  `Description_EN` text NOT NULL,
  `Color` varchar(7) NOT NULL,
  PRIMARY KEY (`Id_Category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `event_category`
--

INSERT INTO `event_category` (`Id_Category`, `Name_FR`, `Name_EN`, `Description_FR`, `Description_EN`, `Color`) VALUES
(1, 'Cours théorique', 'Lecture', 'Cours théorique', 'Lecture', ''),
(2, 'Laboratoire', 'Lab', 'Laboratoire', 'Lab', ''),
(3, 'TP', 'Exercise session', 'Travaux pratiques (exercices)', 'Exercise session', ''),
(4, 'Conférence', 'Conference', 'Conférence', 'Conference', ''),
(5, 'Projet', 'Project', 'Deadline, cours, événement lié à un projet', 'Deadline, course, event linked to a project', ''),
(6, 'Devoir', 'Homework', 'Devoir', 'Homework', ''),
(7, 'Q & R', 'Q & A', 'Session de questions/réponses ', 'Questions/answers session', ''),
(8, 'Autre', 'Other', 'Catégorie pour les événements ne pouvant être classés dans aucune autre catégorie', 'Category for the events that cannot be associated with any other category', ''),
(9, 'Sport', 'Sport', 'Activité sportive', 'Sport activity', ''),
(10, 'Chapiteau', 'Chapiteau', 'Célèbre chapiteau du Val-Benoît', 'Famous Val-Benoit chapiteau', ''),
(11, 'Travail', 'Work', 'Événement lié au travail (job d''étudiant,  travail personnel,...)', 'Event linked to work (student job, personnal work,...)', ''),
(12, 'Restaurant', 'Restaurant', 'Bon appétit!', 'Good appetite! ', ''),
(13, 'Soirée', 'Party', 'Sortie, soirée,...', 'Night-out, party,...', ''),
(14, 'Personnel', 'Personnal', 'Activité personnelle', 'Personnal activity', ''),
(15, 'Loisirs', 'Leisure', 'Loisirs (modélisme, bricolage,...)', 'Leisure (model-making, DIY,...)', ''),
(16, 'Musique', 'Music', 'Musique (concert, répétition,...)', 'Music (concert, rehearsal,...)', ''),
(17, 'Anniversaire', 'Birthday', 'Anniversaire', 'Birthday', ''),
(18, 'Autre', 'Other', 'Catégorie pour les événements ne pouvant être classés dans aucune autre catégorie', 'Category for the events that cannot be associated with any other category', ''),
(19, 'Examen oral', 'Oral exam', 'Examen oral', 'Oral exam', ''),
(20, 'Examen écrit', 'Written exam', 'Examen écrit', 'Written exam', ''),
(21, 'Interrogation', 'Written test', 'Interrogation écrite', 'Written test', '');

-- --------------------------------------------------------

--
-- Table structure for table `event_export`
--

CREATE TABLE IF NOT EXISTS `event_export` (
  `Id_Export` int(11) NOT NULL AUTO_INCREMENT,
  `Id_User` int(11) NOT NULL,
  `User_Hash` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Export`),
  UNIQUE KEY `User_Hash` (`User_Hash`),
  KEY `Id_User` (`Id_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `export_filter`
--

CREATE TABLE IF NOT EXISTS `export_filter` (
  `Id_Filter` int(11) NOT NULL,
  `Id_Export` int(11) NOT NULL,
  `Value` text NOT NULL,
  PRIMARY KEY (`Id_Filter`,`Id_Export`),
  KEY `Id_Export` (`Id_Export`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_staff_member`
--

CREATE TABLE IF NOT EXISTS `faculty_staff_member` (
  `Id_Faculty_Member` int(11) NOT NULL,
  PRIMARY KEY (`Id_Faculty_Member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `faculty_staff_member`
--

INSERT INTO `faculty_staff_member` (`Id_Faculty_Member`) VALUES
(2),
(5),
(7),
(11),
(12),
(13);

-- --------------------------------------------------------

--
-- Table structure for table `favorite_event`
--

CREATE TABLE IF NOT EXISTS `favorite_event` (
  `Id_Event` int(11) NOT NULL,
  `Id_Student` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`,`Id_Student`),
  KEY `Id_Student` (`Id_Student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `Id_File` int(11) NOT NULL AUTO_INCREMENT,
  `Filepath` varchar(255) NOT NULL,
  `Id_User` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id_File`),
  KEY `Id_User` (`Id_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `filter`
--

CREATE TABLE IF NOT EXISTS `filter` (
  `Id_Filter` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Filter`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `filter`
--

INSERT INTO `filter` (`Id_Filter`, `Name`) VALUES
(1, 'datetime'),
(2, 'access'),
(3, 'event_category'),
(4, 'event_type'),
(5, 'global_event'),
(6, 'pathway'),
(7, 'professor'),
(8, 'time_type');

-- --------------------------------------------------------

--
-- Table structure for table `global_event`
--

CREATE TABLE IF NOT EXISTS `global_event` (
  `Id_Global_Event` int(11) NOT NULL AUTO_INCREMENT,
  `ULg_Identifier` varchar(20) NOT NULL,
  `Name_Short` varchar(255) NOT NULL,
  `Name_Long` varchar(255) NOT NULL,
  `Id_Owner` int(11) NOT NULL,
  `Period` enum('Q1','Q2','TA') NOT NULL,
  `Description` text NOT NULL,
  `Feedback` text NOT NULL,
  `Workload_Th` int(11) NOT NULL,
  `Workload_Pr` int(11) NOT NULL,
  `Workload_Au` int(11) NOT NULL,
  `Workload_St` int(11) NOT NULL,
  `Language` enum('EN','FR') NOT NULL,
  `Acad_Start_Year` year(4) NOT NULL,
  PRIMARY KEY (`Id_Global_Event`),
  UNIQUE KEY `course_year` (`ULg_Identifier`,`Acad_Start_Year`),
  KEY `Id_Owner` (`Id_Owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `global_event`
--

INSERT INTO `global_event` (`Id_Global_Event`, `ULg_Identifier`, `Name_Short`, `Name_Long`, `Id_Owner`, `Period`, `Description`, `Feedback`, `Workload_Th`, `Workload_Pr`, `Workload_Au`, `Workload_St`, `Language`, `Acad_Start_Year`) VALUES
(1, 'MATH0009-4', 'Mathématiques générales, Partim A, 30h Th, 30h Pr, BASTIN Françoise', 'Mathématiques générales, Partim A', 2, 'Q1', '', '', 30, 30, 0, 0, 'EN', 2014),
(2, 'INFO2047-1', 'Introduction à la programmation', 'Introduction à la programmation, 12h Th, 12h Pr, DONNET Benoît, FONTENEAU Raphaël', 7, 'Q1', '', '', 12, 12, 0, 0, 'FR', 2014),
(3, 'DROI0724-1', 'Droit et activités de l''ingénieur', 'Droit et activités de l''ingénieur, 30h Th, BIQUET Christine, CLESSE Jacques, LECOCQ Pascale, VANBRABANT Bernard, Suppl: CHICHOYAN Daisy, GOL Déborah, VERCHEVAL Cécile', 11, 'Q1', 'Un cours super amusant et présentiel', '', 30, 0, 0, 0, 'FR', 2014),
(4, 'MATH0495-1', 'Eléments du calcul des probabilités ', 'Eléments du calcul des probabilités , 15h Th, 15h Pr, 5h Proj., GRIBOMONT Pascal', 13, 'Q1', '50% de réussite', '', 15, 15, 5, 0, 'FR', 2014),
(5, 'INFO0054-1', 'Programmation fonctionnelle', 'Programmation fonctionnelle, 30h Th, 25h Pr, 15h Proj., GRIBOMONT Pascal', 13, 'Q2', 'Now in english', '', 30, 25, 15, 0, 'EN', 2014);

-- --------------------------------------------------------

--
-- Table structure for table `global_event_file`
--

CREATE TABLE IF NOT EXISTS `global_event_file` (
  `Id_File` int(11) NOT NULL,
  `Id_Global_Event` int(11) NOT NULL,
  PRIMARY KEY (`Id_File`,`Id_Global_Event`),
  KEY `Id_Global_Event` (`Id_Global_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `global_event_pathway`
--

CREATE TABLE IF NOT EXISTS `global_event_pathway` (
  `Id_Global_Event` int(11) NOT NULL,
  `Id_Pathway` varchar(20) NOT NULL,
  PRIMARY KEY (`Id_Global_Event`,`Id_Pathway`),
  KEY `Id_Pathway` (`Id_Pathway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `global_event_pathway`
--

INSERT INTO `global_event_pathway` (`Id_Global_Event`, `Id_Pathway`) VALUES
(2, 'ABICIV000201'),
(3, 'ABICIV000201'),
(3, 'ABICIV000301'),
(5, 'ABICIV000301'),
(4, 'ABINFO000201'),
(5, 'ABINFO000301'),
(5, 'ABINFO000401'),
(1, 'ABINFO009901'),
(5, 'AEMINF000101');

-- --------------------------------------------------------

--
-- Table structure for table `global_event_subscription`
--

CREATE TABLE IF NOT EXISTS `global_event_subscription` (
  `Id_Global_Event` int(11) NOT NULL,
  `Id_Student` int(11) NOT NULL,
  `Free_Student` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id_Global_Event`,`Id_Student`),
  KEY `Id_Student` (`Id_Student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `global_event_subscription`
--

INSERT INTO `global_event_subscription` (`Id_Global_Event`, `Id_Student`, `Free_Student`) VALUES
(1, 6, 0),
(2, 1, 0),
(2, 4, 0),
(2, 8, 0),
(3, 1, 0),
(3, 3, 0),
(3, 4, 0),
(3, 9, 0),
(3, 14, 0),
(3, 16, 0),
(5, 1, 0),
(5, 9, 0),
(5, 14, 0);

-- --------------------------------------------------------

--
-- Table structure for table `independent_event`
--

CREATE TABLE IF NOT EXISTS `independent_event` (
  `Id_Event` int(11) NOT NULL,
  `Id_Owner` int(11) NOT NULL,
  `Public` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id_Event`),
  KEY `Id_Owner` (`Id_Owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `independent_event`
--

INSERT INTO `independent_event` (`Id_Event`, `Id_Owner`, `Public`) VALUES
(2, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `independent_event_manager`
--

CREATE TABLE IF NOT EXISTS `independent_event_manager` (
  `Id_Event` int(11) NOT NULL,
  `Id_User` int(11) NOT NULL,
  `Id_Role` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`,`Id_User`),
  KEY `Id_User` (`Id_User`),
  KEY `Id_Role` (`Id_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `independent_event_manager`
--

INSERT INTO `independent_event_manager` (`Id_Event`, `Id_User`, `Id_Role`) VALUES
(2, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `independent_event_pathway`
--

CREATE TABLE IF NOT EXISTS `independent_event_pathway` (
  `Id_Event` int(11) NOT NULL,
  `Id_Pathway` varchar(20) NOT NULL,
  PRIMARY KEY (`Id_Event`,`Id_Pathway`),
  KEY `Id_Pathway` (`Id_Pathway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_event_update`
--

CREATE TABLE IF NOT EXISTS `mobile_event_update` (
  `Id_Event` int(11) NOT NULL,
  `Id_User` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`,`Id_User`),
  KEY `Id_User` (`Id_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `modification`
--

CREATE TABLE IF NOT EXISTS `modification` (
  `Id_Request` int(11) NOT NULL,
  `Id_Target` int(11) NOT NULL,
  `Proposition` text NOT NULL,
  PRIMARY KEY (`Id_Request`,`Id_Target`),
  KEY `Id_Target` (`Id_Target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `modification_request`
--

CREATE TABLE IF NOT EXISTS `modification_request` (
  `Id_Request` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Event` int(11) NOT NULL,
  `Id_Sender` int(11) NOT NULL,
  `Status` enum('waiting','accepted','cancelled','refused') NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`Id_Request`),
  KEY `Id_Event` (`Id_Event`),
  KEY `Id_Sender` (`Id_Sender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `modification_target`
--

CREATE TABLE IF NOT EXISTS `modification_target` (
  `Id_Target` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Target`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `modification_target`
--

INSERT INTO `modification_target` (`Id_Target`, `Name`, `Type`) VALUES
(1, 'place', 'varchar(255)'),
(2, 'to_date_range', 'ereg(''^start:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}),end:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})$'')'),
(3, 'to_deadline', 'ereg(''deadline:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2})'')'),
(4, 'change_date', 'ereg(''(start|end):([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})'')'),
(5, 'change_time', 'ereg(''(start|end|deadline):([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2})'')');

-- --------------------------------------------------------

--
-- Table structure for table `pathway`
--

CREATE TABLE IF NOT EXISTS `pathway` (
  `Id_Pathway` varchar(20) NOT NULL,
  `Name_Long` varchar(255) NOT NULL,
  `Name_Short` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Pathway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pathway`
--

INSERT INTO `pathway` (`Id_Pathway`, `Name_Long`, `Name_Short`) VALUES
('ABICAR000201', '2e année du grade de bachelier en sciences de l''ingénieur, orientation ingénieur civil architecte', '2e an. bac. sc. ing., or. ing. civ. architecte'),
('ABICIV000201', '2e année du grade de bachelier en sciences de l''ingénieur, orientation ingénieur civil', '2e an. bac. sc. ing., or. ing. civil'),
('ABICIV000301', '3e année du grade de bachelier en sciences de l''ingénieur, orientation ingénieur civil', '3e an. bac. sc. ing., or. ing. civil'),
('ABICIV009901', 'Bachelier en sciences de l''ingénieur, orientation ingénieur civil', 'Bac. sc. ingé., or. ingé. civ. '),
('ABINFO000201', '2e année du grade de bachelier en sciences informatiques', '2e an. bac. sc. informatiques'),
('ABINFO000301', '3e année du grade de bachelier en sciences informatiques', '3e an. bac. sc. informatiques'),
('ABINFO000401', 'Solde de crédits du bachelier en sciences informatiques', 'Solde bac. sc. informatiques'),
('ABINFO009901', 'Bachelier en sciences informatiques', 'Bac. sc. info.'),
('AEMINF000101', 'Année préparatoire au master en sciences informatiques', 'An. prépa. master sc. info.');

-- --------------------------------------------------------

--
-- Table structure for table `recurrence`
--

CREATE TABLE IF NOT EXISTS `recurrence` (
  `Id_Recurrence` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Recur_Category` int(11) NOT NULL,
  PRIMARY KEY (`Id_Recurrence`),
  KEY `Id_Recur_Category` (`Id_Recur_Category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `recurrence`
--

INSERT INTO `recurrence` (`Id_Recurrence`, `Id_Recur_Category`) VALUES
(2, 1),
(3, 1),
(4, 1),
(6, 1),
(5, 2),
(1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `recurrence_category`
--

CREATE TABLE IF NOT EXISTS `recurrence_category` (
  `Id_Recur_Category` int(11) NOT NULL AUTO_INCREMENT,
  `Recur_Category_EN` varchar(255) NOT NULL,
  `Recur_Category_FR` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Recur_Category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `recurrence_category`
--

INSERT INTO `recurrence_category` (`Id_Recur_Category`, `Recur_Category_EN`, `Recur_Category_FR`) VALUES
(1, 'Daily', 'Journalier'),
(2, 'Weekly', 'Hebdomadaire'),
(3, 'Bi-monthly', 'Bimensuel'),
(4, 'Monthly', 'Mensuel'),
(5, 'Yearly', 'Annuel'),
(6, 'Never', 'Jamais');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_access`
--

CREATE TABLE IF NOT EXISTS `schedule_access` (
  `Id_Faculty_Member` int(11) NOT NULL,
  `Id_Student` int(11) NOT NULL,
  `Status` enum('sent','refused','accepted') NOT NULL,
  PRIMARY KEY (`Id_Faculty_Member`,`Id_Student`),
  KEY `Id_Student` (`Id_Student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `Id_Student` int(11) NOT NULL,
  `Mobile_User` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id_Student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Id_Student`, `Mobile_User`) VALUES
(1, 0),
(3, 0),
(4, 0),
(6, 0),
(8, 0),
(9, 0),
(14, 0),
(15, 0),
(16, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_event`
--

CREATE TABLE IF NOT EXISTS `student_event` (
  `Id_Event` int(11) NOT NULL,
  `Id_Owner` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`),
  KEY `Id_Owner` (`Id_Owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student_event`
--

INSERT INTO `student_event` (`Id_Event`, `Id_Owner`) VALUES
(3, 9),
(19, 14),
(20, 16);

-- --------------------------------------------------------

--
-- Table structure for table `student_event_category`
--

CREATE TABLE IF NOT EXISTS `student_event_category` (
  `Id_Category` int(11) NOT NULL,
  `Id_Student` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id_Category`),
  KEY `Id_Student` (`Id_Student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student_event_category`
--

INSERT INTO `student_event_category` (`Id_Category`, `Id_Student`) VALUES
(9, NULL),
(10, NULL),
(11, NULL),
(12, NULL),
(13, NULL),
(14, NULL),
(15, NULL),
(16, NULL),
(17, NULL),
(18, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_pathway`
--

CREATE TABLE IF NOT EXISTS `student_pathway` (
  `Id_Pathway` varchar(20) NOT NULL,
  `Id_Student` int(11) NOT NULL,
  `Acad_Start_Year` year(4) NOT NULL,
  PRIMARY KEY (`Id_Student`,`Id_Pathway`,`Acad_Start_Year`),
  KEY `Id_Pathway` (`Id_Pathway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student_pathway`
--

INSERT INTO `student_pathway` (`Id_Pathway`, `Id_Student`, `Acad_Start_Year`) VALUES
('ABICIV000201', 1, 2014),
('ABICIV000201', 4, 2014),
('ABICIV000201', 8, 2014),
('ABICIV000201', 16, 2014),
('ABICIV000301', 14, 2014),
('ABICIV009901', 9, 2014),
('ABINFO000201', 3, 2014),
('ABINFO000301', 15, 2014),
('ABINFO009901', 6, 2014);

-- --------------------------------------------------------

--
-- Table structure for table `sub_event`
--

CREATE TABLE IF NOT EXISTS `sub_event` (
  `Id_Event` int(11) NOT NULL,
  `Id_Global_Event` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`),
  KEY `Id_Global_Event` (`Id_Global_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sub_event`
--

INSERT INTO `sub_event` (`Id_Event`, `Id_Global_Event`) VALUES
(1, 2),
(4, 2),
(6, 3),
(11, 3),
(14, 3),
(15, 5),
(40, 5),
(41, 5),
(42, 5),
(43, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sub_event_excluded_pathway`
--

CREATE TABLE IF NOT EXISTS `sub_event_excluded_pathway` (
  `Id_Event` int(11) NOT NULL,
  `Id_Pathway` varchar(20) NOT NULL,
  `Id_Global_Event` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`,`Id_Pathway`,`Id_Global_Event`),
  KEY `Id_Global_Event` (`Id_Global_Event`,`Id_Pathway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sub_event_excluded_pathway`
--

INSERT INTO `sub_event_excluded_pathway` (`Id_Event`, `Id_Pathway`, `Id_Global_Event`) VALUES
(11, 'ABICIV000301', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sub_event_excluded_team_member`
--

CREATE TABLE IF NOT EXISTS `sub_event_excluded_team_member` (
  `Id_Event` int(11) NOT NULL,
  `Id_User` int(11) NOT NULL,
  `Id_Global_Event` int(11) NOT NULL,
  PRIMARY KEY (`Id_Event`,`Id_User`,`Id_Global_Event`),
  KEY `Id_User` (`Id_User`,`Id_Global_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sub_event_excluded_team_member`
--

INSERT INTO `sub_event_excluded_team_member` (`Id_Event`, `Id_User`, `Id_Global_Event`) VALUES
(14, 12, 3);

-- --------------------------------------------------------

--
-- Table structure for table `superuser`
--

CREATE TABLE IF NOT EXISTS `superuser` (
  `Id_Superuser` int(11) NOT NULL AUTO_INCREMENT,
  `Login` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Superuser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `teaching_role`
--

CREATE TABLE IF NOT EXISTS `teaching_role` (
  `Id_Role` int(11) NOT NULL AUTO_INCREMENT,
  `Role_EN` varchar(255) NOT NULL,
  `Role_FR` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `teaching_role`
--

INSERT INTO `teaching_role` (`Id_Role`, `Role_EN`, `Role_FR`) VALUES
(1, 'Professor', 'Professeur'),
(2, 'Teaching assistant', 'Assistant'),
(3, 'Teaching student', 'Elève moniteur');

-- --------------------------------------------------------

--
-- Table structure for table `teaching_team_member`
--

CREATE TABLE IF NOT EXISTS `teaching_team_member` (
  `Id_Global_Event` int(11) NOT NULL,
  `Id_User` int(11) NOT NULL,
  `Id_Role` int(11) NOT NULL,
  PRIMARY KEY (`Id_Global_Event`,`Id_User`),
  KEY `Id_User` (`Id_User`),
  KEY `Id_Role` (`Id_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `teaching_team_member`
--

INSERT INTO `teaching_team_member` (`Id_Global_Event`, `Id_User`, `Id_Role`) VALUES
(1, 2, 1),
(2, 7, 1),
(3, 11, 1),
(3, 12, 1),
(4, 13, 1),
(5, 13, 1);

-- --------------------------------------------------------

--
-- Table structure for table `time_range_event`
--

CREATE TABLE IF NOT EXISTS `time_range_event` (
  `Id_Event` int(11) NOT NULL,
  `Start` datetime NOT NULL,
  `End` datetime NOT NULL,
  PRIMARY KEY (`Id_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `time_range_event`
--

INSERT INTO `time_range_event` (`Id_Event`, `Start`, `End`) VALUES
(1, '2015-03-25 08:30:00', '2015-03-25 12:30:00'),
(2, '2015-04-02 12:00:00', '2015-04-02 13:00:00'),
(6, '2015-03-20 08:30:00', '2015-03-20 12:00:00'),
(11, '2015-03-29 08:30:00', '2015-03-29 12:00:00'),
(15, '2015-03-02 08:30:00', '2015-03-02 12:00:00'),
(40, '2015-03-06 14:00:00', '2015-03-06 16:00:00'),
(41, '2015-03-13 14:00:00', '2015-03-13 16:00:00'),
(42, '2015-03-20 14:00:00', '2015-03-20 16:00:00'),
(43, '2015-03-27 14:00:00', '2015-03-27 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `Id_User` int(11) NOT NULL AUTO_INCREMENT,
  `Id_ULg` varchar(20) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Surname` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_User`),
  UNIQUE KEY `ulg_id_unique` (`Id_ULg`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Id_User`, `Id_ULg`, `Name`, `Surname`) VALUES
(1, 's013194', 'Romain', 'Henry'),
(2, 'u220236', 'Eric', 'Delhez'),
(3, 's123578', 'Tom', 'Fastrez'),
(4, 's131400', 'Odile', 'Izem'),
(5, 'u011975', 'Jacques', 'Verly'),
(6, 's023178', 'Patrick', 'Gérard'),
(7, 'u216357', 'Benoît', 'Donnet'),
(8, 's114310', 'Morgane', 'Igliesias'),
(9, 's101052', 'Pierre', 'Mortier'),
(11, 'u016785', 'Bernard', 'Vanbrabant'),
(12, 'u200937', 'Cécile', 'Vercheval'),
(13, 'u013317', 'Pascal', 'Gribomont'),
(14, 's060934', '', ''),
(15, 's114330', '', ''),
(16, 's114352', '', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_event`
--
ALTER TABLE `academic_event`
  ADD CONSTRAINT `academic_event_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `academic_event_category`
--
ALTER TABLE `academic_event_category`
  ADD CONSTRAINT `academic_event_category_ibfk_1` FOREIGN KEY (`Id_Category`) REFERENCES `event_category` (`Id_Category`) ON DELETE CASCADE;

--
-- Constraints for table `academic_event_file`
--
ALTER TABLE `academic_event_file`
  ADD CONSTRAINT `academic_event_file_ibfk_1` FOREIGN KEY (`Id_File`) REFERENCES `file` (`Id_File`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_event_file_ibfk_2` FOREIGN KEY (`Id_Event`) REFERENCES `academic_event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE;

--
-- Constraints for table `date_range_event`
--
ALTER TABLE `date_range_event`
  ADD CONSTRAINT `date_range_event_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `deadline_event`
--
ALTER TABLE `deadline_event`
  ADD CONSTRAINT `deadline_event_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `dynamic_export`
--
ALTER TABLE `dynamic_export`
  ADD CONSTRAINT `dynamic_export_ibfk_1` FOREIGN KEY (`Id_Export`) REFERENCES `event_export` (`Id_Export`) ON DELETE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Id_Recurrence`) REFERENCES `recurrence` (`Id_Recurrence`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_ibfk_2` FOREIGN KEY (`Id_Category`) REFERENCES `event_category` (`Id_Category`) ON DELETE CASCADE;

--
-- Constraints for table `event_annotation`
--
ALTER TABLE `event_annotation`
  ADD CONSTRAINT `event_annotation_ibfk_1` FOREIGN KEY (`Id_Student`) REFERENCES `student` (`Id_Student`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_annotation_ibfk_2` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `event_export`
--
ALTER TABLE `event_export`
  ADD CONSTRAINT `event_export_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE;

--
-- Constraints for table `export_filter`
--
ALTER TABLE `export_filter`
  ADD CONSTRAINT `export_filter_ibfk_1` FOREIGN KEY (`Id_Filter`) REFERENCES `filter` (`Id_Filter`) ON DELETE CASCADE,
  ADD CONSTRAINT `export_filter_ibfk_2` FOREIGN KEY (`Id_Export`) REFERENCES `dynamic_export` (`Id_Export`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_staff_member`
--
ALTER TABLE `faculty_staff_member`
  ADD CONSTRAINT `faculty_staff_member_ibfk_1` FOREIGN KEY (`Id_Faculty_Member`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE;

--
-- Constraints for table `favorite_event`
--
ALTER TABLE `favorite_event`
  ADD CONSTRAINT `favorite_event_ibfk_1` FOREIGN KEY (`Id_Student`) REFERENCES `student` (`Id_Student`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_event_ibfk_2` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE;

--
-- Constraints for table `global_event`
--
ALTER TABLE `global_event`
  ADD CONSTRAINT `global_event_ibfk_1` FOREIGN KEY (`Id_Owner`) REFERENCES `faculty_staff_member` (`Id_Faculty_Member`) ON DELETE CASCADE;

--
-- Constraints for table `global_event_file`
--
ALTER TABLE `global_event_file`
  ADD CONSTRAINT `global_event_file_ibfk_1` FOREIGN KEY (`Id_File`) REFERENCES `file` (`Id_File`) ON DELETE CASCADE,
  ADD CONSTRAINT `global_event_file_ibfk_2` FOREIGN KEY (`Id_Global_Event`) REFERENCES `global_event` (`Id_Global_Event`) ON DELETE CASCADE;

--
-- Constraints for table `global_event_pathway`
--
ALTER TABLE `global_event_pathway`
  ADD CONSTRAINT `global_event_pathway_ibfk_1` FOREIGN KEY (`Id_Pathway`) REFERENCES `pathway` (`Id_Pathway`) ON DELETE CASCADE,
  ADD CONSTRAINT `global_event_pathway_ibfk_2` FOREIGN KEY (`Id_Global_Event`) REFERENCES `global_event` (`Id_Global_Event`) ON DELETE CASCADE;

--
-- Constraints for table `global_event_subscription`
--
ALTER TABLE `global_event_subscription`
  ADD CONSTRAINT `global_event_subscription_ibfk_1` FOREIGN KEY (`Id_Global_Event`) REFERENCES `global_event` (`Id_Global_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `global_event_subscription_ibfk_2` FOREIGN KEY (`Id_Student`) REFERENCES `student` (`Id_Student`) ON DELETE CASCADE;

--
-- Constraints for table `independent_event`
--
ALTER TABLE `independent_event`
  ADD CONSTRAINT `independent_event_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `academic_event` (`Id_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `independent_event_ibfk_2` FOREIGN KEY (`Id_Owner`) REFERENCES `faculty_staff_member` (`Id_Faculty_Member`) ON DELETE CASCADE;

--
-- Constraints for table `independent_event_manager`
--
ALTER TABLE `independent_event_manager`
  ADD CONSTRAINT `independent_event_manager_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `academic_event` (`Id_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `independent_event_manager_ibfk_2` FOREIGN KEY (`Id_User`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE,
  ADD CONSTRAINT `independent_event_manager_ibfk_3` FOREIGN KEY (`Id_Role`) REFERENCES `teaching_role` (`Id_Role`) ON DELETE CASCADE;

--
-- Constraints for table `independent_event_pathway`
--
ALTER TABLE `independent_event_pathway`
  ADD CONSTRAINT `independent_event_pathway_ibfk_1` FOREIGN KEY (`Id_Pathway`) REFERENCES `pathway` (`Id_Pathway`) ON DELETE CASCADE,
  ADD CONSTRAINT `independent_event_pathway_ibfk_2` FOREIGN KEY (`Id_Event`) REFERENCES `independent_event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `mobile_event_update`
--
ALTER TABLE `mobile_event_update`
  ADD CONSTRAINT `mobile_event_update_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `mobile_event_update_ibfk_2` FOREIGN KEY (`Id_User`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE;

--
-- Constraints for table `modification`
--
ALTER TABLE `modification`
  ADD CONSTRAINT `modification_ibfk_1` FOREIGN KEY (`Id_Request`) REFERENCES `modification_request` (`Id_Request`) ON DELETE CASCADE,
  ADD CONSTRAINT `modification_ibfk_2` FOREIGN KEY (`Id_Target`) REFERENCES `modification_target` (`Id_Target`) ON DELETE CASCADE;

--
-- Constraints for table `modification_request`
--
ALTER TABLE `modification_request`
  ADD CONSTRAINT `modification_request_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `modification_request_ibfk_2` FOREIGN KEY (`Id_Sender`) REFERENCES `faculty_staff_member` (`Id_Faculty_Member`) ON DELETE CASCADE;

--
-- Constraints for table `recurrence`
--
ALTER TABLE `recurrence`
  ADD CONSTRAINT `recurrence_ibfk_1` FOREIGN KEY (`Id_Recur_Category`) REFERENCES `recurrence_category` (`Id_Recur_Category`) ON DELETE CASCADE;

--
-- Constraints for table `schedule_access`
--
ALTER TABLE `schedule_access`
  ADD CONSTRAINT `schedule_access_ibfk_1` FOREIGN KEY (`Id_Student`) REFERENCES `student` (`Id_Student`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_access_ibfk_2` FOREIGN KEY (`Id_Faculty_Member`) REFERENCES `faculty_staff_member` (`Id_Faculty_Member`) ON DELETE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`Id_Student`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE;

--
-- Constraints for table `student_event`
--
ALTER TABLE `student_event`
  ADD CONSTRAINT `student_event_ibfk_1` FOREIGN KEY (`Id_Owner`) REFERENCES `student` (`Id_Student`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_event_ibfk_2` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `student_event_category`
--
ALTER TABLE `student_event_category`
  ADD CONSTRAINT `student_event_category_ibfk_1` FOREIGN KEY (`Id_Category`) REFERENCES `event_category` (`Id_Category`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_event_category_ibfk_2` FOREIGN KEY (`Id_Student`) REFERENCES `student` (`Id_Student`) ON DELETE CASCADE;

--
-- Constraints for table `student_pathway`
--
ALTER TABLE `student_pathway`
  ADD CONSTRAINT `student_pathway_ibfk_1` FOREIGN KEY (`Id_Student`) REFERENCES `student` (`Id_Student`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_pathway_ibfk_2` FOREIGN KEY (`Id_Pathway`) REFERENCES `pathway` (`Id_Pathway`) ON DELETE CASCADE;

--
-- Constraints for table `sub_event`
--
ALTER TABLE `sub_event`
  ADD CONSTRAINT `sub_event_ibfk_1` FOREIGN KEY (`Id_Global_Event`) REFERENCES `global_event` (`Id_Global_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `sub_event_ibfk_2` FOREIGN KEY (`Id_Event`) REFERENCES `academic_event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `sub_event_excluded_pathway`
--
ALTER TABLE `sub_event_excluded_pathway`
  ADD CONSTRAINT `sub_event_excluded_pathway_ibfk_1` FOREIGN KEY (`Id_Global_Event`, `Id_Pathway`) REFERENCES `global_event_pathway` (`Id_Global_Event`, `Id_Pathway`) ON DELETE CASCADE,
  ADD CONSTRAINT `sub_event_excluded_pathway_ibfk_2` FOREIGN KEY (`Id_Event`) REFERENCES `sub_event` (`Id_Event`) ON DELETE CASCADE;

--
-- Constraints for table `sub_event_excluded_team_member`
--
ALTER TABLE `sub_event_excluded_team_member`
  ADD CONSTRAINT `sub_event_excluded_team_member_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `sub_event` (`Id_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `sub_event_excluded_team_member_ibfk_2` FOREIGN KEY (`Id_User`, `Id_Global_Event`) REFERENCES `teaching_team_member` (`Id_User`, `Id_Global_Event`) ON DELETE CASCADE;

--
-- Constraints for table `teaching_team_member`
--
ALTER TABLE `teaching_team_member`
  ADD CONSTRAINT `teaching_team_member_ibfk_1` FOREIGN KEY (`Id_Global_Event`) REFERENCES `global_event` (`Id_Global_Event`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaching_team_member_ibfk_2` FOREIGN KEY (`Id_User`) REFERENCES `user` (`Id_User`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaching_team_member_ibfk_3` FOREIGN KEY (`Id_Role`) REFERENCES `teaching_role` (`Id_Role`) ON DELETE CASCADE;

--
-- Constraints for table `time_range_event`
--
ALTER TABLE `time_range_event`
  ADD CONSTRAINT `time_range_event_ibfk_1` FOREIGN KEY (`Id_Event`) REFERENCES `event` (`Id_Event`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
