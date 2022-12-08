<?php

namespace Drupal\commerce_paypal\Plugin\Commerce\PaymentMethodType;

use Drupal\commerce_payment\Entity\PaymentMethodInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentMethodType\CreditCard;

/**
 * Provides the PayPal Checkout payment method type.
 *
 * @CommercePaymentMethodType(
 *   id = "paypal_checkout",
 *   label = @Translation("PayPal Checkout"),
 *   create_label = @Translation("PayPal"),
 * )
 */
class PayPalCheckout extends CreditCard {

  /**
   * {@inheritdoc}
   */
  public function buildLabel(PaymentMethodInterface $payment_method) {
    if ($payment_method->hasField('card_type') && !$payment_method->get('card_type')->isEmpty()) {
      return parent::buildLabel($payment_method);
    }
    return $this->t('PayPal');
  }

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    // Return an empty array of fields to ensure these do not get removed when
    // commerce_paypal is uninstalled.
    // The credit card fields are already installed/defined by commerce_payment.
    return [];
  }

}
