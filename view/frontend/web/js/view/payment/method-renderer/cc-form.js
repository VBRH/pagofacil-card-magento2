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
        },
        getMonthlyInstallments: function () {
            let arrayData = [];
            for (let index in window.checkoutConfig.payment.months_installments) {
                arrayData[index] = window.checkoutConfig.payment.months_installments[index];
            }

            arrayData.sort(function (a, b) {
                return a - b;
            });

            return arrayData;
        }
    })
});