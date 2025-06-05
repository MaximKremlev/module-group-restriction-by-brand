<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Helper;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use MaximKremlev\GroupRestrictionByBrand\Model\RestrictedProducts;
use MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel\RestrictedBrands;

/**
 * Helper for brand restrictions functionality
 *
 * Class Data
 * @package MaximKremlev\GroupRestrictionByBrand\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Collection flag for brand filter
     */
    public const COLLECTION_FLAG_BRAND_FILTER_ADDED = 'brand_filter_added';

    /**
     * Brand attribute code
     */
    public const ATTRIBUTE_CODE_BRAND = 'brand';

    /**
     * Code for a collection flag
     */
    public const COLLECTION_FILTER_FLAG_CODE = 'restricted_customer_group_filter_applied';

    /**
     * Flag to skip brand restriction filter
     */
    public const SKIP_BRAND_RESTRICTION_FLAG = 'skip_brand_restriction';

    /**
     * @var HttpContext
     */
    private HttpContext $httpContext;

    /**
     * @var RestrictedBrands
     */
    private RestrictedBrands $restrictedBrands;

    /**
     * @var RestrictedProducts
     */
    private RestrictedProducts $restrictedProducts;

    /**
     * @param Context $context
     * @param HttpContext $httpContext
     * @param RestrictedBrands $restrictedBrands
     * @param RestrictedProducts $restrictedProducts
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        RestrictedBrands $restrictedBrands,
        RestrictedProducts $restrictedProducts
    ) {
        parent::__construct($context);

        $this->httpContext = $httpContext;
        $this->restrictedBrands = $restrictedBrands;
        $this->restrictedProducts = $restrictedProducts;
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * Get current customer group id
     *
     * @return int
     */
    public function getCurrentCustomerGroupId(): int
    {
        return (int)$this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }

    /**
     * Get restricted brand ids for current customer group
     *
     * @return array
     */
    public function getRestrictedBrandIds(): array
    {
        if (!$this->isCustomerLoggedIn()) {
            return [];
        }

        return $this->restrictedBrands->getRestrictedBrandIds(
            $this->getCurrentCustomerGroupId()
        );
    }

    /**
     * Get restricted product ids for current customer group
     *
     * @return array
     */
    public function getRestrictedProductIds(): array
    {
        if (!$this->isCustomerLoggedIn()) {
            return [];
        }

        if (!$restrictedBrands = $this->getRestrictedBrandIds()) {
            return [];
        }

        return $this->restrictedProducts->getRestrictedProductIds($restrictedBrands);
    }
}
