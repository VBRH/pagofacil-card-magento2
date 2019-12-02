define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        console.log('algo esta pasando por aquí');
        rendererList.push(
            {
                type: 'pagofacil_card',
                component: 'Pagofacil_Card/js/view/payment/method-renderer/cc-form'
            }
        );

        return Component.extend({});
    }
);