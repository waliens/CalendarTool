USE calendar_tool;

--
-- Insert data for testing the application
--


--
-- Some users
-- 

INSERT INTO `pathway` (`Id_Pathway`, `Name_Long`, `Name_Short`) VALUES
('ABICAR000201', '2e année du grade de bachelier en sciences de l''ingénieur, orientation ingénieur civil architecte', '2e an. bac. sc. ing., or. ing. civ. architecte'),
('ABICIV000201', '2e année du grade de bachelier en sciences de l''ingénieur, orientation ingénieur civil', '2e an. bac. sc. ing., or. ing. civil'),
('ABICIV000301', '3e an. bac. sc. ing., or. ing. civil', '3e année du grade de bachelier en sciences de l''ingénieur, orientation ingénieur civil'),
('ABICIV009901', 'Bachelier en sciences de l''ingénieur, orientation ingénieur civil', 'Bac. sc. ingé., or. ingé. civ. '),
('ABINFO000201', '2e année du grade de bachelier en sciences informatiques', '2e an. bac. sc. informatiques'),
('ABINFO009901', 'Bachelier en sciences informatiques', 'Bac. sc. info.');

INSERT INTO `user` (`Id_User`, `Id_ULg`, `Name`, `Surname`) VALUES
(1, 's013194', 'Romain', 'Mormont'),
(2, 'u220236', 'Eric', 'Delhez'),
(3, 's123578', 'Tom', 'Fastrez'),
(4, 's131400', '', ''),
(5, 'u011975', 'Jacques', 'Verly'),
(6, 's023178', '', ''),
(7, 'u216357', 'Benoît', 'Donnet'),
(8, 's114310', '', ''),
(9, 's101052', '', '');

INSERT INTO `student` (`Id_Student`, `Mobile_User`) VALUES
(1, 0), (3, 0), (4, 0), (6, 0), (8, 0), (9, 0);

INSERT INTO `student_pathway` (`Id_Pathway`, `Id_Student`, `Acad_Start_Year`) VALUES
('ABICIV000201', 1, 2014),
('ABINFO000201', 3, 2014),
('ABICIV000201', 4, 2014),
('ABINFO009901', 6, 2014),
('ABICIV000201', 8, 2014),
('ABICIV009901', 9, 2014);

INSERT INTO `faculty_staff_member` (`Id_Faculty_Member`) VALUES
(2), (5), (7);

--
-- Events
--

-- global events

INSERT INTO `global_event` (`Id_Global_Event`, `ULg_Identifier`, `Name_Short`, `Name_Long`, `Id_Owner`, `Period`, `Description`, `Feedback`, `Workload_Th`, `Workload_Pr`, `Workload_Au`, `Workload_St`, `Language`, `Acad_Start_Year`) VALUES
(1, 'MATH0009-4', 'Mathématiques générales, Partim A, 30h Th, 30h Pr, BASTIN Françoise', 'Mathématiques générales, Partim A', 2, 'Q1', '', '', 30, 30, 0, 0, 'EN', 2014),
(2, 'INFO2047-1', 'Introduction à la programmation', 'Introduction à la programmation, 12h Th, 12h Pr, DONNET Benoît, FONTENEAU Raphaël', 7, 'Q1', '', '', 12, 12, 0, 0, 'FR', 2014);

INSERT INTO `global_event_pathway` (`Id_Global_Event`, `Id_Pathway`) VALUES
(2, 'ABICIV000201'), 
(1, 'ABINFO009901');

INSERT INTO `global_event_subscription` (`Id_Global_Event`, `Id_Student`, `Free_Student`) VALUES
(1, 6, 0),
(2, 1, 0),
(2, 4, 0),
(2, 8, 0);

INSERT INTO `teaching_team_member` (`Id_Global_Event`, `Id_User`, `Id_Role`) VALUES
(23, 2, 1), (40, 7, 1);

-- events 

INSERT INTO `event` (`Id_Event`, `Name`, `Description`, `Id_Recurrence`, `Place`, `Id_Category`) VALUES
(1, 'Cours introductif', 'Premier cours', 1, 'R18/B28', 1),
(2, 'Footcheball', 'Match de foot au Brésil', 1, 'Sao Polo', 8),
(3, 'Conférence sur le theme de l''école', 'Jean Phillibert nous racontera son parcours scolaire', 1, '604/B4', 4),
(4, 'Premier projet', 'Projet de programmation de folie', 1, NULL, 5);

-- event type data

INSERT INTO `academic_event` (`Id_Event`, `Feedback`, `Workload`, `Practical_Details`) VALUES
(1, '', 2, 'Bring your laptop'),
(2, '', 1, 'Bring your courage'),
(4, '', 35, 'It will be fun');

INSERT INTO `independent_event` (`Id_Event`, `Id_Owner`, `Public`) VALUES
(2, 7, 1);

INSERT INTO `student_event` (`Id_Event`, `Id_Owner`) VALUES
(3, 9);

INSERT INTO `sub_event` (`Id_Event`, `Id_Global_Event`) VALUES
(1, 40),
(4, 40);


-- event time data

INSERT INTO `deadline_event` (`Id_Event`, `Limit`) VALUES
(4, '2015-04-26 23:59:00');

INSERT INTO `date_range_event` (`Id_Event`, `Start`, `End`) VALUES
(3, '2015-05-01', '2015-05-06');

INSERT INTO `time_range_event` (`Id_Event`, `Start`, `End`) VALUES
(1, '2015-03-25 08:30:00', '2015-03-25 12:30:00'),
(2, '2015-04-02 12:00:00', '2015-04-02 13:00:00');

