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
