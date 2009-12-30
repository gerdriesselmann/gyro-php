-- Correct misspellings
UPDATE countries SET name = 'Cook Islands' WHERE id = 'CK';
UPDATE countries SET name = 'Montserrat' WHERE id = 'MS';
UPDATE countries SET name = 'Saint Helena' WHERE id = 'SH';
UPDATE countries SET name = 'Saint Kitts and Nevis' WHERE id = 'KN';
UPDATE countries SET name = 'Saint Lucia' WHERE id = 'LC';
UPDATE countries SET name = 'Saint Pierre and Miquelon' WHERE id = 'PM';
UPDATE countries SET name = 'Saint Vincent and the Grenadines' WHERE id = 'VC';
UPDATE countries SET name = 'Sao Tome and Principe' WHERE id = 'ST';
UPDATE countries SET name = 'Svalbard and Jan Mayen' WHERE id = 'SJ';
UPDATE countries SET name = 'Trinida and Tobago' WHERE id = 'TT';
UPDATE countries SET name = 'Turks and Caicos Islands' WHERE id = 'TC';
UPDATE countries SET name = 'Wallis and Futuna' WHERE id = 'WF';
UPDATE countries SET name = 'Virgin Islands, British' WHERE id = 'VG';
UPDATE countries SET name = 'Virgin Islands, U.S.' WHERE id = 'VI';
UPDATE countries SET name = 'Antigua and Barbuda' WHERE id = 'AG';

UPDATE countries SET name = replace(name, '&', 'and');

-- Add missing
INSERT INTO countries (id, id_continent, code3, codenum, name) VALUES
	('IM', 'EU', 'IMN', 833, 'Isle of Man'),
	('JE', 'EU', 'JEY', 831, 'Jersey'),
	('BL', 'NA', 'BLM', 652, 'Saint Barthélemy'),
	('MF', 'NA', 'MAF', 663, 'Saint Martin');
		
	
INSERT INTO countriestranslations (id_country, lang, name) VALUES 
	('IM', 'de', 'Isle of Man'),
	('JE', 'de', 'Jersey'),	
	('BL', 'de', 'St. Barthélemy'),
	('MF', 'de', 'St. Martin');
	
INSERT INTO countries2countriesgroups (id_group, id_country) VALUES 
	(3, 'IM'), (3, 'JE'), (3, 'BL'), (3, 'MF');
	
