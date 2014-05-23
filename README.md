# Zeptech database abstraction layer

Install via [Composer](http://getcomposer.org/): zeptech/database

## Running the tests
Most tests can be run without additional configuration however some will be
skipped. In order to run the tests against databases other than SQLite you will
need to provide connection information in `test/db.cfg.xml`:

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<phpunit>
	<php>
		<var name="MYSQL_USER" value="<mysql-user>" />
		<var name="MYSQL_PASS" value="<mysql-pass>" />
		<var name="PGSQL_USER" value="<pgsql-user>" />
		<var name="PGSQL_PASS" value="<pgsql-pass>" />
	</php>
</phpunit>
```

Then invoke the test runner using `phpunit --configuration test/db.cfg.xml
test/`

The above configuration will still result in some skipped tests. This is because
the tests define two levels of required authorization. The base level simply
requires a user that is able to connect to the database server but that doesn't
necessarily have any priviledges beyond that. The second level requires a user
with full priviledges to two databases named `phpunit_db` and `phpunit_db_cp`.
The user should be specified using the variables `MYSQL_PRIV_USER` and
`MYSQL_PRIV_PASS`:

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<phpunit>
	<php>
		<var name="MYSQL_USER" value="<mysql-user>" />
		<var name="MYSQL_PASS" value="<mysql-pass>" />
		<var name="MYSQL_PRIV_USER" value="<mysql-priv-user>" />
		<var name="MYSQL_PRIV_PASS" value="<mysql-priv-pass>" />
		<var name="PGSQL_USER" value="<pgsql-user>" />
		<var name="PGSQL_PASS" value="<pgsql-pass>" />
	</php>
</phpunit>
```
