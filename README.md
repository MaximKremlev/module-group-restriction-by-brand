# Product Restriction by Brand for Customer Group

## Overview
This Magento 2 module allows restricting product visibility based on brand attribute for specific customer groups.

## Features
- Restrict access to products of specific brands for customer groups
- Multi-select interface in customer group admin form
- Automatic product filtering in catalog and search results

## Requirements
- Magento 2.4.7 or higher
- PHP 8.1 or higher

## Installation

### Using Composer (recommended)
```bash
composer config repositories.MaximKremlev vcs ssh://git@github.com/MaximKremlev/module-group-restriction-by-brand.git
composer require maximkremlev/module-group-restriction-by-brand
bin/magento module:enable MaximKremlev_GroupRestrictionByBrand
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

### Manual Installation (not recommended)
1. Create directory `app/code/MaximKremlev/GroupRestrictionByBrand`
2. Clone this repository into that directory
3. Run the following commands:
```bash
bin/magento module:enable MaximKremlev_GroupRestrictionByBrand
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

## Configuration
1. Navigate to Admin > Customers > Customer Groups
2. Edit a customer group
3. In the "Restricted Brands" field, select brands that should be restricted for this group
4. Save the customer group
5. Clean cache

## How It Works
- The module adds a multiselect field to the customer group form for selecting restricted brands
- When a customer from a restricted group browses the catalog:
  - Products with restricted brands are filtered out from collection
  - Products without brand value are still shown
  - Filtering happens before collection load for optimal performance

