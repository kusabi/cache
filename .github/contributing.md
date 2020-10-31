## Coding Standards

This library follows [PSR-1](https://www.php-fig.org/psr/psr-1/) & [PSR-2](https://www.php-fig.org/psr/psr-2/) standards.

## Unit Tests

Before pushing changes ensure you run the unit tests.

`vendor/bin/phpunit --testsuite unit`


## Integration tests

A dockerfile is provided to test the integration of this library with actual services such as redis.

If you have docker installed, then run the following commands.

```bash
docker build -t cache .
docker run -it cache vendor/bin/phpunit
```

or combine them as

```bash
docker build -t cache . && docker run -it cache vendor/bin/phpunit
```

## Code sniffer and validator

Please remember to run the following commands

```bash
vendor/bin/php-cs-fixer fix
vendor/bin/phan
```