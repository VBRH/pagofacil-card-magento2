define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'pagofacil_card',
                component: 'Pagofacil_Card/js/view/payment/method-renderer/cc-form'
            },
            {
                type: 'pagofacil_cash',
                component: 'Pagofacil_Card/js/view/payment/method-renderer/cash-form'
            }
        );

        return Component.extend({});
    }
);