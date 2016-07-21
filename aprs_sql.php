CREATE TABLE aprscalls (
  aprscalls_ID int(10) unsigned NOT NULL AUTO_INCREMENT,
  aprscallsCallsign char(20) DEFAULT NULL,
  aprscallsActive bit(1) NOT NULL DEFAULT b'0',
  aprscallsComment tinytext,
  aprscallsWX bit(1) NOT NULL DEFAULT b'0',
  aprscallsMenu bit(1) NOT NULL DEFAULT b'0',
  aprscallsWildcard bit(1) NOT NULL DEFAULT b'0',
  aprscallsLastReport int(10) unsigned zerofill DEFAULT '0000000000',
  aprscallsLastUpdate int(11) unsigned NOT NULL DEFAULT '0',
  aprscallsLastEdit timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (aprscalls_ID)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8_unicode_ci COMMENT='List of callsigns to monitor';
CREATE TABLE aprsmsg (
  CallsignSSID varchar(9) NOT NULL,
  CallsignTo varchar(9) NOT NULL,
  ReportTime datetime NOT NULL,
  Message text,
  Packet text,
  wab char(4) DEFAULT NULL,
  iaru char(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8_unicode_ci COMMENT='Messages received from monitored callsigns';
CREATE TABLE aprspackets (
  CallsignSSID varchar(9) NOT NULL,
  ReportTime datetime NOT NULL,
  PacketType tinyint(4) unsigned DEFAULT '0',
  IsWx tinyint(4) unsigned DEFAULT '0',
  Packet text,
  wab char(4) DEFAULT NULL,
  iaru char(6) DEFAULT NULL,
  KEY IX_APRSPackets_RT (ReportTime),
  KEY IX_APRSPackets (CallsignSSID,ReportTime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8_unicode_ci COMMENT='Raw packet data for each callsign';
CREATE TABLE aprsprefs (
  aprsprefs_id tinyint(3) unsigned NOT NULL DEFAULT '1',
  aprsprefs_host char(50) DEFAULT NULL,
  aprsprefs_mycall char(50) DEFAULT NULL,
  aprsprefs_client char(50) DEFAULT NULL,
  aprsprefs_lastedit timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (aprsprefs_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8_unicode_ci COMMENT='Preferences for server module';
CREATE TABLE aprstrack (
  CallsignSSID varchar(9) NOT NULL,
  ReportTime datetime NOT NULL,
  comment text NOT NULL,
  Latitude decimal(10,4) NOT NULL,
  Longitude decimal(10,4) NOT NULL,
  Icon char(2) DEFAULT NULL,
  Course smallint(6) DEFAULT NULL,
  Speed int(11) DEFAULT NULL,
  Altitude int(11) DEFAULT NULL,
  wab char(4) DEFAULT NULL,
  iaru char(6) DEFAULT NULL,
  KEY IX_APRSTrack_RT (ReportTime),
  KEY IX_APRSTrack (CallsignSSID,ReportTime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8_unicode_ci COMMENT='Tracking information for each call';
CREATE TABLE aprswx (
  CallsignSSID varchar(9) NOT NULL,
  ReportTime datetime NOT NULL,
  WindDir smallint(6) DEFAULT NULL,
  WindSpeed smallint(6) DEFAULT NULL,
  GustSpeed smallint(6) DEFAULT NULL,
  Temperature smallint(6) DEFAULT NULL,
  HourRain decimal(4,2) DEFAULT NULL,
  DayRain decimal(6,2) DEFAULT NULL,
  MidnightRain decimal(6,2) DEFAULT NULL,
  Humidity tinyint(4) DEFAULT NULL,
  BarPressure decimal(5,1) DEFAULT NULL,
  KEY IX_APRSWx_RT (ReportTime),
  KEY IX_APRSWx (CallsignSSID,ReportTime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8_unicode_ci COMMENT='Weather info from each call';
