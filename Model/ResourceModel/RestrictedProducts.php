<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel;

use Exception;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Product;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Restricted Products Resource Model
 *
 * Class RestrictedProducts
 * @package MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel
 */
class RestrictedProducts extends AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var EavConfig
     */
    private EavConfig $eavConfig;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EavConfig $eavConfig
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EavConfig $eavConfig,
        ?string $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }

    /**
     * Get product IDs with restricted brands
     *
     * @param array $restrictedBrands
     * @return array
     * @throws LocalizedException
     */
    public function getRestrictedProductIds(array $restrictedBrands): array
    {
        if (empty($restrictedBrands)) {
            return [];
        }

        try {
            $connection = $this->getConnection();

            // Get brand attribute ID
            $brandAttribute = $this->eavConfig->getAttribute(Product::ENTITY, BrandHelper::ATTRIBUTE_CODE_BRAND);
            if (!$brandAttribute || !$brandAttribute->getId()) {
                return [];
            }

            $storeId = $this->storeManager->getStore()->getId();

            // Build query to get product IDs with store scope
            $select = $connection->select()
                ->distinct()
                ->from(
                    ['cpe' => $this->getTable('catalog_product_entity')],
                    ['entity_id']
                )
                ->joinLeft(
                    ['cpei' => $this->getTable('catalog_product_entity_int')],
                    'cpei.entity_id = cpe.entity_id AND cpei.attribute_id = ' . $brandAttribute->getId() .
                    ' AND cpei.store_id = ' . $storeId,
                    []
                )
                ->joinLeft(
                    ['cpei_default' => $this->getTable('catalog_product_entity_int')],
                    'cpei_default.entity_id = cpe.entity_id AND cpei_default.attribute_id = ' . $brandAttribute->getId() .
                    ' AND cpei_default.store_id = 0',
                    []
                )
                ->where(
                    'COALESCE(cpei.value, cpei_default.value) IN (?)',
                    $restrictedBrands
                );

            // Add website filter
            if ($websiteId = $this->storeManager->getStore()->getWebsiteId()) {
                $select->join(
                    ['cpw' => $this->getTable('catalog_product_website')],
                    'cpw.product_id = cpe.entity_id',
                    []
                )->where('cpw.website_id = ?', $websiteId);
            }

            return $connection->fetchCol($select);
        } catch (Exception $e) {
            throw new LocalizedException(__('Could not get restricted product IDs: %1', $e->getMessage()));
        }
    }
}
