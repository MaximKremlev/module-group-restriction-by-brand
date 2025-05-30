<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Plugin;

use Exception;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\App\RequestInterface;
use MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel\RestrictedBrands;

/**
 * Plugin for saving brand restrictions
 *
 * Class SaveRestrictedBrandsPlugin
 * @package MaximKremlev\GroupRestrictionByBrand\Plugin
 */
class SaveRestrictedBrandsPlugin
{
    /**
     * @var RestrictedBrands
     */
    private RestrictedBrands $restrictedBrands;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param RestrictedBrands $restrictedBrands
     * @param RequestInterface $request
     */
    public function __construct(
        RestrictedBrands $restrictedBrands,
        RequestInterface $request
    ) {
        $this->restrictedBrands = $restrictedBrands;
        $this->request = $request;
    }

    /**
     * Remove restricted brands data from group before saving
     *
     * Prevents database errors by removing brand data that doesn't belong in customer_group table
     *
     * @param GroupRepositoryInterface $subject
     * @param GroupInterface $group
     * @return array
     */
    public function beforeSave(
        GroupRepositoryInterface $subject,
        GroupInterface $group
    ): array {
        $extensionAttributes = $group->getExtensionAttributes();
        $extensionAttributes?->setRestrictedBrands([]);

        return [$group];
    }

    /**
     * Save restricted brands after customer group save
     *
     * @param GroupRepositoryInterface $subject
     * @param GroupInterface $result
     * @param GroupInterface $group
     * @return GroupInterface
     */
    public function afterSave(
        GroupRepositoryInterface $subject,
        GroupInterface $result,
        GroupInterface $group
    ): GroupInterface {
        try {
            $restrictedBrands = $this->request->getParam(
                AddFieldToCustomerGroupFormPlugin::FORM_FIELD_RESTRICTED_BRANDS,
                []
            );

            if (is_array($restrictedBrands)) {
                $this->restrictedBrands->saveRestrictedBrands(
                    (int)$result->getId(),
                    $restrictedBrands
                );
            }
        } catch (Exception) {}

        return $result;
    }
}
