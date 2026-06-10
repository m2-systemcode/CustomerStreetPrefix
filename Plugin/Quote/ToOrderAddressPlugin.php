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

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Api\Data\OrderAddressInterface;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;
use SystemCode\CustomerStreetPrefix\Model\Api\StreetPrefixResolver;

class ToOrderAddressPlugin
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param AddressRepositoryInterface $addressRepository
     * @param StreetPrefixResolver $streetPrefixResolver
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly AddressRepositoryInterface $addressRepository,
        private readonly StreetPrefixResolver $streetPrefixResolver
    ) {
    }

    /**
     * Execute after convert.
     *
     * @param ToOrderAddress $subject
     * @param OrderAddressInterface $result
     * @param Address $quoteAddress
     * @return OrderAddressInterface
     */
    public function afterConvert(
        ToOrderAddress $subject,
        OrderAddressInterface $result,
        Address $quoteAddress
    ): OrderAddressInterface {
        if (!$this->config->isActive()) {
            return $result;
        }

        $streetPrefix = (string) ($quoteAddress->getStreetPrefix() ?? '');

        if ($streetPrefix === '' && $quoteAddress->getCustomerAddressId()) {
            $streetPrefix = (string) (
                $this->resolveFromCustomerAddress((int) $quoteAddress->getCustomerAddressId()) ?? ''
            );
        }

        if ($streetPrefix !== '') {
            $result->setStreetPrefix($streetPrefix);
        }

        return $result;
    }

    /**
     * Resolve from customer address.
     *
     * @param int $customerAddressId
     * @return ? string
     */
    private function resolveFromCustomerAddress(int $customerAddressId): ?string
    {
        try {
            $customerAddress = $this->addressRepository->getById($customerAddressId);
        } catch (NoSuchEntityException | LocalizedException) {
            return null;
        }

        return $this->streetPrefixResolver->resolve($customerAddress);
    }
}
