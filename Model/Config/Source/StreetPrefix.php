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

namespace SystemCode\CustomerStreetPrefix\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use SystemCode\CustomerStreetPrefix\Model\Config;

class StreetPrefix extends AbstractSource
{
    /**
     * Initialize dependencies.
     *
     * @param Config $config
     */
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * Retrieve all options.
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = $this->config->getSelectOptions();
        }

        return $this->_options;
    }
}
