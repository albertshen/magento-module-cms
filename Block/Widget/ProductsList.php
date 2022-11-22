<?php
/**
 * Copyright © PHP Digital, Inc. All rights reserved.
 */
namespace Albert\Magento\Cms\Block\Widget;

/**
 * @author Albert Shen <albertshen1206@gmail.com>
 */
class ProductsList extends \Albert\Magento\Cms\Block\Product\AbstractProduct
{

   /**
    * Retrieve data
    *
    * @return array
    */
   public function getResults()
   {
      $block = $this->createCollection();
      //var_dump($block->count());exit;
      $data = [];
      foreach ($block->getItems() as $item) {
         $data['items'][] = ['sku' => $item->getSku(), 'url' => $item->getProductUrl()];
      }
      return $data;
   }

}