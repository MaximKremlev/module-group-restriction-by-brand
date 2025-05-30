<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;

/**
 * Plugin for filtering product collection based on brand restrictions
 *
 * Class ProductCollectionPlugin
 * @package MaximKremlev\GroupRestrictionByBrand\Plugin
 */
class ProductCollectionPlugin
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
     * Add brand restriction filter to product collection
     *
     * Filters out products with brands that are restricted for current customer group.
     * Also includes products without brand value.
     *
     * @param Collection $subject
     * @return void
     */
    public function beforeLoad(Collection $subject): void
    {
        if ($subject->hasFlag(BrandHelper::COLLECTION_FLAG_BRAND_FILTER_ADDED)) {
            return;
        }

        if (!$this->brandHelper->isCustomerLoggedIn()) {
            return;
        }

        $restrictedBrands = $this->brandHelper->getRestrictedBrandIds();
        if (!empty($restrictedBrands)) {
            $subject->addAttributeToFilter([
                ['attribute' => BrandHelper::ATTRIBUTE_CODE_BRAND, 'null' => true],
                ['attribute' => BrandHelper::ATTRIBUTE_CODE_BRAND, 'nin' => $restrictedBrands]
            ]);
        }

        $subject->setFlag(BrandHelper::COLLECTION_FLAG_BRAND_FILTER_ADDED, true);
    }
}
