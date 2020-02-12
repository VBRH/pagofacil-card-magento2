 Magento Payment gateway extension.
 =======
![pagofacil_logo](https://pagofacil.net/images/logo_pagofacil@2x.png)
## PagoFácil Online
### How to work?
Practical solution that offers comfort and confidentiality. In just a few simple steps you can start charging
- step 1
    * Your customer makes his order
- step 2
    * Your customer enter the credit card details
- step 3
    * Confirm the order and purchase information
- step 4
    * You and your clustomer receive confirmation of the transaction

## Install
Remember the basic known about
```bash
usermod -a -G <web server group name> <support user>
chown -R <web server user name>:<web server group name> /path/of/your/magento
find . -type d -exec chmod 775 {} + && find . -type f -exec chmod 664 {} + && chmod ug+x bin/magento

composer require pagofacilnet/module-payment

bin/magento module:enable Pagofacil_Card --clear-static-content
bin/magento setup:upgrade
bin/magento setup:di:compile
```
If you need more information about how to install, please, read the [Magento installation flow]
### Update
```bash
composer clear-cache
composer update pagofacilnet/card-magento2
bin/magento setup:upgrade
bin/magento setup:di:compile
```
## Documentation
- Read our [documentation] to learn how to use Pago Fácil solutions
- [Blog]
- [How to contribute](CONTRIBUTING.md)
- [Code of conduct](CODE_OF_CONDUCT.md)
- [Change log](CHANGELOG.md)
- [Code metrics](METRICS.md)

## Contributors
PagoFacil.net team
## License
[Open Source License](LICENSE.txt)

[documentation]:<https://pagofacil.net/desarrolladores>
[Blog]:<https://blog.pagofacil.net/>
[Magento installation flow]:<https://devdocs.magento.com/guides/v2.3/install-gde/install-flow-diagram.html>
