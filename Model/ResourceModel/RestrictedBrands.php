<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Restricted Brands Resource Model
 *
 * Class RestrictedBrands
 * @package MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel
 */
class RestrictedBrands extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init('customer_group_restricted_brands', 'entity_id');
    }

    /**
     * Get restricted brand ids by customer group id
     *
     * @param int $customerGroupId
     * @return array
     */
    public function getRestrictedBrandIds(int $customerGroupId): array
    {
        try {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from($this->getMainTable(), ['brand_id'])
                ->where('customer_group_id = ?', $customerGroupId);

            return $connection->fetchCol($select);
        } catch (Exception) {
            return [];
        }
    }

    /**
     * Save restricted brands for customer group
     *
     * @param int $customerGroupId
     * @param array $brandIds
     * @return void
     * @throws LocalizedException
     */
    public function saveRestrictedBrands(int $customerGroupId, array $brandIds): void
    {
        $connection = $this->getConnection();

        $connection->delete(
            $this->getMainTable(),
            ['customer_group_id = ?' => $customerGroupId]
        );

        if (!empty($brandIds)) {
            $data = [];
            foreach ($brandIds as $brandId) {
                if ($brandId) {
                    $data[] = [
                        'customer_group_id' => $customerGroupId,
                        'brand_id' => $brandId
                    ];
                }
            }

            if (!empty($data)) {
                $connection->insertMultiple($this->getMainTable(), $data);
            }
        }
    }
}
