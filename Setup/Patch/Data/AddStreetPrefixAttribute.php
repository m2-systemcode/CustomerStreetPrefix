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

namespace SystemCode\CustomerStreetPrefix\Setup\Patch\Data;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SystemCode\CustomerStreetPrefix\Model\Config\Source\StreetPrefix;

class AddStreetPrefixAttribute implements DataPatchInterface
{
    private const array USED_IN_FORMS = [
        'adminhtml_customer_address',
        'customer_address_edit',
        'customer_register_address',
    ];

    /**
     * Initialize dependencies.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetupFactory $customerSetupFactory
    ) {
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->addAttribute('customer_address', 'street_prefix', [
            'type' => 'varchar',
            'input' => 'select',
            'label' => 'Street Prefix',
            'source' => StreetPrefix::class,
            'global' => 1,
            'visible' => 1,
            'required' => 0,
            'user_defined' => 1,
            'system' => 0,
            'visible_on_front' => 1,
            'position' => 65,
            'group' => 'General',
        ]);

        $customerSetup->addAttributeToSet(
            'customer_address',
            AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS,
            'General',
            'street_prefix',
            65
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'street_prefix');
        $attribute->setData('used_in_forms', self::USED_IN_FORMS);
        $attribute->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
