<?php
/**
 * @defgroup SystemUpdate
 * @ingroup Modules
 * 
 * Executes updates and installs new modules.
 * 
 * @section Usage
 * 
 * Systemupdate executes PHP and SQL scripts in folders app/install and app/install/updates.
 * 
 * If a module is enabled the first time or an app is new, Ssystemupdate will look for files name
 * 
 * - app/install/install.sql and
 * - app/install/install.php
 * 
 * Later must provide a function called "[module name|app]_install", e.g. "app_install" or "coolmodule_install".
 * 
 * Later updates are read from folder app/install/updates. Update files can be either SQL or PHP files and must be named
 * 
 * [VERSION]_explaination.[EXTENSION]
 * 
 * Version must boil down to a positive integer, may contain leading zeros. Valid filenames would be:
 * 
 * - 0001_keys_added_to_post_table.sql
 * - 2_convert_content_row.php
 * 
 * PHP files must contain a function called "[module name|app]_update_[version]", where version is version of file, but
 * may not contain leading zeros.
 */
