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

namespace SystemCode\CustomerStreetPrefix\Model\Api;

use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;

class StreetPrefixResolver
{
    /**
     * Resolve .
     *
     * @param object $object
     * @return string|null
     */
    public function resolve(object $object): ?string
    {
        $value = $this->resolveFromEntity($object);

        if ($value !== null) {
            return $value;
        }

        if ($object instanceof ExtensibleDataInterface) {
            $extensionAttributes = $object->getExtensionAttributes();

            if ($extensionAttributes !== null && method_exists($extensionAttributes, 'getStreetPrefix')) {
                $extensionValue = $extensionAttributes->getStreetPrefix();

                if (is_string($extensionValue) && $extensionValue !== '') {
                    return $extensionValue;
                }
            }
        }

        return null;
    }

    /**
     * Resolve from entity.
     *
     * @param object $object
     * @return string|null
     */
    public function resolveFromEntity(object $object): ?string
    {
        if ($object instanceof DataObject) {
            $value = $object->getData('street_prefix');

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        if (method_exists($object, 'getStreetPrefix')) {
            $value = $object->getStreetPrefix();

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        if ($object instanceof CustomAttributesDataInterface) {
            $attribute = $object->getCustomAttribute('street_prefix');

            if ($attribute !== null) {
                $value = $attribute->getValue();

                if (is_scalar($value) && (string) $value !== '') {
                    return (string) $value;
                }
            }
        }

        return null;
    }
}
