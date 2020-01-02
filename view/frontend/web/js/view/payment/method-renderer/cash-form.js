define(
    ['Magento_Checkout/js/view/payment/default'],
    function (Component, $) {
        'use strict';
        return Component.extend(
            {
                defaults: {
                    template: 'Pagofacil_Card/payment/cash-form'
                },
                getCode: function () {
                    return 'pagofacil_cash';
                },
                isActive: function () {
                    return true;
                }
            }
        );
    }
);