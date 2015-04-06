UPDATE ulg_student LEFT JOIN
(
	SELECT Id_ULg, Id_Pathway FROM user NATURAL JOIN 
	( SELECT Id_Student AS Id_User, Id_Pathway FROM student_pathway WHERE Acad_Start_Year = 2014 ) AS path
) AS users
ON ulg_student.Id_ULg_Student = users.Id_ULg 
SET ulg_student.Id_Pathway = users.Id_Pathway WHERE ulg_student.Id_ULg_Student = users.Id_ULg;