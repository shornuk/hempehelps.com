if (!customElements.get('purchase-options')) {
  customElements.define(
    'purchase-options',
    class PurchaseOptions extends HTMLElement {
      constructor() {
        super();
        this.savedPriceHTML = null;
        this.onPlanChange = this.onPlanChange.bind(this);
        this.onVariantChange = this.onVariantChange.bind(this);
      }

      connectedCallback() {
        this.addEventListener('change', this.onPlanChange);
        this.variantSelects = this.closest('variant-selects');
        if (this.variantSelects) this.variantSelects.addEventListener('change', this.onVariantChange);
      }

      disconnectedCallback() {
        this.removeEventListener('change', this.onPlanChange);
        if (this.variantSelects) this.variantSelects.removeEventListener('change', this.onVariantChange);
      }

      onPlanChange(event) {
        if (!event.target.matches('[data-purchase-option]')) return;
        // Keep VariantSelects from treating this as a variant change.
        event.stopPropagation();
        this.applyMode(event.target.value === 'subscribe');
      }

      onVariantChange(event) {
        if (event.target.matches('[data-purchase-option]')) return;
        this.reset();
      }

      reset() {
        const oneTime = this.querySelector('[data-purchase-option][value="onetime"]');
        if (oneTime) oneTime.checked = true;
        this.applyMode(false);
      }

      applyMode(subscribe) {
        const section = this.dataset.section;
        const buttons = document.querySelector(`#product-form-${section} .product-form__buttons`);
        const cta = document.getElementById(`SubscriptionCta-${section}`);
        const ctaLink = cta && cta.querySelector('.subscription-cta__button');
        const ctaLabel = cta && cta.querySelector('.subscription-cta__label');
        const price = document.getElementById(`price-${section}`);
        const priceTemplate = this.querySelector('template[data-subscription-price]');

        if (subscribe) {
          if (ctaLink) ctaLink.href = this.dataset.url;
          if (ctaLabel) ctaLabel.textContent = this.dataset.ctaLabel;
          if (price && priceTemplate) {
            if (this.savedPriceHTML === null) this.savedPriceHTML = price.innerHTML;
            price.innerHTML = priceTemplate.innerHTML;
          }
        } else if (price && this.savedPriceHTML !== null) {
          price.innerHTML = this.savedPriceHTML;
          this.savedPriceHTML = null;
        }

        if (buttons) buttons.classList.toggle('hidden', subscribe);
        if (cta) cta.classList.toggle('hidden', !subscribe);
      }
    }
  );
}
