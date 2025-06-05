<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Plugin\Catalog\Model;

use Magento\Catalog\Model\Layer as LayerCore;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as FulltextCollection;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;

/**
 * Plugin for adding brand restrictions for layer collection
 *
 * Class Layer
 * @package MaximKremlev\GroupRestrictionByBrand\Plugin\Catalog\Model
 */
class Layer
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
     * Add restricted product filter to the search engine.
     *
     * @param LayerCore $subject
     * @param Collection|FulltextCollection $collection
     *
     * @return array|null
     */
    public function beforePrepareProductCollection(LayerCore $subject, Collection|FulltextCollection $collection): ?array
    {
        $collection->setFlag(BrandHelper::COLLECTION_FILTER_FLAG_CODE, 1);

        if ($productIds = $this->brandHelper->getRestrictedProductIds()) {
            // add filter to product fulltext search | catalog product collection
            if ($this->isFulltextCollection($collection)) {
                // fulltext collection uses searchCriteriaBuilder for adding filters
                $collection->addFieldToFilter('maxim_group_restriction_elastic_entity_id', $productIds);
            } else {
                $collection->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }

            return [$collection];
        }

        return null;
    }

    /**
     * Determine if a collection is a fulltext collection
     *
     * Avoid instanceof check for compatibility
     */
    private function isFulltextCollection(AbstractCollection $collection): bool
    {
        return method_exists($collection, 'getFacetedData');
    }
}
