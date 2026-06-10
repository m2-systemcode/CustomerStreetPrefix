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

namespace SystemCode\CustomerStreetPrefix\Plugin\Quote\Address\Validator;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Address\Validator\AddressAttributeValidator;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Validation\StreetPrefixValidationGuard;

class AddressAttributeValidatorPlugin
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
     * @param AddressAttributeValidator $subject
     * @param callable $proceed
     * @param AddressInterface $address
     * @param string $addressType
     * @return void
     */
    public function aroundValidate(
        AddressAttributeValidator $subject,
        callable $proceed,
        AddressInterface $address,
        string $addressType
    ): void {
        if (!$this->shouldSkipStreetPrefixValidation($address)) {
            $proceed($address, $addressType);
            return;
        }

        $this->validationGuard->runWithoutValidation(
            static function () use ($proceed, $address, $addressType): void {
                $proceed($address, $addressType);
            }
        );
    }

    /**
     * Handle should skip street prefix validation.
     *
     * @param AddressInterface $address
     * @return bool
     */
    private function shouldSkipStreetPrefixValidation(AddressInterface $address): bool
    {
        if (!$this->config->isActive()) {
            return true;
        }

        return (int) $address->getCustomerAddressId() > 0;
    }
}
