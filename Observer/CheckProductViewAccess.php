<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Observer;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NotFoundException;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;
use MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel\RestrictedBrands;

/**
 * Observer for checking product access based on brand restrictions
 *
 * Class CheckProductViewAccess
 * @package MaximKremlev\GroupRestrictionByBrand\Observer
 */
class CheckProductViewAccess implements ObserverInterface
{
    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var RestrictedBrands
     */
    private RestrictedBrands $restrictedBrands;

    /**
     * @param Session $customerSession
     * @param RestrictedBrands $restrictedBrands
     */
    public function __construct(
        Session $customerSession,
        RestrictedBrands $restrictedBrands
    ) {
        $this->customerSession = $customerSession;
        $this->restrictedBrands = $restrictedBrands;
    }

    /**
     * Check if product brand is restricted for current customer group
     *
     * @param Observer $observer
     * @return void
     * @throws NotFoundException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->customerSession->isLoggedIn()) {
            return;
        }

        $product = $observer->getEvent()->getProduct();
        $brandId = $product->getData(BrandHelper::ATTRIBUTE_CODE_BRAND);

        if (!$brandId) {
            return;
        }

        try {
            $restrictedBrands = $this->restrictedBrands->getRestrictedBrandIds(
                (int)$this->customerSession->getCustomerGroupId()
            );
        } catch (Exception) {
            $restrictedBrands = [];
        }

        if (in_array($brandId, $restrictedBrands, true)) {
            throw new NotFoundException(__('The product that was requested doesn\'t exist.'));
        }
    }
}
