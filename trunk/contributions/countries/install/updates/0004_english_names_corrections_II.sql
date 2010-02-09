-- Correct misspellings
UPDATE countries SET name = 'Bahamas' WHERE id = 'BS';
UPDATE countries SET name = 'Trinida and Tobago' WHERE id = 'TT';

UPDATE countries SET codenum = 832 WHERE id = 'JE';

-- Add missing
INSERT INTO countries (id, id_continent, code3, codenum, name) VALUES
	('GG', 'EU', 'GGY', 831, 'Guernsey');
	
INSERT INTO countriestranslations (id_country, lang, name) VALUES 
	('GG', 'de', 'Guernsey');
	
INSERT INTO countries2countriesgroups (id_group, id_country) VALUES 
	(3, 'GG');
	
