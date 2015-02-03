# Unit tests for the ct.util.database module.

## Data 

A test MySQL database is provided in the data folder. This database contains one table called 'chanson' which has the following strucure : 

	CREATE TABLE IF NOT EXISTS `chanson` (
	  `id_chanson` int(11) NOT NULL AUTO_INCREMENT,
	  `titre` varchar(200) NOT NULL,
	  `artiste` varchar(200) NOT NULL,
	  `filepath` varchar(200) NOT NULL,
	  `commentaire` varchar(200) NOT NULL,
	  PRIMARY KEY (`id_chanson`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;


This table contains 38 different entries of increasing ids (from 1). 

## Test cases

