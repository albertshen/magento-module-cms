<?php
/**
 * @author <albertshen1206@gmail.com>
 */
namespace Albert\Magento\Cms\Block\Widget;

class Block extends \Magento\Framework\DataObject implements \Albert\Magento\Cms\Block\CmsBlockInterface
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