<?php
/**
 * Copyright © PHP Digital, Inc. All rights reserved.
 */
namespace AlbertMage\Cms\Block\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @author Albert Shen <albertshen1206@gmail.com>
 */
class ImageChooser extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }
    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl('cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']);
        /** @var \Magento\Backend\Block\Widget\Button $chooser */
        $chooser = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setType('button')
            ->setClass('btn-chooser')
            ->setLabel($config['button']['open'])
            ->setOnClick('MediabrowserUtility.openDialog(\'' . $sourceUrl . '\', 0, 0, "MediaBrowser", {})')
            ->setDisabled($element->getReadonly());

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $image = $objectManager->create('\AlbertMage\Cms\Helper\Image');
            $imageElement = '<img src="'.$image->resize('/'.$element->getData('value'), 640, 480).'" />';

        // /** @var \Magento\Framework\Data\Form\Element\Text $input */
        $input = $this->elementFactory->create("hidden", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("widget-option input-text admin__control-text");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }
        $element->setData('after_element_html', $imageElement.$input->getElementHtml() . $chooser->toHtml()
            . "<script>require(['mage/adminhtml/browser']);</script>");
        return $element;
    }
}