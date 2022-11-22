<?php
/**
 * Copyright © PHP Digital, Inc. All rights reserved.
 */
namespace Albert\Magento\Cms\Block;

/**
 * Widget Block Interface
 * @author Albert Shen <albertshen1206@gmail.com>
 */
namespace  AlbertMage\Cms\Block;

/**
 * Interface \AlbertMage\Cms\Block\BlockInterface
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
