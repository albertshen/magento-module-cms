<?php
/**
 * Copyright © PHP Digital, Inc. All rights reserved.
 */
namespace AlbertMage\Cms\Model\Config\Source;

/**
 * @author Albert Shen <albertshen1206@gmail.com>
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