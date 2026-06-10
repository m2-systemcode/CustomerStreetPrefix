define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    function getConfig() {
        return window.checkoutConfig.customerStreetPrefix || {};
    }

    function isExistingCustomerAddress(address) {
        return !!address && !!address.customerAddressId;
    }

    function shouldCopyAttribute(attrCode, address) {
        if (attrCode !== 'street_prefix') {
            return true;
        }

        return getConfig().isActive && !isExistingCustomerAddress(address);
    }

    function copyCustomAttributesToExtensionAttributes(address) {
        if (!address || address.customAttributes === undefined) {
            return;
        }

        if (address.extension_attributes === undefined) {
            address.extension_attributes = {};
        }

        $.each(address.customAttributes, function (key, value) {
            var attrCode = value.attribute_code,
                attrValue = value.value;

            if (!attrCode || attrValue === undefined || attrValue === null || attrValue === '') {
                return;
            }

            if (!shouldCopyAttribute(attrCode, address)) {
                return;
            }

            address.extension_attributes[attrCode] = attrValue;
        });
    }

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            var config = getConfig(),
                billingAddress = quote.billingAddress();

            if (!config.isActive) {
                return originalAction(paymentData, messageContainer);
            }

            if (billingAddress) {
                copyCustomAttributesToExtensionAttributes(billingAddress);
            }

            return originalAction(paymentData, messageContainer);
        });
    };
});
