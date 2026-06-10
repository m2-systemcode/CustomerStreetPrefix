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

namespace SystemCode\CustomerStreetPrefix\Plugin\Eav\Entity\Attribute;

use Magento\Customer\Model\Address as CustomerAddress;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Validation\StreetPrefixValidationGuard;

class BackendPlugin
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param StreetPrefixValidationGuard $validationGuard
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly StreetPrefixValidationGuard $validationGuard
    ) {
    }

    /**
     * Execute around validate.
     *
     * @param AbstractBackend $subject
     * @param callable $proceed
     * @param DataObject $object
     */
    public function aroundValidate(AbstractBackend $subject, callable $proceed, DataObject $object)
    {
        $attribute = $subject->getAttribute();

        if ($attribute === null
            || $attribute->getAttributeCode() !== 'street_prefix'
            || !$this->shouldSkipValidation($object)
        ) {
            return $proceed($object);
        }

        return true;
    }

    /**
     * Handle should skip validation.
     *
     * @param DataObject $object
     * @return bool
     */
    private function shouldSkipValidation(DataObject $object): bool
    {
        if ($this->validationGuard->shouldSkip()) {
            return true;
        }

        if (!$this->config->isActive()) {
            return true;
        }

        if ($object instanceof CustomerAddress && (int) $object->getId() > 0) {
            return true;
        }

        return $object instanceof QuoteAddress && (int) $object->getCustomerAddressId() > 0;
    }
}
