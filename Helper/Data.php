<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Helper;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
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
     * @var HttpContext
     */
    private HttpContext $httpContext;

    /**
     * @var RestrictedBrands
     */
    private RestrictedBrands $restrictedBrands;

    /**
     * @param Context $context
     * @param HttpContext $httpContext
     * @param RestrictedBrands $restrictedBrands
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        RestrictedBrands $restrictedBrands
    ) {
        parent::__construct($context);

        $this->httpContext = $httpContext;
        $this->restrictedBrands = $restrictedBrands;
    }

    /**
     * Check if customer is logged in
     *
     * Uses HttpContext to be compatible with page caching
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
     * Uses HttpContext to be compatible with page caching
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
}
