<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Widget Block Interface
 *
 * @author <albertshen1206@gmail.com>
 */
namespace  Albert\Magento\Cms\Block;

/**
 * Interface \Albert\Magento\Cms\Block\BlockInterface
 */
interface BlockInterface
{
    /**
     * Return data in the widget.
     *
     * @return array
     */
    public function getResults();
}
