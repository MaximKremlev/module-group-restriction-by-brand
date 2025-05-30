<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Model\Attribute\Source;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;

/**
 * Source model for brand attribute options
 *
 * Class Brand
 * @package MaximKremlev\GroupRestrictionByBrand\Model\Attribute\Source
 */
class Brand extends AbstractSource
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $optionCollectionFactory;

    /**
     * @var Config
     */
    private Config $eavConfig;

    /**
     * @param CollectionFactory $optionCollectionFactory
     * @param Config $eavConfig
     */
    public function __construct(
        CollectionFactory $optionCollectionFactory,
        Config $eavConfig
    ) {
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        if (!$this->_options) {
            try {
                /** @var Attribute $attribute */
                $attribute = $this->eavConfig->getAttribute(
                    Product::ENTITY,
                    BrandHelper::ATTRIBUTE_CODE_BRAND
                );

                if ($attribute && $attribute->getId()) {
                    $collection = $this->optionCollectionFactory->create()
                        ->setAttributeFilter($attribute->getAttributeId())
                        ->setPositionOrder('asc', true);

                    $this->_options[] = ['label' => ' ', 'value' => ''];

                    foreach ($collection as $option) {
                        $this->_options[] = [
                            'label' => $option->getValue(),
                            'value' => $option->getOptionId()
                        ];
                    }
                }
            } catch (LocalizedException) {}
        }

        return $this->_options ?? [];
    }
}
