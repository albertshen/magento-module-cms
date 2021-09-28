<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Albert\Magento\Cms\Model\Template;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filter\DirectiveProcessor\DependDirective;
use Magento\Framework\Filter\DirectiveProcessor\ForDirective;
use Magento\Framework\Filter\DirectiveProcessor\IfDirective;
use Magento\Framework\Filter\DirectiveProcessor\LegacyDirective;
use Magento\Framework\Filter\DirectiveProcessor\TemplateDirective;
use Magento\Framework\Filter\DirectiveProcessor\VarDirective;
use Magento\Framework\Filter\DirectiveProcessorInterface;

/**
 * Template Filter Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Filter
{

    /**
     * @var DirectiveProcessorInterface[]
     */
    private $directiveProcessors;

    /**
     * @var bool
     */
    private $strictMode = true;

    /**
     * @var VariableResolverInterface|null
     */
    private $variableResolver;

    /**
     * @var \Magento\Widget\Model\ResourceModel\Widget
     */
    protected $_widgetResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Store id
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param DirectiveProcessorInterface[] $directiveProcessors
     * @param \Magento\Framework\Filter\VariableResolverInterface|null $variableResolver
     * @param \Magento\Widget\Model\ResourceModel\Widget\Widget $widgetResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        $directiveProcessors = [],
        \Magento\Framework\Filter\VariableResolverInterface $variableResolver = null,
        \Magento\Widget\Model\ResourceModel\Widget $widgetResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\BlockFactory $blockFactory
    ) {
        $this->string = $string;
        $this->directiveProcessors = $directiveProcessors;
        $this->variableResolver = $variableResolver ?? ObjectManager::getInstance()->get(\Magento\Framework\Filter\VariableResolverInterface::class);
        if (empty($directiveProcessors)) {
            $this->directiveProcessors = [
                'legacy' => ObjectManager::getInstance()->get(LegacyDirective::class),
            ];
        }
        $this->_storeManager = $storeManager;
        $this->_blockFactory = $blockFactory;
    }

    /**
     * Filter the string as array data.
     *
     * @param string $value
     * @return array
     * @throws \Exception
     */
    public function filter($value)
    {
        $results = [];
        foreach ($this->directiveProcessors as $key => $directiveProcessor) {
            if (!$directiveProcessor instanceof DirectiveProcessorInterface) {
                throw new \InvalidArgumentException(
                    'Directive processors must implement ' . DirectiveProcessorInterface::class
                );
            }

            if (preg_match_all('/{{(widget)(.*?)}}/si', $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    $results[] = $this->generateWidget($construction);
                }
            }
        }
        if (count($results) === 1) {
            $results = $results[0];
        } 
        return $results;
    }

    /**
     * Generate widget
     *
     * @param string[] $construction
     * @return string
     */
    public function widgetDirective($construction)
    {
        return $this->generateWidget($construction);
    }

    /**
     * General method for generate widget
     *
     * @param string[] $construction
     * @return array
     */
    public function generateWidget($construction)
    {
        $params = $this->getParameters($construction[2]);

        // Determine what name block should have in layout
        $name = null;
        if (isset($params['name'])) {
            $name = $params['name'];
        }

        if (!isset($params['store_id'])) {
            $params['store_id'] = $this->getStoreId();
        }

        // validate required parameter type or id
        if (!empty($params['type'])) {
            $type = $params['type'];
        } elseif (!empty($params['id'])) {
            $preConfigured = $this->_widgetResource->loadPreconfiguredWidget($params['id']);
            $type = $preConfigured['widget_type'];
            $params = $preConfigured['parameters'];
        } else {
            return [];
        }

        $widget = ObjectManager::getInstance()->create($type, ['data' => $params]);

        if ($widget instanceof \Albert\Magento\Cms\Block\CmsBlockInterface) {
            $block = $this->_blockFactory->create();
            $block->setStoreId($params['store_id'])->load($params['block_id']);
            if ($widget->getData('component')) {
                return array_merge(['component' => $widget->getData('component')], ['items' => $this->filter($block->getContent())]);
            }
            return $this->filter($block->getContent());
        }
        // // define widget block andcheck the type is instance of Widget Interface
        if (!$widget instanceof \Albert\Magento\Cms\Block\BlockInterface) {
            return [];
        }

        if ($widget->getData('component')) {
            return array_merge(['component' => $widget->getData('component')], $widget->getResults());
        }
        return $widget->getResults();
    }

    /**
     * Return associative array of parameters.
     *
     * @param string $value raw parameters
     * @return array
     * @deprecated 102.0.4 Use the directive interfaces instead
     */
    protected function getParameters($value)
    {
        $tokenizer = new \Magento\Framework\Filter\Template\Tokenizer\Parameter();
        $tokenizer->setString($value);
        $params = $tokenizer->tokenize();
        foreach ($params as $key => $value) {
            if (substr($value, 0, 1) === '$') {
                $params[$key] = $this->getVariable(substr($value, 1), null);
            }
        }
        return $params;
    }

    /**
     * Resolve a variable's value for a given var directive construction
     *
     * @param string $value raw parameters
     * @param string $default default value
     * @return string
     * @deprecated 102.0.4 Use \Magento\Framework\Filter\VariableResolverInterface instead
     */
    protected function getVariable($value, $default = '{no_value_defined}')
    {
        \Magento\Framework\Profiler::start('email_template_processing_variables');
        $result = $this->variableResolver->resolve($value, $this, $this->templateVars) ?? $default;
        \Magento\Framework\Profiler::stop('email_template_processing_variables');

        return $result;
    }

    /**
     * Getter. If $_storeId is null, return design store id.
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (null === $this->_storeId) {
            $this->_storeId = $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }
}
