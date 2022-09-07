<?php

namespace Drupal\rhma\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\symfony_mailer\EmailFactoryInterface;
use Drupal\symfony_mailer\MailerHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Rhma form.
 */
class MailerTestFrom extends FormBase {

  /**
   * The email factory service.
   *
   * @var \Drupal\symfony_mailer\EmailFactoryInterface
   */
  protected $emailFactory;

  /**
   * Constructs a new TestForm.
   *
   * @param \Drupal\symfony_mailer\EmailFactoryInterface $email_factory
   *   The email factory service.
   */
  public function __construct(EmailFactoryInterface $email_factory) {
    $this->emailFactory = $email_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('email_factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rhma_mailer_test_from';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#required' => FALSE,
    ];

    $form['recipient'] = [
      '#type' => 'email',
      '#title' => $this->t('Recipient'),
      '#required' => FALSE,
    ];

    $form['body'] = [
      '#type' => 'text_format',
      '#format' => $content['format'] ?? filter_default_format(),
      '#title' => $this->t('Body'),
      '#required' => TRUE,
    ];

    $form['type'] = [
      '#type' => 'radios',
      '#options' => [
        'marketing' => $this->t('Marketing'),
        'promotional' => $this->t('Promotional'),
      ],
      '#title' => $this->t('Type'),
      '#required' => TRUE,
      '#default_value' => 'promotional'
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $to = $form_state->getValue('recipient') ?: $this->currentUser();
    $body = $form_state->getValue('body');
    $subject = $form_state->getValue('subject');
    $fake_recipient = $to;

    $body['value'] .= 'some injected html like: <strong>a bold phrase</strong>';
    $message = ['#markup' => $body['value']];
    $sub_type = $form_state->getValue('type') == 'marketing' ? 'rhma_marketing' : 'rhma_promotional';


    $promotionalMail = $this
      ->emailFactory
      ->newTypedEmail('rhma', $sub_type, $fake_recipient)
      ->setSubject($subject)
      ->setBody($message)
      ->setTo($to);

    $promotionalMail->send();


    $this->messenger()->addStatus($this->t('The message has been sent.'));
  }

}
