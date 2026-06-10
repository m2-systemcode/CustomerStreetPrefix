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

namespace SystemCode\CustomerStreetPrefix\Plugin\Block\Address;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Block\Address\Edit;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\ViewModel\StreetPrefix;

class EditPlugin
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param LayoutInterface $layout
     * @param StreetPrefix $viewModel
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly LayoutInterface $layout,
        private readonly StreetPrefix $viewModel
    ) {
    }

    /**
     * Execute after to html.
     *
     * @param Edit $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(Edit $subject, string $result): string
    {
        if (!$this->config->isActive()) {
            return $result;
        }

        $prefixHtml = $this->renderField($this->resolveCurrentValue($subject->getAddress()));

        if ($prefixHtml === '') {
            return $result;
        }

        $replaced = preg_replace(
            '/(<div class="field street[^"]*">)/',
            $prefixHtml . '$1',
            $result,
            1
        );

        return is_string($replaced) ? $replaced : $result;
    }

    /**
     * Resolve current value.
     *
     * @param AddressInterface $address
     * @return string
     */
    private function resolveCurrentValue(AddressInterface $address): string
    {
        $value = $address->getCustomAttribute('street_prefix')?->getValue();

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Handle render field.
     *
     * @param string $currentValue
     * @return string
     */
    private function renderField(string $currentValue): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate('SystemCode_CustomerStreetPrefix::address/street-prefix.phtml')
            ->setData('view_model', $this->viewModel)
            ->setData('current_value', $currentValue)
            ->toHtml();
    }
}
