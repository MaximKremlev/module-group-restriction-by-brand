<?php

declare(strict_types=1);

namespace MaximKremlev\GroupRestrictionByBrand\Plugin;

use Magento\Customer\Block\Adminhtml\Group\Edit\Form;
use MaximKremlev\GroupRestrictionByBrand\Model\Attribute\Source\Brand;
use MaximKremlev\GroupRestrictionByBrand\Model\ResourceModel\RestrictedBrands;
use Magento\Framework\App\RequestInterface;

/**
 * Plugin for adding brand restrictions field to customer group form
 *
 * Class AddFieldToCustomerGroupFormPlugin
 * @package MaximKremlev\GroupRestrictionByBrand\Plugin
 */
class AddFieldToCustomerGroupFormPlugin
{
    /**
     * Form field name for restricted brands selection
     */
    public const string FORM_FIELD_RESTRICTED_BRANDS = 'restricted_brands';

    /**
     * Form field label for restricted brands
     */
    private const string FORM_FIELD_LABEL = 'Restricted Brands';

    /**
     * @var Brand
     */
    private Brand $brandSource;

    /**
     * @var RestrictedBrands
     */
    private RestrictedBrands $restrictedBrands;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param Brand $brandSource
     * @param RestrictedBrands $restrictedBrands
     * @param RequestInterface $request
     */
    public function __construct(
        Brand $brandSource,
        RestrictedBrands $restrictedBrands,
        RequestInterface $request
    ) {
        $this->brandSource = $brandSource;
        $this->restrictedBrands = $restrictedBrands;
        $this->request = $request;
    }

    /**
     * Add brand multiselect field to customer group form
     *
     * Adds field for selecting restricted brands and handles loading of existing values.
     * Uses request parameter 'id' to determine if editing existing group.
     *
     * @param Form $subject
     * @param Form $result
     * @return Form
     */
    public function afterSetForm(Form $subject, Form $result): Form
    {
        $fieldset = $result->getForm()->getElement('base_fieldset');
        if ($fieldset) {
            $restrictedBrands = [];
            $brands = array_filter($this->brandSource->getAllOptions(), function ($option) {
                return $option['value'] !== '';
            });

            if ($groupId = (int)$this->request->getParam('id')) {
                $restrictedBrands = $this->restrictedBrands->getRestrictedBrandIds($groupId);
            }

            $fieldset->addField(
                self::FORM_FIELD_RESTRICTED_BRANDS,
                'multiselect',
                [
                    'name' => self::FORM_FIELD_RESTRICTED_BRANDS . '[]',
                    'label' => __(self::FORM_FIELD_LABEL),
                    'title' => __(self::FORM_FIELD_LABEL),
                    'required' => false,
                    'values' => $brands,
                    'value' => $restrictedBrands
                ]
            );

            $formValues = $result->getForm()->getValues();
            if (isset($formValues[self::FORM_FIELD_RESTRICTED_BRANDS])) {
                $result->getForm()->addValues([
                    self::FORM_FIELD_RESTRICTED_BRANDS => $formValues[self::FORM_FIELD_RESTRICTED_BRANDS]
                ]);
            } elseif (!empty($restrictedBrands)) {
                $result->getForm()->addValues([
                    self::FORM_FIELD_RESTRICTED_BRANDS => $restrictedBrands
                ]);
            }
        }

        return $result;
    }
}
