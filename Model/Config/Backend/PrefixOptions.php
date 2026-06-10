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

namespace SystemCode\CustomerStreetPrefix\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

class PrefixOptions extends Value
{
    /**
     * Initialize dependencies.
     *
     * @param SerializerInterface $serializer
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ?AbstractResource $resource
     * @param ?AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        private readonly SerializerInterface $serializer,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave(): void
    {
        $value = $this->getValue();

        if (!is_array($value)) {
            $this->setValue('');
            return;
        }

        unset($value['__empty']);
        $this->setValue($this->serializer->serialize($value));
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _afterLoad(): void
    {
        $value = $this->getValue();

        if (!is_string($value) || $value === '') {
            return;
        }

        try {
            $decoded = $this->serializer->unserialize($value);
        } catch (\InvalidArgumentException) {
            return;
        }

        if (is_array($decoded)) {
            $this->setValue($decoded);
        }
    }
}
