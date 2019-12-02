define([
    'Magento_Payment/js/view/payment/cc-form',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Payment/js/model/credit-card-validation/validator'
], function (Component, $, quote, customer) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Pagofacil_Card/payment/card-form'
        },
        context: function () {
            return this;
        },
        getCode: function () {
            console.log(this.isPlaceOrderActionAllowed());
            return 'pagofacil_card';
        },
        isActive: function () {
            return true;
        },
        getData: function () {
            let data = {
                'method': this.getCode()
            };

            return data;
        },
        preparePayment: function () {
            let $form = $('#' + this.getCode() + '-form');

            if ($form.validation() && $form.validation('isValid')) {
            } else {
                return $form.validation() && $form.validation('isValid');
            }
        }
    })
});