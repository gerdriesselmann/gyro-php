ALTER TABLE `continents`
 ADD COLUMN `lat1` FLOAT AFTER `name`,
 ADD COLUMN `lon1` FLOAT AFTER `lat1`,
 ADD COLUMN `lat2` FLOAT AFTER `lon1`,
 ADD COLUMN `lon2` FLOAT AFTER `lat2`;

UPDATE continents SET lat1=37.56712, lon1=63.525379, lat2=-46.900452, lon2=-25.35874 WHERE id='AF';
UPDATE continents SET lat1=81.008797, lon1=39.869301, lat2=27.636311, lon2=-31.266001 WHERE id='EU';
UPDATE continents SET lat1=13.39029, lon1=-26.33247, lat2=-59.450451, lon2=-109.47493 WHERE id='SA';
UPDATE continents SET lat1=-53.00774, lon1=180, lat2=-90, lon2=-180 WHERE id='AN';
UPDATE continents SET lat1=82.50045, lon1=180, lat2=-12.56111, lon2=19.6381 WHERE id='AS';
UPDATE continents SET lat1=83.162102, lon1=-52.23304, lat2=5.49955, lon2=-167.276413 WHERE id='NA';
UPDATE continents SET lat1=-6.06945, lon1=-175.292496, lat2=-53.05872, lon2=105.377037 WHERE id='OC';
