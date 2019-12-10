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
            return 'pagofacil_card';
        },
        isActive: function () {
            return true;
        },
        getData: function () {
            let data = {
                'method': this.getCode(),
                'additional_data': {
                    'cc_cid': this.creditCardVerificationNumber(),
                    'cc_type': this.creditCardType(),
                    'cc_exp_year': this.creditCardExpYear(),
                    'cc_exp_month': this.creditCardExpMonth(),
                    'cc_number': this.creditCardNumber(),
                }
            };

            return data;
        },
        preparePayment: function () {
            let $form = $('#' + this.getCode() + '-form');

            return $form.validation() && $form.validation('isValid');
        },
        validateAddress: function () {
            let customerData = quote.billingAddress._latestValue;
            let address = undefined;

            switch (true) {
                case typeof customerData.city === 'undefined' || customerData.city.length === 0:
                    return false;
                case typeof customerData.countryId === 'undefined' || customerData.countryId.length === 0:
                    return false;
                case typeof customerData.postcode === 'undefined' || customerData.postcode.length === 0:
                    return false;
                case typeof customerData.street === 'undefined' || customerData.street[0].length === 0:
                    return false;
                case typeof customerData.region === 'undefined' || customerData.region.length === 0:
                    return false;
            }

            address = {
                city: customerData.city,
                country_code: customerData.countryId,
                postal_code: customerData.postcode,
                state: customerData.region,
                line1: customerData.street[0],
                line2: customerData.street[1]
            };

            return address;
        }
    })
});