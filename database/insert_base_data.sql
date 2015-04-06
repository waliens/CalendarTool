USE calendar_tool;
--
-- Insert required for the application to work. Essentially categories data
--

--
-- Insert modification target data
--

INSERT INTO `modification_target` (`Id_Target`, `Name`, `Type`) VALUES
(1, 'place', 'varchar(255)'),
(2, 'to_date_range', 'ereg(''^start:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}),end:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})$'')'),
(3, 'to_deadline', 'ereg(''deadline:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2})'')'),
(4, 'change_date', 'ereg(''(start|end):([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})'')'),
(5, 'change_time', 'ereg(''(start|end|deadline):([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2})'')');

--
-- Insert event categoriesy
-- 

INSERT INTO `event_category` (`Id_Category`, `Name_FR`, `Name_EN`, `Description_FR`, `Description_EN`, `Color`) VALUES
(1, 'Cours théorique', 'Lecture', 'Cours théorique', 'Lecture', '#00a3c7'),
(2, 'Laboratoire', 'Lab', 'Laboratoire', 'Lab', '#066d18'),
(3, 'TP', 'Exercise session', 'Travaux pratiques (exercices)', 'Exercise session', '#545454'),
(4, 'Conférence', 'Conference', 'Conférence', 'Conference', '#0010a5'),
(5, 'Projet', 'Project', 'Deadline, cours, événement lié à un projet', 'Deadline, course, event linked to a project', '#6300a5'),
(6, 'Devoir', 'Homework', 'Devoir', 'Homework', '#fec500'),
(7, 'Q & R', 'Q & A', 'Session de questions/réponses ', 'Questions/answers session', '#7279db'),
(8, 'Autre', 'Other', 'Catégorie pour les événements ne pouvant être classés dans aucune autre catégorie', 'Category for the events that cannot be associated with any other category', '#0064b5'),
(9, 'Sport', 'Sport', 'Activité sportive', 'Sport activity', '#ffff00'),
(10, 'Chapiteau', 'Chapiteau', 'Célèbre chapiteau du Val-Benoît', 'Famous Val-Benoit chapiteau', '#ab699b'),
(11, 'Travail', 'Work', 'Événement lié au travail (job d''étudiant,  travail personnel,...)', 'Event linked to work (student job, personnal work,...)', '#ff9400'),
(12, 'Restaurant', 'Restaurant', 'Bon appétit!', 'Good appetite! ', '#48cfd2'),
(13, 'Soirée', 'Party', 'Sortie, soirée,...', 'Night-out, party,...', '#0fad00'),
(14, 'Personnel', 'Personnal', 'Activité personnelle', 'Personnal activity', '#8cc700'),
(15, 'Loisirs', 'Leisure', 'Loisirs (modélisme, bricolage,...)', 'Leisure (model-making, DIY,...)', '#3f5643'),
(16, 'Musique', 'Music', 'Musique (concert, répétition,...)', 'Music (concert, rehearsal,...)', '#553300'),
(17, 'Anniversaire', 'Birthday', 'Anniversaire', 'Birthday', '#540055'),
(18, 'Autre', 'Other', 'Catégorie pour les événements ne pouvant être classés dans aucune autre catégorie', 'Category for the events that cannot be associated with any other category', '#b6b9c0'),
(19, 'Examen oral', 'Oral exam', 'Examen oral', 'Oral exam', '#ff0000'),
(20, 'Examen écrit', 'Written exam', 'Examen écrit', 'Written exam', '#ff6600'),
(21, 'Interrogation', 'Written test', 'Interrogation écrite', 'Written test', '#c5007c');

INSERT INTO `academic_event_category` (`Id_Category`) VALUES
(1), (2), (3), (4), (5), (6), (7), (8), (19), (20), (21); 

INSERT INTO `student_event_category` (`Id_Category`, `Id_Student`) VALUES
(9, NULL), (10, NULL), (11, NULL), (12, NULL), (13, NULL), (14, NULL), (15, NULL), (16, NULL), (17, NULL), (18, NULL);

--
-- Add team member category
--

INSERT INTO `teaching_role` (`Id_Role`, `Role_FR`, `Role_EN`) VALUES
(1, 'Professeur', 'Professor'),
(2, 'Assistant', 'Teaching assistant'),
(3, 'Elève moniteur', 'Teaching student');

--
-- Insert recurrence category
--

INSERT INTO `recurrence_category` (`Id_Recur_Category`, `Recur_Category_FR`, `Recur_Category_EN`) VALUES
(1, 'Journalier', 'Daily'),
(2, 'Hebdomadaire', 'Weekly'),
(3, 'Bimensuel', 'Bi-monthly'),
(4, 'Mensuel', 'Monthly'),
(5, 'Annuel', 'Yearly'),
(6, 'Jamais', 'Never');

INSERT INTO `recurrence` (`Id_Recurrence`, `Id_Recur_Category`) VALUES
(1, 6); -- recurrence non recursive events


-- 
-- Insert event filter
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