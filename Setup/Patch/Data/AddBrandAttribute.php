<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Model\Config as EavConfig;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;
use MaximKremlev\GroupRestrictionByBrand\Model\Attribute\Source\Brand;

/**
 * Add brand attribute to product entity
 */
class AddBrandAttribute implements DataPatchInterface, PatchRevertableInterface
{
    private const ATTRIBUTE_LABEL = 'Brand';
    private const ATTRIBUTE_GROUP = 'General';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var EavConfig
     */
    private EavConfig $eavConfig;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param EavConfig $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        EavConfig $eavConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Add brand attribute if it doesn't exist
     */
    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Check if attribute already exists
        $attributeId = $eavSetup->getAttributeId(
            Product::ENTITY,
            BrandHelper::ATTRIBUTE_CODE_BRAND
        );

        if (!$attributeId) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                BrandHelper::ATTRIBUTE_CODE_BRAND,
                [
                    'type' => 'int',
                    'label' => self::ATTRIBUTE_LABEL,
                    'input' => 'select',
                    'source' => Brand::class,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'searchable' => true,
                    'filterable' => true,
                    'comparable' => true,
                    'visible_in_advanced_search' => true,
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                    'visible' => true,
                    'is_html_allowed_on_front' => false,
                    'visible_on_front' => true,
                    'filterable_in_search' => true,
                    'used_for_promo_rules' => true,
                    'used_for_sort_by' => true,
                    'option' => [
                        'values' => [
                            'Apple',
                            'Samsung',
                            'Sony',
                            'LG',
                            'Xiaomi',
                            'Huawei'
                        ]
                    ],
                    'system' => false,
                    'group' => self::ATTRIBUTE_GROUP,
                    'user_defined' => true
                ]
            );

            $attributeSetId = $eavSetup->getDefaultAttributeSetId(Product::ENTITY);
            $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Product::ENTITY, $attributeSetId);
            $attributeId = $eavSetup->getAttributeId(Product::ENTITY, BrandHelper::ATTRIBUTE_CODE_BRAND);

            $eavSetup->addAttributeToGroup(
                Product::ENTITY,
                $attributeSetId,
                $attributeGroupId,
                $attributeId,
                50
            );

            $this->eavConfig->clear();
        }
    }

    /**
     * @inheritDoc
     */
    public function revert()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Product::ENTITY, BrandHelper::ATTRIBUTE_CODE_BRAND);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
