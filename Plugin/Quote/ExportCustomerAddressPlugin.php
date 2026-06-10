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

use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Quote\Model\Quote\Address;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Api\StreetPrefixResolver;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ExportCustomerAddressPlugin
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
     * Execute after export customer address.
     *
     * @param Address $subject
     * @param CustomerAddressInterface $result
     * @return CustomerAddressInterface
     */
    public function afterExportCustomerAddress(
        Address $subject,
        CustomerAddressInterface $result
    ): CustomerAddressInterface {
        if (!$this->config->isActive()) {
            return $result;
        }

        $streetPrefix = $this->streetPrefixResolver->resolveFromEntity($subject);

        if ($streetPrefix === null) {
            return $result;
        }

        $result->setCustomAttribute('street_prefix', $streetPrefix);

        return $result;
    }
}
