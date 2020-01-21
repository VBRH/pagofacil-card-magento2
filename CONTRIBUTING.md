# How to Contribute

We'd love to accept your patches and contributions to this project. There are
just a few small guidelines you need to follow.

## Code reviews

All submissions, including submissions by project members, require review. We
use GitHub pull requests for this purpose. Consult
[GitHub Help] for more
information on using pull requests.

## Community Guidelines

Please consider read about some concepts like OOP, [AOP], MVC, Patterns design, DDD, TDD,
mutant Testing, PSR1, PSR12, etc.
 
This project has been provided with several tools for ensure the code quality:

* [PHP Code Sniffer]
* [PHP Mess Detector]
* [PHP CS Fixer]

```bash
vendor/bin/phpcbf --standard=PSR1 pagofacil/path/
vendor/bin/phpcbf --standard=PSR12 pagofacil/path/

vendor/bin/phpmd pagofacil/path/ xml codesize controversial design naming unusedcode --exclude=vendor/
vendor/bin/phpmd pagofacil/path/ xml codesize controversial design naming unusedcode --exclude=vendor/

vendor/bin/php-cs-fixer fix pagofacil/path/ --dry-run --diff
```

* [PHPMetrics]
* [PHPLoc]
* [PHPStan]
```bash
vendor/bin/phploc pagofacil/path 
vendor/bin/phpmetrics --report-html=report/metrics pagofacil/path
vendor/bin/phpstan analyse -l 7 pagofacil/path
```

* [PHPUnit]
```bash
vendor/bin/phpunit
```
This project follows [Google's Open Source Community Guidelines].

[PHP Code Sniffer]:https://github.com/squizlabs/PHP_CodeSniffer/wiki
[PHP Mess Detector]:https://phpmd.org/
[PHP CS Fixer]:https://github.com/FriendsOfPHP/PHP-CS-Fixer
[PHPMetrics]:https://phpmetrics.org/
[PHPLoc]:https://github.com/sebastianbergmann/phploc
[PHPStan]:https://github.com/phpstan/phpstan
[PHPUnit]:https://phpunit.readthedocs.io/es/latest/index.html
[AOP]:https://en.wikipedia.org/wiki/Aspect-oriented_programming
[GitHub Help]:https://help.github.com/articles/about-pull-requests/

[Google's Open Source Community Guidelines]:https://opensource.google.com/conduct/
