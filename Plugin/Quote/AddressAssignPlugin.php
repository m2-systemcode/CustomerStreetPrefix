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

namespace SystemCode\CustomerStreetPrefix\Plugin\Quote;

use Magento\Quote\Api\Data\AddressInterface;
use SystemCode\CustomerStreetPrefix\Model\Quote\StreetPrefixApplier;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AddressAssignPlugin
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
     * Execute before assign.
     *
     * @param mixed $subject
     * @param mixed $cartId
     * @param AddressInterface $address
     * @param mixed $useForShipping
     * @return void
     */
    public function beforeAssign(
        $subject,
        $cartId,
        AddressInterface $address,
        $useForShipping = false
    ): void {
        $this->streetPrefixApplier->prepare($address);
    }
}
