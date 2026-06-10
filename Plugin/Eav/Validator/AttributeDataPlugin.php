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

namespace SystemCode\CustomerStreetPrefix\Plugin\Eav\Validator;

use Magento\Customer\Model\Address as CustomerAddress;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Validator\Attribute\Data;
use Magento\Framework\Model\AbstractModel;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Validation\StreetPrefixValidationGuard;

class AttributeDataPlugin
{
    private const string ATTRIBUTE_CODE = 'street_prefix';

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
     * Execute before is valid.
     *
     * @param Data $subject
     * @param mixed $entity
     * @return array
     */
    public function beforeIsValid(Data $subject, $entity): array
    {
        if (!$entity instanceof AbstractModel || !$this->shouldSkipValidation($entity)) {
            return [$entity];
        }

        $subject->setDeniedAttributesList([self::ATTRIBUTE_CODE]);

        return [$entity];
    }

    /**
     * Handle should skip validation.
     *
     * @param AbstractModel $entity
     * @return bool
     */
    private function shouldSkipValidation(AbstractModel $entity): bool
    {
        if ($this->validationGuard->shouldSkip()) {
            return true;
        }

        $resource = $entity->getResource();

        if (!$resource instanceof AbstractEntity) {
            return false;
        }

        $entityType = $resource->getEntityType()->getEntityTypeCode();

        if (!in_array($entityType, ['customer_address', 'order_address'], true)) {
            return false;
        }

        if (!$this->config->isActive()) {
            return true;
        }

        if ($entity instanceof CustomerAddress && (int) $entity->getId() > 0) {
            return true;
        }

        return $entity instanceof QuoteAddress && (int) $entity->getCustomerAddressId() > 0;
    }
}
