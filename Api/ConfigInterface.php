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

namespace SystemCode\CustomerStreetPrefix\Api;

interface ConfigInterface
{
    public const string XML_PATH_ENABLED = 'customerstreetprefix/general/enabled';
    public const string XML_PATH_REQUIRED = 'customerstreetprefix/general/required';
    public const string XML_PATH_LABEL = 'customerstreetprefix/general/field_label';
    public const string XML_PATH_OPTIONS = 'customerstreetprefix/general/prefix_options';

    /**
     * Check whether enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Check whether required.
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * Retrieve field label.
     *
     * @return string
     */
    public function getFieldLabel(): string;

    /**
     * Retrieve select options.
     *
     * @return array<int, array{label: string, value: string}>
     */
    public function getSelectOptions(): array;

    /**
     * Check whether active.
     *
     * @return bool
     */
    public function isActive(): bool;
}
