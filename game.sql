SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `game` (
  `timestamp` int(15) NOT NULL,
  `player` varchar(20) NOT NULL,
  `info` varchar(255) NOT NULL,
  UNIQUE KEY `player` (`player`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `game` (`timestamp`, `player`, `info`) VALUES
(1298692994, 'Andrew', '1.237832397463716,3.6569356797676953,1.5495559215387154,0.010000000000000009,1,0,0,0,1');
