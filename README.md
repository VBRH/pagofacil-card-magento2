PagoFacil.net
=============

## Magento Payment gateway extension.

### Install

```sh
composer config repositories.pagofacil git https://github.com/angelbarrientos/pagofacil-card-magento2.git
composer requiere pagofacilnet/card-magento2 dev-develop

bin/magento module:enable Pagofacil_Card
bin/magento setup:upgrade
bin/magento cache:clean
```

### Update
```sh
composer update pagofacilnet/card-magento2
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

## Synopsis
An extension to add integration with Payment Gateway.

## Contributors
PagoFacil.net team

## License
[Open Source License](LICENSE.txt)
