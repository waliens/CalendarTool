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
('ABICIV000301', '3e année du grade de bachelier en sciences de l''ingénieur, orientation ingénieur civil', '3e an. bac. sc. ing., or. ing. civil'),
('ABICIV009901', 'Bachelier en sciences de l''ingénieur, orientation ingénieur civil', 'Bac. sc. ingé., or. ingé. civ. '),
('ABINFO000201', '2e année du grade de bachelier en sciences informatiques', '2e an. bac. sc. informatiques'),
('ABINFO000301', '3e année du grade de bachelier en sciences informatiques', '3e an. bac. sc. informatiques'),
('ABINFO000401', 'Solde de crédits du bachelier en sciences informatiques', 'Solde bac. sc. informatiques'),
('ABINFO009901', 'Bachelier en sciences informatiques', 'Bac. sc. info.'),
('AEMINF000101', 'Année préparatoire au master en sciences informatiques', 'An. prépa. master sc. info.');

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

INSERT INTO `faculty_staff_member` (`Id_Faculty_Member`) VALUES
(2),
(5),
(7),
(11),
(12),
(13);


--
-- Events
--

-- global events

INSERT INTO `global_event` (`Id_Global_Event`, `ULg_Identifier`, `Name_Short`, `Name_Long`, `Id_Owner`, `Period`, `Description`, `Feedback`, `Workload_Th`, `Workload_Pr`, `Workload_Au`, `Workload_St`, `Language`, `Acad_Start_Year`) VALUES
(1, 'MATH0009-4', 'Mathématiques générales, Partim A, 30h Th, 30h Pr, BASTIN Françoise', 'Mathématiques générales, Partim A', 2, 'Q1', '', '', 30, 30, 0, 0, 'EN', 2014),
(2, 'INFO2047-1', 'Introduction à la programmation', 'Introduction à la programmation, 12h Th, 12h Pr, DONNET Benoît, FONTENEAU Raphaël', 7, 'Q1', '', '', 12, 12, 0, 0, 'FR', 2014),
(3, 'DROI0724-1', 'Droit et activités de l''ingénieur', 'Droit et activités de l''ingénieur, 30h Th, BIQUET Christine, CLESSE Jacques, LECOCQ Pascale, VANBRABANT Bernard, Suppl: CHICHOYAN Daisy, GOL Déborah, VERCHEVAL Cécile', 11, 'Q1', 'Un cours super amusant et présentiel', '', 30, 0, 0, 0, 'FR', 2014),
(4, 'MATH0495-1', 'Eléments du calcul des probabilités ', 'Eléments du calcul des probabilités , 15h Th, 15h Pr, 5h Proj., GRIBOMONT Pascal', 13, 'Q1', '50% de réussite', '', 15, 15, 5, 0, 'FR', 2014),
(5, 'INFO0054-1', 'Programmation fonctionnelle', 'Programmation fonctionnelle, 30h Th, 25h Pr, 15h Proj., GRIBOMONT Pascal', 13, 'Q2', 'Now in english', '', 30, 25, 15, 0, 'EN', 2014);

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

INSERT INTO `teaching_team_member` (`Id_Global_Event`, `Id_User`, `Id_Role`) VALUES
(1, 2, 1),
(2, 7, 1),
(3, 11, 1),
(3, 12, 1),
(4, 13, 1),
(5, 13, 1);

-- events 

INSERT INTO `recurrence` (`Id_Recurrence`, `Id_Recur_Category`) VALUES
(2, 1),
(3, 1),
(4, 1),
(6, 1),
(5, 2);

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

-- event type data

INSERT INTO `academic_event` (`Id_Event`, `Feedback`, `Workload`, `Practical_Details`) VALUES
(1, '', 2, 'Bring your laptop'),
(2, '', 1, 'Bring your courage'),
(4, '', 35, 'It will be fun');

INSERT INTO `independent_event` (`Id_Event`, `Id_Owner`, `Public`) VALUES
(2, 7, 1);

INSERT INTO `independent_event_manager` (`Id_Event`, `Id_User`, `Id_Role`) VALUES
(2, 7, 1);

INSERT INTO `student_event` (`Id_Event`, `Id_Owner`) VALUES
(3, 9),
(19, 14),
(20, 16);

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

INSERT INTO `sub_event_excluded_pathway` (`Id_Event`, `Id_Pathway`, `Id_Global_Event`) VALUES
(11, 'ABICIV000301', 3);

INSERT INTO `sub_event_excluded_team_member` (`Id_Event`, `Id_User`, `Id_Global_Event`) VALUES
(14, 12, 3);

-- event time data

INSERT INTO `deadline_event` (`Id_Event`, `Limit`) VALUES
(4, '2015-04-26 23:59:00'),
(14, '2015-03-12 23:30:00');

INSERT INTO `date_range_event` (`Id_Event`, `Start`, `End`) VALUES
(3, '2015-05-01', '2015-05-06'),
(19, '2015-03-01', '2015-03-01'),
(20, '2015-03-02', '2015-03-08');

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
