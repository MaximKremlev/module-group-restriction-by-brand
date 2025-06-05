<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Model;

use Magento\Framework\Model\AbstractModel;
use MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel\RestrictedProducts as ResourceModel;

/**
 * Restricted Products Model
 *
 * Class RestrictedProducts
 * @package MaximKremlev\GroupRestrictionByBrand\Model
 */
class RestrictedProducts extends AbstractModel
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get restricted product IDs for given brand IDs
     *
     * @param array $brandIds
     * @return array
     */
    public function getRestrictedProductIds(array $brandIds): array
    {
        return $this->getResource()->getRestrictedProductIds($brandIds);
    }
}
