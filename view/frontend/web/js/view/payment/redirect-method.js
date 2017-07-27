/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component, url) {
        'use strict';
        return Component.extend({
            redirectUrl: window.checkoutConfig.defaultSuccessPageUrl,
            defaults: {
                template: 'Mygento_Payment/payment/method'
            },

            /**
             * After place order callback
             */
            afterPlaceOrder: function () {
                var self = this;
                self.redirectAfterPlaceOrder = false;
                window.location.replace(url.build(this.redirectUrl));
            }
        });
    }
);