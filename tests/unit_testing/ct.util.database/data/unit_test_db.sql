-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Sam 17 Janvier 2015 à 04:06
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `unit_test_db`
--
CREATE DATABASE IF NOT EXISTS `unit_test_db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `unit_test_db`;

-- --------------------------------------------------------

--
-- Structure de la table `chanson`
--

CREATE TABLE IF NOT EXISTS `chanson` (
  `id_chanson` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `artiste` varchar(200) NOT NULL,
  `filepath` varchar(200) NOT NULL,
  `commentaire` varchar(200) NOT NULL,
  PRIMARY KEY (`id_chanson`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Contenu de la table `chanson`
--

INSERT INTO `chanson` (`id_chanson`, `titre`, `artiste`, `filepath`, `commentaire`) VALUES
(1, 'Beatles', 'Let it be', 'song/1960-2000/The Beatles - Let It Be.m4a', '0:00'),
(2, 'Rolling Stones', 'Satisfaction', 'song/1960-2000/The Rolling Stones - I can''t get no.m4a', '0:00'),
(3, 'Queen', 'We Are The Champions', 'song/1960-2000/Queen - We Are The Champions.m4a', '0:05'),
(4, 'Patrick Hernandez', 'Born to be alive', 'song/1960-2000/Patrick Hernandez - Born to Be Alive.m4a', '0:00'),
(5, 'Manau', 'La Tribu de Dana', 'song/1960-2000/Manau - La Tribu de Dana.m4a', '0:00'),
(6, 'Nirvana', 'Smells Like Teen Spirit', 'song/1960-2000/Nirvana - Smells Like Teen Spirit.mp3', '0:00'),
(7, 'Queen', 'We Will Rock You', 'song/1960-2000/Queen - We will rock you.mp3', '0:05'),
(8, 'Jacques Brel', 'Ne me quitte pas', 'song/1960-2000/Jacques Brel - Ne Me Quitte Pas.mp3', '0:09'),
(9, 'Nino Ferrer', 'Le sud', 'song/1960-2000/Nino Ferrer - Le Sud.mp3', '0:00'),
(10, 'Maxime Le Forestier', 'San Francisco', 'song/1960-2000/Maxime Le Forestier - San Francisco.mp3', '0:00'),
(11, 'Michael Jackson', 'Smooth Criminal', 'song/1960-2000/Michael Jackson - Smooth Criminal.mp3', '0:14'),
(12, 'Mika', 'Grace Kelly', 'song/2000-2010/MIKA - Grace Kelly.mp3', '0:00'),
(13, 'Beyonce', 'Single Ladies', 'song/2000-2010/Beyoncé - Single Ladies (Put a Ring on It).mp3', '0:00'),
(14, 'Sean Paul', 'Get Busy', 'song/2000-2010/Sean Paul - Get Busy.mp3', '0:00'),
(15, '50 Cent', 'In Da Club', 'song/2000-2010/50 Cent - In Da Club.mp3', '0:00'),
(16, 'Daniel Powter', 'Bad day', 'song/2000-2010/Daniel Powter - Bad Day.mp3', '0:00'),
(17, 'James Blunt', 'Your Beautiful', 'song/2000-2010/James Blunt - You''re Beautiful.mp3', '0:00'),
(18, 'Rihanna', 'Unfaithful', 'song/2000-2010/Rihanna - Unfaithful.mp3', '0:50'),
(19, 'Arvil Lavigne', 'Girlfriend', 'song/2000-2010/Avril Lavigne - Girlfriend.mp3', '0:12'),
(20, 'Green Day', 'Boulevard of broken dreams', 'song/2000-2010/Green Day - Boulevard Of Broken Dreams.m4a', '0:26'),
(21, 'Red Hot Chili Pepper', 'Snow (Hey Oh)', 'song/2000-2010/Red Hot Chili Peppers - Snow ( Hey Oh ).m4a', '0:00'),
(22, 'Coldplay', 'Viva la vida', 'song/2000-2010/Coldplay - Viva La Vida.m4a', '0:00'),
(23, 'Flo Rida', 'Right round', 'song/2000-2010/Flo Rida - Right Round.m4a', '0:00'),
(24, 'Black Eyed Peace', 'I gotta feeling', 'song/2000-2010/The Black Eyed Peas - I Gotta Feeling.m4a', '0:00'),
(25, 'Train', 'Hey Soul Sister', 'song/2010-2014/Train - Hey Soul Sister.mp3', '0:00'),
(26, 'Justin Bieber', 'Baby', 'song/2010-2014/Justin Bieber - Baby.mp3', '0:00'),
(27, 'Bruno Mars', 'Just the way you are', 'song/2010-2014/Bruno Mars - Just The Way You Are.mp3', '0:16'),
(28, 'Far East Movemment', 'Like a G6', 'song/2010-2014/Far East Movement - Like A G6.mp3', '0:07'),
(29, 'Katy Perry', 'Firework', 'song/2010-2014/Katy Perry - Firework.mp3', '0:07'),
(30, 'Wiz Kalifha', 'Black And Yellow', 'song/2010-2014/Wiz Khalifa - Black And Yellow.mp3', '0:00'),
(31, 'Lady Gaga', 'Born this way ', 'song/2010-2014/Lady Gaga - Born This Way.mp3', '3:00'),
(32, 'LMFAO', 'Party Rock Anthem', 'song/2010-2014/LMFAO - Party Rock Anthem.mp3', '1:39'),
(33, 'Maroon 5', 'Move like jagger', 'song/2010-2014/Maroon 5 - Moves Like Jagger.mp3', '0:45'),
(34, 'Adele', 'Someone like you', 'song/2010-2014/Adele - Someone Like You.mp3', '0:00'),
(35, 'Snoop Dogg and Wiz Khalifa', 'Young, Wild and free', 'song/2010-2014/Young, Wild & Free - Wiz Khalifa feat. Snoop Dogg.mp3', '0:00'),
(36, 'Fun', 'We are young', 'song/2010-2014/Fun - We Are Young.mp3', '0:00'),
(37, 'Nicki Minaj', 'Starship', 'song/2010-2014/Nicki Minaj - Starships (Explicit).mp3', '0:40'),
(38, 'The lumineers', 'Ho Hey', 'song/2010-2014/The Lumineers - Ho Hey.mp3', '0:03');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
