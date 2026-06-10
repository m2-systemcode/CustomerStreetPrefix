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

namespace SystemCode\CustomerStreetPrefix\Model\Validation;

class StreetPrefixValidationGuard
{
    /**
     * @var int
     */
    private int $depth = 0;

    /**
     * Handle run without validation.
     *
     * @param callable $callback
     * @return void
     */
    public function runWithoutValidation(callable $callback): void
    {
        $this->depth++;

        try {
            $callback();
        } finally {
            $this->depth--;
        }
    }

    /**
     * Handle should skip.
     *
     * @return bool
     */
    public function shouldSkip(): bool
    {
        return $this->depth > 0;
    }
}
