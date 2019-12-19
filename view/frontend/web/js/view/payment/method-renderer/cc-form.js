define([
    'Magento_Payment/js/view/payment/cc-form',
    'jquery',
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Pagofacil_Card/payment/card-form'
        },
        context: function(){
            return this;
        },
        getCode: function () {
            return 'pagofacil_card';
        },
        isActive: function () {
            return true;
        }
    })
});