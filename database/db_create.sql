
CREATE DATABASE IF NOT EXISTS calendar_tool 
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;

USE calendar_tool;

START TRANSACTION;

-- 
-- Tables containing user informations
--

CREATE TABLE IF NOT EXISTS `user`
(
	`Id_User` int(11) NOT NULL AUTO_INCREMENT,
	`Id_ULg` varchar(20) NOT NULL,
	`Name` varchar(255) NOT NULL,
	`Surname` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_User`), 
	CONSTRAINT ulg_id_unique UNIQUE (Id_ULg)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `student`
(
	`Id_Student` int(11) NOT NULL,
	`Mobile_User` boolean NOT NULL,
	FOREIGN KEY(`Id_Student`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Student`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `faculty_staff_member`
(
	`Id_Faculty_Member` int(11) NOT NULL,
	FOREIGN KEY(`Id_Faculty_Member`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Faculty_Member`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `pathway`
(
	`Id_Pathway` varchar(20) NOT NULL,
	`Name_Long` varchar(255) NOT NULL,
	`Name_Short` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_Pathway`) 
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `student_pathway`
(
	`Id_Pathway` varchar(20) NOT NULL,
	`Id_Student` int(11) NOT NULL,
	`Acad_Start_Year` year NOT NULL,
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Student`, `Id_Pathway`, `Acad_Start_Year`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `schedule_access`
(
	`Id_Faculty_Member` int(11) NOT NULL,
	`Id_Student` int(11) NOT NULL,
	`Status` enum('sent', 'refused', 'accepted') NOT NULL,
	PRIMARY KEY(`Id_Faculty_Member`, `Id_Student`),
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Faculty_Member`) REFERENCES `faculty_staff_member`(`Id_Faculty_Member`) ON DELETE CASCADE
) ENGINE=InnoDB;

--
-- Log and administration tables
--

CREATE TABLE IF NOT EXISTS `activity`
(
	`Id_Activity` int(11) NOT NULL AUTO_INCREMENT,
	`Action` text NOT NULL,
	`Id_User` int(11), 
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Activity`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `superuser`
(
	`Id_Superuser` int(11) NOT NULL AUTO_INCREMENT,
	`Login` varchar(255) NOT NULL,
	`Password` varchar(255) NOT NULL, ## must be hashed and salted
	PRIMARY KEY(`Id_Superuser`)
) ENGINE=InnoDB;

--
-- Files
--

CREATE TABLE IF NOT EXISTS `file`
(
	`Id_File` int(11) NOT NULL AUTO_INCREMENT,
	`Filepath` varchar(255) NOT NULL,
	`Id_User` int(11) NOT NULL,
	`Name` varchar(255),
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_File`)
) ENGINE=InnoDB;

--
-- Events
--

