INSERT INTO countriesgroups (id, name, abbrevation, type) VALUES (
    4, 'European Economic Area', 'EEA', 'POLITICAL'
);

/* Copy all countries from EU to EEA */
INSERT INTO countries2countriesgroups (id_country, id_group)
    SELECT id_country, 4 FROM countries2countriesgroups WHERE id_group = 1;

/* Add Iceland, Liechtenstein and Norway - and also post-brexit GB for now */
INSERT INTO countries2countriesgroups (id_country, id_group) VALUES
    ('IS', 4),
    ('LI', 4),
    ('NO', 4),
    ('GB', 4)
;

