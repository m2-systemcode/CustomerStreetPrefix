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

namespace SystemCode\CustomerStreetPrefix\Plugin\Quote;

use Magento\Quote\Model\Quote\Address;
use SystemCode\CustomerStreetPrefix\Model\Quote\StreetPrefixApplier;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QuoteAddressSavePlugin
{
    /**
     * Initialize dependencies.
     *
     * @param StreetPrefixApplier $streetPrefixApplier
     */
    public function __construct(
        private readonly StreetPrefixApplier $streetPrefixApplier
    ) {
    }

    /**
     * Execute before save.
     *
     * @param Address $subject
     * @return void
     */
    public function beforeSave(Address $subject): void
    {
        $this->streetPrefixApplier->prepare($subject);
    }
}
