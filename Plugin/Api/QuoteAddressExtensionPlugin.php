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

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Quote\Api\Data\AddressExtensionInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Api\StreetPrefixResolver;

class QuoteAddressExtensionPlugin
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
     * Execute after get extension attributes.
     *
     * @param Address $subject
     * @param AddressExtensionInterface|null $extensionAttributes
     * @return AddressExtensionInterface|null
     */
    public function afterGetExtensionAttributes(
        Address $subject,
        ?AddressExtensionInterface $extensionAttributes
    ): ?AddressExtensionInterface {
        if (!$this->config->isActive()) {
            return $extensionAttributes;
        }

        $streetPrefix = $this->streetPrefixResolver->resolveFromEntity($subject);

        if ($streetPrefix === null) {
            return $extensionAttributes;
        }

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(
                AddressInterface::class
            );
        }

        $extensionAttributes->setStreetPrefix($streetPrefix);

        return $extensionAttributes;
    }
}
