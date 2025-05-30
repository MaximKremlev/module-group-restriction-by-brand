<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Model;

use Magento\Framework\Model\AbstractModel;
use MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel\RestrictedBrands as ResourceModel;

/**
 * Restricted Brands Model
 */
class RestrictedBrands extends AbstractModel
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }
}
