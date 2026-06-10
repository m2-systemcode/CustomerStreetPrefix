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

namespace SystemCode\CustomerStreetPrefix\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as Subject;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class LayoutProcessor
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
     * Execute after process.
     *
     * @param Subject $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(Subject $subject, array $jsLayout): array
    {
        if (!$this->config->isActive()) {
            return $jsLayout;
        }

        $fieldConfig = $this->buildFieldConfig();
        $jsLayout = $this->applyToShipping($jsLayout, $fieldConfig);

        return $this->applyToBilling($jsLayout, $fieldConfig);
    }

    /**
     * Handle build field config.
     *
     * @return array
     */
    private function buildFieldConfig(): array
    {
        $options = [];

        foreach ($this->config->getSelectOptions() as $option) {
            $options[] = [
                'value' => $option['value'],
                'label' => $option['label'],
            ];
        }

        $validation = [];

        if ($this->config->isRequired()) {
            $validation['required-entry'] = true;
        }

        return [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'PLACEHOLDER_SCOPE',
                'template' => 'ui/form/field',
                'options' => $options,
                'id' => 'street-prefix',
            ],
            'dataScope' => 'PLACEHOLDER_SCOPE.street_prefix',
            'label' => $this->config->getFieldLabel(),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'required' => $this->config->isRequired(),
            'validation' => $validation,
            'id' => 'street-prefix',
        ];
    }

    /**
     * Resolve sort order.
     *
     * @param array $fieldset
     * @return int
     */
    private function resolveSortOrder(array $fieldset): int
    {
        return (int) ($fieldset['street']['sortOrder'] ?? 70) - 5;
    }

    /**
     * Apply to shipping.
     *
     * @param array $jsLayout
     * @param array $fieldConfig
     * @return array
     */
    private function applyToShipping(array $jsLayout, array $fieldConfig): array
    {
        $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] ?? null;

        if (!is_array($fieldset)) {
            return $jsLayout;
        }

        $shippingField = $fieldConfig;
        $shippingField['config']['customScope'] = 'shippingAddress.custom_attributes';
        $shippingField['dataScope'] = 'shippingAddress.custom_attributes.street_prefix';
        $shippingField['sortOrder'] = $this->resolveSortOrder($fieldset);
        $fieldset['street_prefix'] = $shippingField;

        return $jsLayout;
    }

    /**
     * Apply to billing.
     *
     * @param array $jsLayout
     * @param array $fieldConfig
     * @return array
     */
    private function applyToBilling(array $jsLayout, array $fieldConfig): array
    {
        $paymentsList = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children'] ?? null;

        if (!is_array($paymentsList)) {
            return $jsLayout;
        }

        foreach (array_keys($paymentsList) as $paymentMethodForm) {
            $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);
            $formKey = $paymentMethodCode . '-form';
            $formFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$formKey]
                ['children']['form-fields']['children'] ?? null;

            if (!is_array($formFields)) {
                continue;
            }

            $billingField = $fieldConfig;
            $scope = 'billingAddress' . $paymentMethodCode . '.custom_attributes';
            $billingField['config']['customScope'] = $scope;
            $billingField['dataScope'] = $scope . '.street_prefix';
            $billingField['sortOrder'] = $this->resolveSortOrder($formFields);
            $formFields['street_prefix'] = $billingField;
        }

        return $jsLayout;
    }
}
