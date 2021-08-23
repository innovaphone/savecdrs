The script cdr.php takes CDRs via HTTP GET and stores them in following files in the subfolder 'data':
 - data/cdr_pbx.txt - PBX CDRs in text file
 - data/cdr_gw.txt - Gateway CDRs in text file
 - data/db_pbx.sq3 - PBX CDRs in SQLite DB
 - data/db_gw.sq3 - Gateway CDRs in SQLite DB

Make sure subfolder "data" exists and is writable.

The script will create an SQLite DB connection using PDO abstraction layer.

Make sure folowing extensions are enabled in php.ini:
extension=php_pdo.dll
extension=php_pdo_sqlite.dll

You can easily switch to another DB like Postgres using PDO.

To check the contents of the DB browse files show_pbx_cdrs.php and show_gw_cdrs.php.