CREATE TABLE IF NOT EXISTS `global_event`
(
	`Id_Global_Event` int(11) NOT NULL AUTO_INCREMENT,
	`ULg_Identifier` varchar(20) NOT NULL,
	`Name_Short` varchar(255) NOT NULL,
	`Name_Long` varchar(255) NOT NULL,
	`Id_Owner` int(11) NOT NULL,
	`Period` enum('Q1', 'Q2', 'TA') NOT NULL,
	`Description` text NOT NULL,
	`Feedback` text NOT NULL,
	`Workload_Th` int(11) NOT NULL,
	`Workload_Pr` int(11) NOT NULL,
	`Workload_Au` int(11) NOT NULL,
	`Workload_St` int(11) NOT NULL,
	`Language` enum('EN','FR') NOT NULL,
	`Acad_Start_Year` year NOT NULL,
	FOREIGN KEY(`Id_Owner`) REFERENCES `faculty_staff_member`(`Id_Faculty_Member`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Global_Event`),
	CONSTRAINT course_year UNIQUE (ULg_Identifier, Acad_Start_Year)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `global_event_subscription`
(
	`Id_Global_Event` int(11) NOT NULL,
	`Id_Student` int(11) NOT NULL,
	`Free_Student` boolean NOT NULL,
	FOREIGN KEY(`Id_Global_Event`) REFERENCES `global_event`(`Id_Global_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Global_Event`, `Id_Student`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `global_event_pathway`
(
	`Id_Global_Event` int(11) NOT NULL,
	`Id_Pathway` varchar(20) NOT NULL,
	FOREIGN KEY(`Id_Pathway`) REFERENCES `pathway`(`Id_Pathway`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Global_Event`) REFERENCES `global_event`(`Id_Global_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Global_Event`, `Id_Pathway`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `global_event_file`
(
	`Id_File` int(11) NOT NULL,
	`Id_Global_Event` int(11) NOT NULL, 
	FOREIGN KEY(`Id_File`) REFERENCES `file`(`Id_File`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Global_Event`) REFERENCES `global_event`(`Id_Global_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_File`, `Id_Global_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `teaching_role`
(
	`Id_Role` int(11) NOT NULL AUTO_INCREMENT,
	`Role_EN` varchar(255) NOT NULL, 
	`Role_FR` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_Role`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `teaching_team_member`
(
	`Id_Global_Event` int(11) NOT NULL, 
	`Id_User` int(11) NOT NULL, 
	`Id_Role` int(11) NOT NULL,
	FOREIGN KEY(`Id_Global_Event`) REFERENCES `global_event`(`Id_Global_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Role`) REFERENCES `teaching_role`(`Id_Role`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Global_Event`, `Id_User`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event_category`
(
	`Id_Category` int(11) NOT NULL AUTO_INCREMENT,
	`Name_EN` varchar(255) NOT NULL,
	`Name_FR` varchar(255) NOT NULL,
	`Description_EN` text NOT NULL,
	`Description_FR` text NOT NULL,
	`Color` varchar(7) NOT NULL,
	PRIMARY KEY(`Id_Category`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `academic_event_category`
(
	`Id_Category` int(11) NOT NULL,
	PRIMARY KEY(`Id_Category`),
	FOREIGN KEY(`Id_Category`) REFERENCES `event_category`(`Id_Category`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `student_event_category`
(
	`Id_Category` int(11) NOT NULL,
	`Id_Student` int(11),
	PRIMARY KEY(`Id_Category`),
	FOREIGN KEY(`Id_Category`) REFERENCES `event_category`(`Id_Category`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `recurrence_category`
(	
	`Id_Recur_Category` int(11) NOT NULL AUTO_INCREMENT, -- ID 6 for category "never"
	`Recur_Category_EN` varchar(255) NOT NULL,
	`Recur_Category_FR` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_Recur_Category`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `recurrence`
(
	`Id_Recurrence` int(11) NOT NULL AUTO_INCREMENT, -- ID 1 for non reccurent event
	`Id_Recur_Category` int(11) NOT NULL,
	FOREIGN KEY(`Id_Recur_Category`) REFERENCES `recurrence_category`(`Id_Recur_Category`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Recurrence`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event`
(
	`Id_Event` int(11) NOT NULL AUTO_INCREMENT,
	`Name` varchar(255) NOT NULL,
	`Description` text NOT NULL, 
	`Id_Recurrence` int(11) NOT NULL, -- ID 1 for non reccurent event
	`Place` varchar(255),
	`Id_Category` int(11) NOT NULL,
	PRIMARY KEY(`Id_Event`),
	FOREIGN KEY(`Id_Recurrence`) REFERENCES `recurrence`(`Id_Recurrence`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Category`) REFERENCES `event_category`(`Id_Category`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `date_range_event`
(
	`Id_Event` int(11) NOT NULL,
	`Start` date NOT NULL,
	`End` date NOT NULL,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `deadline_event`
(
	`Id_Event` int(11) NOT NULL,
	`Limit` datetime NOT NULL,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `time_range_event`
(
	`Id_Event` int(11) NOT NULL,
	`Start` datetime NOT NULL,
	`End` datetime NOT NULL,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `favorite_event`
(
	`Id_Event` int(11) NOT NULL, 
	`Id_Student` int(11) NOT NULL,
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`, `Id_Student`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event_annotation`
(
	`Id_Student` int(11) NOT NULL,
	`Id_Event` int(11) NOT NULL,
	`Annotation` text NOT NULL,
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`, `Id_Student`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `student_event`
(
	`Id_Event` int(11) NOT NULL,
	`Id_Owner` int(11) NOT NULL,
	FOREIGN KEY(`Id_Owner`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `academic_event`
(
	`Id_Event` int(11) NOT NULL,
	`Feedback` text NOT NULL,
	`Workload` int(11),
	`Practical_Details` text,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `sub_event`
(
	`Id_Event` int(11) NOT NULL, 
	`Id_Global_Event` int(11) NOT NULL,
	FOREIGN KEY(`Id_Global_Event`) REFERENCES `global_event`(`Id_Global_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Event`) REFERENCES `academic_event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB; 

CREATE TABLE IF NOT EXISTS `independent_event`
(
	`Id_Event` int(11) NOT NULL,
	`Id_Owner` int(11) NOT NULL,
	`Public` boolean NOT NULL,
	FOREIGN KEY(`Id_Event`) REFERENCES `academic_event`(`Id_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Owner`) REFERENCES `faculty_staff_member`(`Id_Faculty_Member`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `sub_event_excluded_pathway`
(
	`Id_Event` int(11) NOT NULL, 
	`Id_Pathway` varchar(20) NOT NULL,
	FOREIGN KEY(`Id_Pathway`) REFERENCES `pathway`(`Id_Pathway`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Event`) REFERENCES `sub_event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`,`Id_Pathway`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `independent_event_pathway`
(
	`Id_Event` int(11) NOT NULL,
	`Id_Pathway` varchar(20) NOT NULL,
	FOREIGN KEY(`Id_Pathway`) REFERENCES `pathway`(`Id_Pathway`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Event`) REFERENCES `independent_event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`,`Id_Pathway`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event_manager`
(
	`Id_Event` int(11) NOT NULL,
	`Id_User` int(11) NOT NULL,
	`Id_Role` int(11) NOT NULL,
	FOREIGN KEY(`Id_Event`) REFERENCES `academic_event`(`Id_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Role`) REFERENCES `teaching_role`(`Id_Role`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`,`Id_User`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `academic_event_file`
(
	`Id_File` int(11) NOT NULL,
	`Id_Event` int(11) NOT NULL,
	FOREIGN KEY(`Id_File`) REFERENCES `file`(`Id_File`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Event`) REFERENCES `academic_event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_File`, `Id_Event`)  
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `modification_request`
(
	`Id_Request` int(11) NOT NULL AUTO_INCREMENT,
	`Id_Event` int(11) NOT NULL,
	`Id_Sender` int(11) NOT NULL,
	`Status` enum('sent', 'accepted', 'cancelled', 'refused') NOT NULL,
	`Description` text NOT NULL,
	PRIMARY KEY(`Id_Request`),
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Sender`) REFERENCES `faculty_staff_member`(`Id_Faculty_Member`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `modification_target`
(
	`Id_Target` int(11) NOT NULL AUTO_INCREMENT, 
	`Name` varchar(255) NOT NULL,
	`Type` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_Target`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `modification`
(
	`Id_Request` int(11) NOT NULL,
	`Id_Target` int(11) NOT NULL, 
	`Proposition` text NOT NULL,
	FOREIGN KEY(`Id_Request`) REFERENCES `modification_request`(`Id_Request`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Target`) REFERENCES `modification_target`(`Id_Target`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Request`, `Id_Target`)
) ENGINE=InnoDB;


--
-- Mobile 
--

CREATE TABLE IF NOT EXISTS `mobile_event_update`
(
	`Id_Event` int(11) NOT NULL,
	`Id_User` int(11) NOT NULL,
	PRIMARY KEY(`Id_Event`, `Id_User`),
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE
) ENGINE=InnoDB;


--
-- Event export
-- 

CREATE TABLE IF NOT EXISTS `event_export`
(
	`Id_Export` int(11) NOT NULL AUTO_INCREMENT,
	`User_Hash` varchar(255) NOT NULL,
	`Id_User` int(11) NOT NULL,
	PRIMARY KEY(`Id_Export`),
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	UNIQUE KEY `User_Hash` (`User_Hash`),
	UNIQUE KEY `Id_User` (`Id_User`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `filter`
(
	`Id_Filter` int(11) NOT NULL AUTO_INCREMENT,
	`Name` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_Filter`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `export_filter`
(
	`Id_Filter` int(11) NOT NULL,
	`Id_Export` int(11) NOT NULL,
	`Value` varchar(255) NOT NULL,
	FOREIGN KEY(`Id_Filter`) REFERENCES `filter`(`Id_Filter`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Export`) REFERENCES `event_export`(`Id_Export`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Filter`, `Id_Export`)
) ENGINE=InnoDB;

--
-- ULg
--

CREATE TABLE IF NOT EXISTS `ulg_pathway`
(
	`Id_Pathway` varchar(20) NOT NULL,
	`Name_Short` varchar(255) NOT NULL,
	`Name_Long` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_Pathway`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ulg_student`
(
	`Id_ULg_Student` varchar(10) NOT NULL,
	`Id_Pathway` varchar(20) NOT NULL,
	FOREIGN KEY(`Id_Pathway`) REFERENCES `ulg_pathway`(`Id_Pathway`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_ULg_Student`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ulg_course`
(
	`Id_Course` varchar(15) NOT NULL,
	`Name_Short` varchar(255) NOT NULL,
	`Name_Long` varchar(255) NOT NULL,
	`Hr_Th` int(11) NOT NULL,
	`Hr_Pr` int(11) NOT NULL,
	`Hr_St` int(11) NOT NULL,
	`Hr_Au` int(11) NOT NULL,
	`Period` varchar(2) NOT NULL,
	PRIMARY KEY(`Id_Course`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ulg_fac_staff`
(
	`Id_ULg_Fac_Staff` varchar(10) NOT NULL,
	`Name` varchar(255),
	`Surname` varchar(255),
	PRIMARY KEY(`Id_ULg_Fac_Staff`) 
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ulg_course_team_member`
(
	`Id_ULg_Fac_Staff` varchar(10) NOT NULL,
	`Id_Course` varchar(15) NOT NULL,
	FOREIGN KEY(`Id_ULg_Fac_Staff`) REFERENCES `ulg_fac_staff`(`Id_ULg_Fac_Staff`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Course`) REFERENCES `ulg_course`(`Id_Course`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_ULg_Fac_Staff`, `Id_Course`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ulg_has_course`
(
	`Id_ULg_Student` varchar(10) NOT NULL,
	`Id_Course` varchar(15) NOT NULL,
	FOREIGN KEY(`Id_ULg_Student`) REFERENCES `ulg_student`(`Id_ULg_Student`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Course`) REFERENCES `ulg_course`(`Id_Course`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_ULg_Student`, `Id_Course`)
) ENGINE=InnoDB;

COMMIT;