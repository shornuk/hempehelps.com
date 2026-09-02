---
paths:
  - '.github/workflows/**'
---

# Workflows

## Flux Pro CI auth
CI must authenticate to composer.fluxui.dev before composer setup. Configure FLUX_USERNAME (account email) and FLUX_LICENSE_KEY as GitHub Actions repository secrets, then run `composer config http-basic.composer.fluxui.dev` in the workflow. auth.json is gitignored and is not available on GitHub.
