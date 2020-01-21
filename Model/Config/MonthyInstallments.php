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
                'label' => __('3 Months')
            ],
            [
                'value' => 6,
                'label' => __('6 Months')
            ],
            [
                'value' => 9,
                'label' => __('9 Months')
            ],
            [
                'value' => 12,
                'label' => __('12 Months')
            ]
        ]);
        $this->monthyValues = new ArrayObject([
            3 => __('3 Months'),
            6 => __('6 Months'),
            9 => __('9 Months'),
            12 => __('12 Months')
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
