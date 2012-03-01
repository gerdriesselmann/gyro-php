UPDATE countriestranslations t, countries c SET t.capital = c.capital WHERE t.id_country = c.id;

UPDATE countriestranslations SET capital = 'Wien' WHERE id_country = 'AT' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Brüssel' WHERE id_country = 'BE' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Bern' WHERE id_country = 'CH' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Peking' WHERE id_country = 'CN' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Prag' WHERE id_country = 'CZ' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Kopenhagen' WHERE id_country = 'DK' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Kairo' WHERE id_country = 'EG' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Athen' WHERE id_country = 'GR' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Neu Delhi' WHERE id_country = 'IN' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Bagdad' WHERE id_country = 'IQ' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Teheran' WHERE id_country = 'IR' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Rom' WHERE id_country = 'IT' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Luxemburg' WHERE id_country = 'LU' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Lissabon' WHERE id_country = 'PT' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Moskau' WHERE id_country = 'RU' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Singapur' WHERE id_country = 'SG' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Vatikan' WHERE id_country = 'VA' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Priština' WHERE id_country = 'XK' AND lang = 'de';
UPDATE countriestranslations SET capital = 'Sana‘a' WHERE id_country = 'YE' AND lang = 'de';

UPDATE countries SET capital = 'Sana‘a' WHERE id = 'YE';
UPDATE countries SET capital = 'Priština' WHERE id = 'XK';

