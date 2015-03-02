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

INSERT INTO `event_category` (`Id_Category`, `Name_FR`, `Color`, `Description_FR`, `Name_EN`, `Description_EN`) VALUES
(1, 'Cours théorique', '', 'Cours théorique', 'Lecture', 'Lecture'),
(2, 'Laboratoire', '', 'Laboratoire', 'Lab', 'Lab'),
(3, 'TP', '', 'Travaux pratiques (exercices)', 'Exercise session', 'Exercise session'),
(4, 'Conférence', '', 'Conférence', 'Conference', 'Conference'),
(5, 'Projet', '', 'Deadline, cours, événement lié à un projet', 'Project', 'Deadline, course, event linked to a project'),
(6, 'Devoir', '', 'Devoir', 'Homework', 'Homework'),
(7, 'Q & R', '', 'Session de questions/réponses ', 'Q & A', 'Questions/answers session'),
(8, 'Autre', '', 'Catégorie pour les événements ne pouvant être classés dans aucune autre catégorie', 'Other', 'Category for the events that cannot be associated with any other category'),
(9, 'Sport', '', 'Activité sportive', 'Sport', 'Sport activity'),
(10, 'Chapiteau', '', 'Célèbre chapiteau du Val-Benoît', 'Chapiteau', 'Famous Val-Benoit chapiteau'),
(11, 'Travail', '', 'Événement lié au travail (job d''étudiant,  travail personnel,...)', 'Work', 'Event linked to work (student job, personnal work,...)'),
(12, 'Restaurant', '', 'Bon appétit!', 'Restaurant', 'Good appetite! '),
(13, 'Soirée', '', 'Sortie, soirée,...', 'Party', 'Night-out, party,...'),
(14, 'Personnel', '', 'Activité personnelle', 'Personnal', 'Personnal activity'),
(15, 'Loisirs', '', 'Loisirs (modélisme, bricolage,...)', 'Leisure', 'Leisure (model-making, DIY,...)'),
(16, 'Musique', '', 'Musique (concert, répétition,...)', 'Music', 'Music (concert, rehearsal,...)'),
(17, 'Anniversaire', '', 'Anniversaire', 'Birthday', 'Birthday'),
(18, 'Autre ', '', 'Événement lié au travail (job d''étudiant,  travail personnel,...)', 'Other', 'Category for the events that cannot be associated with any other category');

INSERT INTO `academic_event_category` (`Id_Category`) VALUES
(1), (2), (3), (4), (5), (6), (7), (8); 

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



