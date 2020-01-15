define([
    'Magento_Payment/js/view/payment/cc-form',
    'jquery',
], function (Component, $) {
    'use strict';

    let total = window.checkoutConfig.payment.total;

    $(document).on('change', '#pagofacil_monthly_installments', function () {
        let monthly_payment = 0;
        let months = parseInt($(this).val());

        if (isNaN(total)) {
            months = 1;
        }

        switch (true) {
            case months > 1:
                monthly_payment = (total/months).toFixed(2);
                $('#total-monthly').css('display', 'inline');
                break;
            case months == 1:
                $('#total-monthly').css('display', 'none');
                break;
        }
        $("#monthly-payment").text(monthly_payment);
    });

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
        },
        getData: function () {
            return {
                'method': this.getCode(),
                'additional_data': {
                    'cc_cid': this.creditCardVerificationNumber(),
                    'cc_type': this.creditCardType(),
                    'cc_exp_year': this.creditCardExpYear(),
                    'cc_exp_month': this.creditCardExpMonthUpdate(),
                    'cc_number': this.creditCardNumber(),
                    'billin-address-municipality': this.getMunicipality(),
                    'monthly-installments': this.getMonthlyInstallmentSelect(),
                    'billing-address-suburb': this.getSuburb()
                }
            };
        },
        getMunicipality: function () {
            return document.querySelector('#pf-municipality').value;
        },
        getMonthlyInstallmentSelect: function () {
            let monthly = document.querySelector('#pagofacil_monthly_installments').value;

            if (1 === monthly.toString().length) {
                monthly = '0' +monthly;
            }

            return monthly;
        },
        getSuburb: function () {
            return document.querySelector('#pf-suburb').value;
        },
        creditCardExpMonthUpdate: function () {
            let expiration = this.creditCardExpMonth();

            if (undefined === expiration) {
                return expiration;
            }

            if (1 === expiration.toString().length) {
                expiration = '0'+ expiration;
            }

            return expiration;
        }
    })
});