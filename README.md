![pagofacil_logo](https://pagofacil.net/images/logo_pagofacil@2x.png)

PagoFacil.net
=============
## Magento Payment gateway extension.

## PagoFácil Online

### How to work?

Practical solution that offers comfort and confidentiality. In just a few simple steps you can start charging

#### step 1
Your customer makes his order
#### step 2
Your customer enter the credit card details
#### step 3
Confirm the order and purchase information
#### step 4
You and your clustomer receive confirmation of the transaction

## Install

```sh
composer require pagofacilnet/module-payment

bin/magento module:enable Pagofacil_Card --clear-static-content
bin/magento setup:upgrade
bin/magento setup:di:compile

```

### Update
```sh
composer clear-cache
composer update pagofacilnet/card-magento2
bin/magento setup:upgrade
bin/magento setup:di:compile
```

## Documentation
- Read our [documentation] to learn how to use Pago Fácil solutions
- [Blog]

Read our 

## Contributors
PagoFacil.net team

[How to contribute](CONTRIBUTING.md)

## License
[Open Source License](LICENSE.txt)


## [Change log](CHANGELOG.md)
## [Code metrics](METRICS.md)

[documentation]:<https://pagofacil.net/desarrolladores>
[Blog]:<https://blog.pagofacil.net/>
