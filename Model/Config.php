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

namespace SystemCode\CustomerStreetPrefix\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Check whether enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_REQUIRED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve field label.
     *
     * @return string
     */
    public function getFieldLabel(): string
    {
        $value = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_LABEL,
            ScopeInterface::SCOPE_STORE
        ));

        if ($value !== '') {
            return $value;
        }

        return (string) __('Street Prefix');
    }

    /**
     * Retrieve select options.
     *
     * @return array
     */
    public function getSelectOptions(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $options = [
            [
                'label' => (string) __('Please select a %1.', mb_strtolower($this->getFieldLabel())),
                'value' => '',
            ],
        ];

        $raw = (string) $this->scopeConfig->getValue(
            self::XML_PATH_OPTIONS,
            ScopeInterface::SCOPE_STORE
        );

        if ($raw === '') {
            return $options;
        }

        try {
            $decoded = $this->serializer->unserialize($raw);
        } catch (\InvalidArgumentException) {
            return $options;
        }

        if (!is_array($decoded)) {
            return $options;
        }

        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }

            $value = trim((string) ($row['prefix_options'] ?? ''));

            if ($value === '') {
                continue;
            }

            $options[] = ['label' => $value, 'value' => $value];
        }

        return $options;
    }

    /**
     * Check whether active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return count($this->getSelectOptions()) > 1;
    }
}
