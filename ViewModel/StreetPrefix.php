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

namespace SystemCode\CustomerStreetPrefix\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;

class StreetPrefix implements ArgumentInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     */
    public function __construct(
        private readonly ConfigInterface $config
    ) {
    }

    /**
     * Handle should render.
     *
     * @return bool
     */
    public function shouldRender(): bool
    {
        return $this->config->isActive();
    }

    /**
     * Retrieve options.
     *
     * @return array<int, array{label: string, value: string}>
     */
    public function getOptions(): array
    {
        return $this->config->getSelectOptions();
    }

    /**
     * Check whether required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->config->isRequired();
    }

    /**
     * Retrieve label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->config->getFieldLabel();
    }

    /**
     * Retrieve required field class.
     *
     * @return string
     */
    public function getRequiredFieldClass(): string
    {
        return $this->isRequired() ? ' required _required' : '';
    }

    /**
     * Retrieve required input class.
     *
     * @return string
     */
    public function getRequiredInputClass(): string
    {
        return $this->isRequired() ? ' validate-select required-entry' : '';
    }
}
