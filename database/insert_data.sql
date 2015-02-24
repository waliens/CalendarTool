USE calendar_tool;

--
-- Insert modification target data
--

INSERT INTO `modification_target` (`Name`, `Type`) VALUES
('place', 'varchar(255)'),
('to_date_range', 'ereg(''^start:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}),end:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})$'')'),
('to_deadline', 'ereg(''deadline:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2})'')'),
('change_date', 'ereg(''(start|end):([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})'')'),
('change_time', 'ereg(''(start|end|deadline):([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2})'')');

--
-- Insert event category
-- 

INSERT INTO `event_category` (`Name`, `Color`, `Description`) VALUES
('Cours théorique', '#4257d6', ''),
('Travaux pratiques', '#d48544', ''),
('Laboratoire', '#dd3b45', '');

--
-- Add team member category
--

INSERT INTO `teaching_role` (`Role`, `Description`) VALUES
('Professeur', ''),
('Assistant', ''),
('Elève moniteur', '');

-- 
-- Insert data for testing
--

INSERT INTO `user` (`Name`, `Surname`, `Id_ULg`) VALUES
('Romain', 'Mormont', 's110940'),
('Eric', 'Delhez', 'u220236'),
('Tom', 'Fastrez', 's253621');

INSERT INTO `student` (`Id_Student`, `Mobile_User`) VALUES
('1', '0'),
('3', '0');

INSERT INTO `faculty_staff_member` (`Id_Faculty_Member`) VALUES
('2');

