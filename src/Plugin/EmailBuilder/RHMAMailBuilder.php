<?php

namespace Drupal\rhma\Plugin\EmailBuilder;

use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailBuilderBase;
use Drupal\symfony_mailer\Processor\TokenProcessorTrait;

/**
 * Creates an example for a hypothetical Promotional email type
 *
 * @EmailBuilder(
 *   id = "rhma",
 *   sub_types = {
 *     "rhma_promotional" = @Translation("Promotional"),
 *     "rhma_marketing" = @Translation("Marketing"),
 *   },
 *   common_adjusters = {"email_subject", "email_body"}
 * )
 */
class RHMAMailBuilder extends EmailBuilderBase {

  use TokenProcessorTrait;

  /**
   * Receive the parameters to work on email
   *
   * @param \Drupal\symfony_mailer\EmailInterface $email
   * @param $recipient_name
   *
   * @return void
   */
  public function createParams(EmailInterface $email, $recipient_name = NULL) {
    parent::createParams($email);
    $email->setParam('recipient_name', $recipient_name);
  }

  public function build(EmailInterface $email) {
    $recipient_name = $email->getParam('recipient_name');
    $email->setVariable('recipient_name', $recipient_name);

    if ($email->getSubType() == 'rhma_marketing') {
      $email->setSubject('This is a marketing email');
    }

  }


}
