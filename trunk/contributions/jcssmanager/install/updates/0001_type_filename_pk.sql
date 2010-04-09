ALTER TABLE `jcsscompressedfiles` 
 DROP PRIMARY KEY,
 ADD PRIMARY KEY  USING BTREE(`type`, `filename`);
 
