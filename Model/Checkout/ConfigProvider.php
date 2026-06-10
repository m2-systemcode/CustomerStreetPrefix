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

namespace SystemCode\CustomerStreetPrefix\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use SystemCode\CustomerStreetPrefix\Api\ConfigInterface;

class ConfigProvider implements ConfigProviderInterface
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
     * Retrieve config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'customerStreetPrefix' => [
                'isActive' => $this->config->isActive(),
            ],
        ];
    }
}
