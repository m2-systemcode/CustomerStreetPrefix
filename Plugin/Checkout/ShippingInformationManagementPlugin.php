<?php
/**
 * NOTICE OF LICENSE
 *
 * @category  SystemCode
 * @package   Systemcode_CustomerStreetPrefix
 * @author    Eduardo Diogo Dias <contato@systemcode.com.br>
 * @copyright System Code LTDA - ME
 * @license   http://opensource.org/licenses/osl-3.0.php
 */
declare(strict_types=1);

namespace SystemCode\CustomerStreetPrefix\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Api\Data\AddressInterface;
use SystemCode\CustomerStreetPrefix\Model\Quote\StreetPrefixApplier;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ShippingInformationManagementPlugin
{
    /**
     * Initialize dependencies.
     *
     * @param StreetPrefixApplier $streetPrefixApplier
     */
    public function __construct(
        private readonly StreetPrefixApplier $streetPrefixApplier
    ) {
    }

    /**
     * Execute before save address information.
     *
     * @param ShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return void
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): void {
        $shippingAddress = $addressInformation->getShippingAddress();

        if ($shippingAddress instanceof AddressInterface) {
            $this->streetPrefixApplier->prepare($shippingAddress);
        }

        $billingAddress = $addressInformation->getBillingAddress();

        if ($billingAddress instanceof AddressInterface) {
            $this->streetPrefixApplier->prepare($billingAddress);
        }
    }
}
