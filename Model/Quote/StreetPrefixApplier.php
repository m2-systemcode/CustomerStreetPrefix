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

namespace SystemCode\CustomerStreetPrefix\Model\Quote;

use Magento\Quote\Api\Data\AddressInterface;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Api\StreetPrefixResolver;

class StreetPrefixApplier
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param StreetPrefixResolver $streetPrefixResolver
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly StreetPrefixResolver $streetPrefixResolver
    ) {
    }

    /**
     * Handle prepare.
     *
     * @param AddressInterface $address
     * @return void
     */
    public function prepare(AddressInterface $address): void
    {
        if (!$this->config->isActive() || $this->isExistingCustomerAddress($address)) {
            return;
        }

        $streetPrefix = $this->streetPrefixResolver->resolve($address);

        if ($streetPrefix === null) {
            return;
        }

        $address->setStreetPrefix($streetPrefix);
    }

    /**
     * Check whether existing customer address.
     *
     * @param AddressInterface $address
     * @return bool
     */
    private function isExistingCustomerAddress(AddressInterface $address): bool
    {
        return (int) $address->getCustomerAddressId() > 0;
    }
}
