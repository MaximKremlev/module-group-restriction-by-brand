<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Plugin\Catalog\Model\ResourceModel\Product;

use Magento\Framework\DB\Select;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;

/**
 * Plugin for adding brand restrictions for product collection
 *
 * Class Collection
 * @package MaximKremlev\GroupRestrictionByBrand\Plugin\Catalog\Model\ResourceModel\Product
 */
class Collection
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
     * @param ProductCollection $subject
     */
    public function beforeLoad(ProductCollection $subject): void
    {
        if ($subject->getFlag(BrandHelper::COLLECTION_FILTER_FLAG_CODE)
            || $subject->getFlag(BrandHelper::SKIP_BRAND_RESTRICTION_FLAG)
        ) {
            return;
        }

        $this->addRestrictedProductFilter($subject, $subject->getSelect());
    }

    /**
     * @param ProductCollection $subject
     * @param Select $productSelect
     *
     * @return Select
     */
    public function afterGetSelect(
        ProductCollection $subject,
        Select $productSelect
    ): Select
    {
        if ($subject->getFlag(BrandHelper::COLLECTION_FILTER_FLAG_CODE)
            || $subject->getFlag(BrandHelper::SKIP_BRAND_RESTRICTION_FLAG)
        ) {
            return $productSelect;
        }

        $this->addRestrictedProductFilter($subject, $productSelect);

        return $productSelect;
    }

    /**
     * @param ProductCollection $subject
     */
    public function beforeGetSize(ProductCollection $subject): void
    {
        if ($subject->getFlag(BrandHelper::COLLECTION_FILTER_FLAG_CODE)
            || $subject->getFlag(BrandHelper::SKIP_BRAND_RESTRICTION_FLAG)
        ) {
            return;
        }

        $this->addRestrictedProductFilter($subject, $subject->getSelect());
    }

    /**
     * @param ProductCollection $subject
     * @param Select $productSelect
     */
    protected function addRestrictedProductFilter(
        ProductCollection $subject,
        Select $productSelect
    ): void
    {
        $subject->setFlag(BrandHelper::COLLECTION_FILTER_FLAG_CODE, 1);

        $productIds = $this->brandHelper->getRestrictedProductIds();

        if ($productIds
            && ($subject->getIdFieldName() === 'entity_id' || $subject->getIdFieldName() === 'selection_id')
        ) {
            $idField = $subject::MAIN_TABLE_ALIAS . '.entity_id';
            $productSelect->where($idField . ' NOT IN (?)', $productIds);
        }
    }
}
