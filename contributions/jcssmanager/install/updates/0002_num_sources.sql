ALTER TABLE jcsscompressedfiles
  ADD COLUMN num_sources INTEGER UNSIGNED NOT NULL DEFAULT 1 AFTER hash;
  