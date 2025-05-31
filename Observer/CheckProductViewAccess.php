<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use MaximKremlev\GroupRestrictionByBrand\Helper\Data as BrandHelper;

/**
 * Observer for checking product access based on brand restrictions
 *
 * Class CheckProductViewAccess
 * @package MaximKremlev\GroupRestrictionByBrand\Observer
 */
class CheckProductViewAccess implements ObserverInterface
{
    /**
     * @var BrandHelper
     */
    private BrandHelper $brandHelper;

    /**
     * @param BrandHelper $brandHelper
     */
    public function __construct(
        BrandHelper $brandHelper
    ) {
        $this->brandHelper = $brandHelper;
    }

    /**
     * Check if product brand is restricted for current customer group
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->brandHelper->isCustomerLoggedIn()) {
            return;
        }

        $product = $observer->getEvent()->getProduct();

        if (!$brandId = $product->getData(BrandHelper::ATTRIBUTE_CODE_BRAND)) {
            return;
        }

        if (in_array($brandId, $this->brandHelper->getRestrictedBrandIds(), true)) {
            throw new NoSuchEntityException(__('The product that was requested doesn\'t exist.'));
        }
    }
}
