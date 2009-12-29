UPDATE countriestranslations SET name = 'Aserbaidschan' WHERE lang = 'de' AND id_country = 'AZ';
UPDATE countriestranslations SET name = 'Bermuda' WHERE lang = 'de' AND id_country = 'BM';
UPDATE countriestranslations SET name = 'Brunei Darussalam' WHERE lang = 'de' AND id_country = 'BN';
UPDATE countriestranslations SET name = 'Libyen' WHERE lang = 'de' AND id_country = 'LY';
UPDATE countriestranslations SET name = 'Marshallinseln' WHERE lang = 'de' AND id_country = 'MH';
UPDATE countriestranslations SET name = 'Polen' WHERE lang = 'de' AND id_country = 'PL';
UPDATE countriestranslations SET name = 'St. Kitts und Nevis' WHERE lang = 'de' AND id_country = 'KN';
UPDATE countriestranslations SET name = 'Vereinigte Staten (USA)' WHERE lang = 'de' AND id_country = 'US';
UPDATE countriestranslations SET name = 'Belarus' WHERE lang = 'de' AND id_country = 'BY';
UPDATE countriestranslations SET name = 'Großbritannien' WHERE lang = 'de' AND id_country = 'GB';

UPDATE countriestranslations SET name = 'Amerikanisch-Ozeanien' WHERE lang = 'de' AND id_country = 'UM';
UPDATE countriestranslations SET name = 'Amerikanisch-Samoa' WHERE lang = 'de' AND id_country = 'AS';
UPDATE countriestranslations SET name = 'Antarktis' WHERE lang = 'de' AND id_country = 'AQ';
UPDATE countriestranslations SET name = 'Bouvetinsel' WHERE lang = 'de' AND id_country = 'BV';
UPDATE countriestranslations SET name = 'Britisches Territorium im Indischen Ozean' WHERE lang = 'de' AND id_country = 'IO';
UPDATE countriestranslations SET name = 'Cookinseln' WHERE lang = 'de' AND id_country = 'CK';
UPDATE countriestranslations SET name = 'Französisch-Guayana' WHERE lang = 'de' AND id_country = 'GF';
UPDATE countriestranslations SET name = 'Französisch-Polynesien' WHERE lang = 'de' AND id_country = 'PF';
UPDATE countriestranslations SET name = 'Französische Süd- und Arktisgebiete' WHERE lang = 'de' AND id_country = 'TF';
UPDATE countriestranslations SET name = 'Heard und McDonaldinseln' WHERE lang = 'de' AND id_country = 'HM';
UPDATE countriestranslations SET name = 'Kaimaninseln' WHERE lang = 'de' AND id_country = 'KY';
UPDATE countriestranslations SET name = 'Kokosinseln' WHERE lang = 'de' AND id_country = 'CC';
UPDATE countriestranslations SET name = 'Nördliche Marianen' WHERE lang = 'de' AND id_country = 'MP';
UPDATE countriestranslations SET name = 'Norfolkinsel' WHERE lang = 'de' AND id_country = 'NF';
UPDATE countriestranslations SET name = 'Palästinensische Gebiete' WHERE lang = 'de' AND id_country = 'PS';
UPDATE countriestranslations SET name = 'Pitcairninseln' WHERE lang = 'de' AND id_country = 'PN';
UPDATE countriestranslations SET name = 'Südgeorgien und die Südlichen Sandwichinseln' WHERE lang = 'de' AND id_country = 'GS';
UPDATE countriestranslations SET name = 'Turks- und Caicosinseln' WHERE lang = 'de' AND id_country = 'TC';
UPDATE countriestranslations SET name = 'Weihnachtsinsel' WHERE lang = 'de' AND id_country = 'CX';
UPDATE countriestranslations SET name = 'Åland' WHERE lang = 'de' AND id_country = 'AX';
UPDATE countriestranslations SET name = 'Macao' WHERE lang = 'de' AND id_country = 'MO';
UPDATE countriestranslations SET name = 'St. Helena' WHERE lang = 'de' AND id_country = 'SH';
UPDATE countriestranslations SET name = 'St. Pierre und Miquelon' WHERE lang = 'de' AND id_country = 'PM';



INSERT IGNORE INTO countriesgroups (id, name, abbrevation, type) VALUES 
	(2, 'Sovereign States', NULL, 'POLITICAL'),
	(3, 'Dependend Territories', NULL, 'POLITICAL');

INSERT IGNORE INTO countries2countriesgroups (id_group, id_country) VALUES
	(3, 'UM'), 	(3, 'AS'),
	(3, 'VI'), 	(3, 'AI'),
	(3, 'AQ'),	(3, 'AW'),
	(3, 'BM'),	(3, 'BV'),
	(3, 'VG'),	(3, 'IO'),
	(3, 'CK'),	(3, 'FK'),
	(3, 'FO'),	(3, 'GF'),
	(3, 'PF'),	(3, 'TF'),
	(3, 'GI'),	(3, 'GL'),
	(3, 'GP'),	(3, 'GU'),
	(3, 'HM'),	(3, 'KY'),
	(3, 'CC'),	(3, 'MQ'),
	(3, 'YT'),	(3, 'MS'),
	(3, 'NC'),	(3, 'AN'),
	(3, 'NU'),	(3, 'MP'),
	(3, 'NF'),	(3, 'PS'),
	(3, 'PN'),	(3, 'PR'),
	(3, 'RE'),	(3, 'SH'),
	(3, 'PM'),	(3, 'GS'),
	(3, 'SJ'),	(3, 'TW'),
	(3, 'TK'),	(3, 'TC'),
	(3, 'WF'),  (3, 'CX'),
	(3, 'EH'),  (3, 'AX'),
	(3, 'HK'),  (3, 'MO');

INSERT IGNORE INTO countries2countriesgroups (id_group, id_country) VALUES (1, 'GB');
	
INSERT IGNORE INTO countries2countriesgroups (id_group, id_country) 
	SELECT 2, id  FROM countries WHERE id NOT IN (SELECT id_country FROM countries2countriesgroups WHERE id_group = 3);
