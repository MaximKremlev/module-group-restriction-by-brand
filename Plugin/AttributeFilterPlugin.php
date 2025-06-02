<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Plugin;

use Magento\CatalogSearch\Model\Layer\Filter\Attribute as AttributeFilter;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;

/**
 * Plugin for filtering layer navigation on brand restrictions
 *
 * Class AttributeFilterPlugin
 * @package MaximKremlev\GroupRestrictionByBrand\Plugin
 */
class AttributeFilterPlugin
{
    /**
     * @var BrandHelper
     */
    private BrandHelper $brandHelper;

    /**
     * @param BrandHelper $brandHelper
     */
    public function __construct(
        BrandHelper $brandHelper
    ) {
        $this->brandHelper = $brandHelper;
    }

    /**
     * Remove restricted brands from filter options
     *
     * @param AttributeFilter $subject
     * @param array $result
     * @return array
     */
    public function afterGetItems(AttributeFilter $subject, array $result): array
    {
        if (!$this->brandHelper->isCustomerLoggedIn()) {
            return $result;
        }

        $attributeCode = $subject->getAttributeModel()->getAttributeCode();

        if ($attributeCode !== BrandHelper::ATTRIBUTE_CODE_BRAND) {
            return $result;
        }

        $restrictedBrands = $this->brandHelper->getRestrictedBrandIds();

        if (empty($restrictedBrands)) {
            return $result;
        }

        // Checking for existence of restricted brands
        return array_filter($result, function ($item) use ($restrictedBrands) {
            return !in_array($item->getValue(), $restrictedBrands, true);
        });
    }
}
