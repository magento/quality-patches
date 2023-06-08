# Quality Patches Tool

Welcome to the Quality Patches Tool!

## Overview

Quality Patches Tool is a command-line tool that delivers quality patches for Adobe Commerce and Magento Open Source. It allows you to:
- view the general information about the latest patches 
- apply patches
- revert  previously applied patches

Quality patches are provided by Adobe support and Magento OS community.

Here is [a full list of available patches](https://devdocs.magento.com/quality-patches/tool.html#patch-grid) in Quality Patches Tool.

## Installation 

**On-Prem Project**

```$ composer require magento/quality-patches```

**Cloud Project**

The [quality-patches](https://github.com/magento/quality-patches) package is a dependency for the [ece-tools](https://github.com/magento/ece-tools/) package starting from v.2002.1.2 and is installed or updated when you [update the ece-tools package version](https://devdocs.magento.com/cloud/project/ece-tools-update.html).

## Usage - On-Prem Project
> Make sure to test all patches in a pre-production environment. For the changes to be reflected, refresh the cache in the [Admin under System > Tools > Cache Management](https://docs.magento.com/user-guide/system/cache-management.html?_ga=2.172766563.1151974537.1596126236-1202073513.1559691283)
>
> Use ```$ ./vendor/bin/magento-patches``` script

**Status command**

Show information about available patches for current Magento version:

```$ ./vendor/bin/magento-patches status```

**Apply command**

Applies provided list of patches:

```$ ./vendor/bin/magento-patches apply MAGETWO-95591 MAGETWO-67097```

**Revert command**

Reverts provided list of patches:

```$ ./vendor/bin/magento-patches revert MAGETWO-95591 MAGETWO-67097```

Reverts all patches:

```$ ./vendor/bin/magento-patches revert --all```

## Usage - Cloud Project
> Make sure to test all patches in a pre-production environment. For Magento Cloud, new branches can be created with magento-cloud environment:branch <branch-name>
>
> Use ```$ ./vendor/bin/ece-patches``` script

### Applying a patch
Add to .magento.env.yaml environment variable QUALITY_PATCHES with a list of patches to apply:
```
stage:
    build:
        QUALITY_PATCHES:
            - MCTEST-1002
            - MCTEST-1003
```
Commit and push updated .magento.env.yaml file into the remote branch. Patches will be applied during deploy process.

### Apply patches manually in a local environment
You can apply patches manually in a local environment and test them before you deploy.

To apply patches manually:
1. Add to .magento.env.yaml environment variable QUALITY_PATCHES with a list of patches to apply
2. From the project root, apply the patches:
 `$ ./vendor/bin/ece-patches apply`
 Patches will be applied in the following order:
   - Cloud-required patches
   - Quality patches from .magento.env.yaml
   - Custom patches from the /m2-hotfixes directory.
3. Check with `./vendor/bin/ece-patches status` if the patch was applied 
4.  Clear the Magento cache `$ ./bin/magento cache:clean`

Test the patches, make any necessary changes to custom patches.

### Revert patches in a local environment
You can revert patches in a local environment to clean instance:

```$ ./vendor/bin/ece-patches revert```

Patches will be reverted in the following order:
- Custom patches from the /m2-hotfixes directory.
- Magento-quality patches
- Cloud-required patches

## Status command information
**Status:**
- *Applied* - the patch is already applied
- *Not applied* - the patch is not applied 
- *N/A* - if the status of patch cannot be defined due to some conflicts

**Type:**
- *Optional* - all patches from [Quality Patches Tool](https://github.com/magento/quality-patches)  are optional for Cloud & On-Prem customers
- *Required* - all patches from [Cloud Patches](https://github.com/magento/magento-cloud-patches) are required for Cloud and optional for On-Prem customers
- *Deprecated* - patch is marked as deprecated (there is a recommendation to revert if it was applied)
- *Custom* - customer specific patches from m2-hotfixes folder (Cloud only)

**Details:**
- *Affected components* - show the list of affected components (magento-modules)
- *Required patches* - shows the list of required patches (dependencies)
- *Recommended replacement* - patch, that is recommended for replacement of deprecated patch 
