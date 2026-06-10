var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'SystemCode_CustomerStreetPrefix/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'SystemCode_CustomerStreetPrefix/js/action/place-order-mixin': true
            }
        }
    }
};
