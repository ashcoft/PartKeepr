# Functional / Unit Testing


## Prerequisites

In order to run functional / unit tests, you first need to define the following environment variable to specify
which test database you are using:

`export SYMFONY__TESTDB=<databasetype>`

The following databases are pre-configured in `config_test.yml`:

- `mysql`
- `pgsql`
- `sqlite`

Note that the database connections are configured for travis testing; you might need to change the database name,
user and password properties.

## Running the tests
 
The unit tests are ran by `phpunit -c app/`. This also generates a coverage report in the directory
`reports/coverage`.

If you need to run individual tests, you can do so by using the following command:

`phpunit -c app/ <yourtestcase>`

Full example:

`phpunit -c app/ src/PartKeepr/AuthBundle/Tests/Controller/DefaultControllerTest`

## Continuous Integration

`ci/github-actions.yml` (to be moved to `.github/workflows/ci.yml`, see
`ci/README.md`) runs on every pull request and on every push to `master`:

| Job | Description |
| --- | --- |
| `composer.json / composer.lock` | Validates `composer.json` and fails when `composer.lock` is not in sync with it. |
| `PHP syntax` | Runs `php -l` over every file in `app/` and `src/`. |
| `PHPUnit (PHP 7.1, sqlite/mysql/pgsql)` | Installs the dependencies, creates the test schema and runs the test suite against all three databases. The coverage report is uploaded as the `coverage-clover` artifact. |

The jobs use PHP 7.1 (the version of `docker/base-dev/Dockerfile`) and select the test database through the
`SYMFONY__TESTDB` environment variable described above. The sqlite job doesn't need any database server, while the
mysql and pgsql jobs use the servers which are pre-installed on the GitHub runner.

### Dependency-free unit tests

Unit tests which neither boot the kernel nor need a database should extend `PHPUnit_Framework_TestCase` (instead of
`PartKeepr\CoreBundle\Tests\WebTestCase`) and must not use the container. Examples are
`PartKeepr\CoreBundle\Tests\Services\SystemServiceTest` and
`PartKeepr\ImportBundle\Tests\Configuration\FieldConfigurationTest`.

They can be executed independently of any database, for example:

`vendor/bin/phpunit -c app/ src/PartKeepr/CoreBundle/Tests/Services/SystemServiceTest.php`
