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

namespace SystemCode\CustomerStreetPrefix\Plugin\Api;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;
use Magento\Framework\Api\ExtensionAttributesFactory;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Api\StreetPrefixResolver;

class CustomerAddressExtensionPlugin
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param StreetPrefixResolver $streetPrefixResolver
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly StreetPrefixResolver $streetPrefixResolver,
        private readonly ExtensionAttributesFactory $extensionAttributesFactory
    ) {
    }

    /**
     * Execute after get data model.
     *
     * @param Address $subject
     * @param AddressInterface $result
     * @return AddressInterface
     */
    public function afterGetDataModel(Address $subject, AddressInterface $result): AddressInterface
    {
        if (!$this->config->isActive()) {
            return $result;
        }

        $streetPrefix = $this->streetPrefixResolver->resolve($subject);

        if ($streetPrefix === null) {
            return $result;
        }

        $extensionAttributes = $result->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(
                AddressInterface::class
            );
        }

        $extensionAttributes->setStreetPrefix($streetPrefix);
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
