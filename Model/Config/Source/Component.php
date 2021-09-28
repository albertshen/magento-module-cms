<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Albert\Magento\Cms\Model\Config\Source;

/**
 * @api
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
                'value' => '', 'label' => __('-- Please Select --')
            ],
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