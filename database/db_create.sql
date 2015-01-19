
CREATE DATABASE calendar_tool IF NOT EXISTS;

-- 
-- Tables containing user informations
--

CREATE TABLE IF NOT EXISTS `user`
(
	`Id_User` int(11) NOT NULL,
	`Id_ULg` varchar(15) NOT NULL,
	`Name` varchar(100) NOT NULL,
	`Surname` varchar(100) NOT NULL,
	PRIMARY KEY(`Id_User`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `student`
(
	`Id_Student` int(11) NOT NULL,
	`Mobile_User` tinyint NOT NULL,
	FOREIGN KEY(`Id_Student`) REFERENCES user(`Id_User`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `faculty_staff_member`
(
	`Id_Faculty_Member` int(11) NOT NULL,
	FOREIGN KEY(`Id_Faculty_Member`) REFERENCES user(`Id_User`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `pathway`
(
	`Id_Pathway` varchar(20) NOT NULL,
	`Name_Long` varchar(250) NOT NULL,
	`Name_Short` varchar(250) NOT NULL,
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
	`Status` varchar(20) NOT NULL,
	PRIMARY KEY(`Id_Faculty_Member`, `Id_Student`),
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Faculty_Member`) REFERENCES `faculty_staff_member`(`Id_Faculty_Member`) ON DELETE CASCADE
) ENGINE=InnoDB;

--
-- Log and administration tables
--

CREATE TABLE IF NOT EXISTS `activity`
(
	`Id_Activity` int(11) NOT NULL,
	`Action` text() NOT NULL,
	`Id_User` int(11), 
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Activity`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `superuser`
(
	`Id_Superuser` int(11) NOT NULL,
	`Login` int(11) NOT NULL,
	`Password` int(11) NOT NULL,
	PRIMARY KEY(`Id_Superuser`)
) ENGINE=InnoDB;

--
-- Files
--

CREATE TABLE IF NOT EXISTS `file`
(
	`Id_File` int(11) NOT NULL,
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
	`Id_Gobal_Event` int(11) NOT NULL,
	`Id_Course` varchar(15) NOT NULL ,
	`Name` varchar(255) NOT NULL,
	`Description` text() NOT NULL,
	`Feedback` text() NOT NULL,
	`Workload_Th` int(11) NOT NULL,
	`Language` varchar(4) NOT NULL,
	`Id_Owner` int(11) NOT NULL,
	`Acad_Start_Year` year NOT NULL,
	`Period` varchar(2) NOT NULL,
	`Workload_Pr` int(11) NOT NULL,
	`Workload_Au` int(11) NOT NULL,
	FOREIGN KEY(`Id_Owner`) REFERENCES `faculty_staff_member`(`Id_Faculty_Member`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Gobal_Event`),
	CONSTRAINT OneCoursePerYear UNIQUE (`Id_Course`, `Acad_Start_Year`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `global_event_subscription`
(
	`Id_Gobal_Event` int(11) NOT NULL,
	`Id_Student` int(11) NOT NULL,
	FOREIGN KEY(`Id_Gobal_Event`) REFERENCES `global_event`(`Id_Gobal_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE
	PRIMARY KEY(`Id_Gobal_Event`, `Id_Student`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `global_event_pathways`
(
	`Id_Global_Event` int(11) NOT NULL,
	`Id_Pathway` varchar(20) NOT NULL,
	FOREIGN KEY(`Id_Pathway`) REFERENCES `pathway`(`Id_Pathway`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Gobal_Event`) REFERENCES `global_event`(`Id_Gobal_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Global_Event`, `Id_Pathway`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `global_event_file`
(
	`Id_File` int(11) NOT NULL,
	`Id_Gobal_Event` int(11) NOT NULL, 
	FOREIGN KEY(`Id_File`) REFERENCES `file`(`Id_File`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Gobal_Event`) REFERENCES `global_event`(`Id_Gobal_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_File`, `Id_Gobal_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `teaching_team_member`
(
	`Id_Gobal_Event` int(11) NOT NULL, 
	`Id_User` int(11) NOT NULL, 
	`Id_Role` int(11) NOT NULL,
	FOREIGN KEY(`Id_Gobal_Event`) REFERENCES `global_event`(`Id_Gobal_Event`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_User`) REFERENCES `user`(`Id_User`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Gobal_Event`, `Id_User`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `teaching_role`
(
	`Id_Role` int(11) NOT NULL,
	`Role` varchar(100) NOT NULL, 
	`Description` varchar(255) NOT NULL,
	PRIMARY KEY(`Id_Role`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event_category`
(
	`Id_Category` int(11) NOT NULL,
	`Name` varchar(255) NOT NULL,
	`Color` varchar(7) NOT NULL,
	`Description` varchar(255) NOT NULL,
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
	FOREIGN KEY(`Id_Category`) REFERENCES `event_category`(`Id_Category`) ON DELETE CASCADE
	FOREIGN KEY(`Id_Student`) REFERENCES `student`(`Id_Student`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `recurrence_category`
(	
	`Id_Recur_Category` int(11) NOT NULL,
	`Recur_Category` varchar(100) NOT NULL,
	PRIMARY KEY(`Id_Recur_Category`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `recurrence`
(
	`Id_Recurrence` int(11) NOT NULL,
	`Id_Recur_Category` int(11) NOT NULL,
	FOREIGN KEY(`Id_Recur_Category`) REFERENCES `recurrence_category`(`Id_Recur_Category`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Recurrence`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Event`
(
	`Id_Event` int(11) NOT NULL,
	`Name` varchar(255) NOT NULL,
	`Description` text() NOT NULL, 
	`Id_Recurrence` int(11),
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

CREATE TABLE IF NOT EXISTS `sub_event`
(
	`Id_Event` int(11) NOT NULL,
	`Id_Global_Event` int(11) NOT NULL,
	FOREIGN KEY(`Id_Gobal_Event`) REFERENCES `global_event`(`Id_Gobal_Event`) ON DELETE CASCADE,

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
	`Annotation` text() NOT NULL,
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
	`Feedback` text() NOT NULL,
	`Workload` int(11),
	`Practical_Details` text() NOT NULL,
	FOREIGN KEY(`Id_Event`) REFERENCES `event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `academic_event_pathways`
(
	`Id_Academic_Event` int(11) NOT NULL,
	`Id_Pathway` varchar(20) NOT NULL,
	FOREIGN KEY(`Id_Pathway`) REFERENCES `pathway`(`Id_Pathway`) ON DELETE CASCADE,
	FOREIGN KEY(`Id_Acad`) REFERENCES `academic_event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Academic_Event`,`Id_Pathway`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `sub_event`
(
	`Id_Event` int(11) NOT NULL, 
	`Id_Global_Event` int(11) NOT NULL,
	FOREIGN KEY(`Id_Global_Event`) REFERENCES `global_event`(`Id_Global_Event`) ON DELETE CASCADE
	FOREIGN KEY(`Id_Event`) REFERENCES `academic_event`(`Id_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
) ENGINE=InnoDB; 

CREATE TABLE IF NOT EXISTS `independent_event`
(
	`Id_Event` int(11) NOT NULL,
	`Id_Owner` int(11) NOT NULL,
	`Public` tinyint NOT NULL,
	FOREIGN KEY(`Id_Event`) REFERENCES `academic_event`(`Id_Academic_Event`) ON DELETE CASCADE,
	PRIMARY KEY(`Id_Event`)
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