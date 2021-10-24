<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Albert\Magento\Cms\Model\Config\Source;

/**
 * @api
 */
class ItemsComponent implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'Slider', 'label' => __('Slider')
            ],
            [
                'value' => 'List', 'label' => __('List')
            ]
        ];
    }

}