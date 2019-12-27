<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Config;

use ArrayObject;
use Magento\Framework\Data\OptionSourceInterface;

class MonthyInstallments implements OptionSourceInterface
{
    /** @var ArrayObject $monthyValues*/
    protected $monthyValues = null;
    /** @var ArrayObject $monthyOptions */
    protected $monthyOptions = null;

    public function __construct()
    {
        $this->monthyOptions = new ArrayObject([
            [
                'value' => 3,
                'label' => __('3 Meses')
            ],
            [
                'value' => 6,
                'label' => __('6 Meses')
            ],
            [
                'value' => 9,
                'label' => __('9 Meses')
            ],
            [
                'value' => 12,
                'label' => __('12 Meses')
            ]
        ]);
        $this->monthyValues = new ArrayObject([
            3 => __('3 Meses'),
            6 => __('6 Meses'),
            9 => __('9 Meses'),
            12 => __('12 Meses')
        ]);
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        return $this->monthyOptions->getArrayCopy();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->monthyValues->getArrayCopy();
    }
}
