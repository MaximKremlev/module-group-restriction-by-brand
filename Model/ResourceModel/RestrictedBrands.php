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
    private const string TABLE_NAME = 'customer_group_restricted_brands';
    private const string ENTITY_ID = 'entity_id';
    private const string BRAND_ID = 'brand_id';
    private const string CUSTOMER_GROUP_ID = 'customer_group_id';

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ENTITY_ID);
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
                ->from($this->getMainTable(), [self::BRAND_ID])
                ->where(self::CUSTOMER_GROUP_ID . ' = ?', $customerGroupId);

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
            [self::CUSTOMER_GROUP_ID . ' = ?' => $customerGroupId]
        );

        if (!empty($brandIds)) {
            $data = [];
            foreach ($brandIds as $brandId) {
                if ($brandId) {
                    $data[] = [
                        self::CUSTOMER_GROUP_ID => $customerGroupId,
                        self::BRAND_ID => $brandId
                    ];
                }
            }

            if (!empty($data)) {
                $connection->insertMultiple($this->getMainTable(), $data);
            }
        }
    }
}
