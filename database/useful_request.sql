SELECT * FROM
(
	(SELECT Id_Event, 'date_range' as type FROM date_range_event WHERE Id_Event = 19)
	UNION ALL
	(SELECT Id_Event, 'time_range' as type FROM time_range_event WHERE Id_Event = 19)
	UNION ALL
	(SELECT Id_Event, 'deadline' as type FROM deadline_event WHERE Id_Event = 19)
) AS der


SELECT * FROM
(
	(SELECT Id_Event, 'sub' as type FROM sub_event WHERE Id_Event = 19)
	UNION ALL
	(SELECT Id_Event, 'indep' as type FROM independent_event WHERE Id_Event = 19)
	UNION ALL
	(SELECT Id_Event, 'stud' as type FROM student_event WHERE Id_Event = 19)
) AS der

SELECT * FROM event
NATURAL LEFT JOIN ( SELECT Id_Event, 'acad'  AS acad FROM academic_event ) AS academic 
NATURAL LEFT JOIN ( SELECT Id_Event, 'sub'   AS sub FROM sub_event ) AS sube 
NATURAL LEFT JOIN ( SELECT Id_Event, 'indep' AS indep FROM independent_event ) AS indepe 
NATURAL LEFT JOIN ( SELECT Id_Event, 'stud'  AS stud FROM student_event ) AS stude 
NATURAL LEFT JOIN ( SELECT Id_Event, 'time'  AS time FROM time_range_event ) AS timer 
NATURAL LEFT JOIN ( SELECT Id_Event, 'date'  AS `date` FROM date_range_event ) AS dater 
NATURAL LEFT JOIN ( SELECT Id_Event, 'dead'  AS dead FROM deadline_event ) AS deadl  