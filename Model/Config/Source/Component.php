<?php
/**
 * Copyright © PHP Digital, Inc. All rights reserved.
 */
namespace AlbertMage\Cms\Model\Config\Source;

/**
 * @author Albert Shen <albertshen1206@gmail.com>
 */
class Component implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'CategoryProductsList', 'label' => __('Category Products List (Rest)')
            ],
            [
                'value' => 'HomeBanner', 'label' => __('Home Banner')
            ],
            [
                'value' => 'ProductHero', 'label' => __('ProductHero')
            ]
        ];
    }

}