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
use Magento\Sales\Api\Data\OrderAddressExtensionInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order\Address;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Api\StreetPrefixResolver;

class OrderAddressExtensionPlugin
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
     * @param OrderAddressExtensionInterface|null $extensionAttributes
     * @return OrderAddressExtensionInterface|null
     */
    public function afterGetExtensionAttributes(
        Address $subject,
        ?OrderAddressExtensionInterface $extensionAttributes
    ): ?OrderAddressExtensionInterface {
        if (!$this->config->isActive()) {
            return $extensionAttributes;
        }

        $streetPrefix = $this->streetPrefixResolver->resolveFromEntity($subject);

        if ($streetPrefix === null) {
            return $extensionAttributes;
        }

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(
                OrderAddressInterface::class
            );
        }

        $extensionAttributes->setStreetPrefix($streetPrefix);

        return $extensionAttributes;
    }
}
