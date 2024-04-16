<?php

namespace Drupal\rsvp_form_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * Implements a custom form block form.
 */
class CustomFormBlockForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvp_form_data_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get the current node, if available.
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $node_id = $node->id();

      $author_id = $node->getOwnerId();
      // Load the user object.
      $author = \Drupal\user\Entity\User::load($author_id);
      if ($author) {
        // Get the roles of the user.
        $roles = $author->getRoles();
      }
    }

     // Get the current user object.
     $current_user = \Drupal::currentUser();
     $user_email = $current_user->getEmail();
     // Get current user's name.
     $user_name = $current_user->getAccountName();
     // Get current user's ID.
     $user_id = $current_user->id();
 
     $user_role = $current_user->getRoles();
     $roles = isset($roles) ? $roles : [];

     if ($user_role == $roles) {


    $form['markup'] = [
      '#markup' => '<h2>Are you attending the Event.</h2>',
    ];

    $form['status'] = [
      '#type' => 'select',
      '#title' => $this->t('RSVP Status'),
      '#options' => [
        'yes' => $this->t('Yes, I will attend'),
        'no' => $this->t('No, I cannot attend'),
        'maybe' => $this->t('Maybe'),
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
  }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get the current user object.
    $current_user = \Drupal::currentUser();
    $user_email = $current_user->getEmail();

    // Get the current node, if available.
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $node_id = $node->id();
    }
    else {
      $node_id = NULL;
    }

    // Check if the combination of user email and node ID already exists.
    $existing_entry = \Drupal::database()->select('rsvp_status_data', 'rsd')
      ->fields('rsd', ['id'])
      ->condition('email', $user_email)
      ->condition('node_id', $node_id)
      ->execute()
      ->fetchField();

    if (!empty($existing_entry)) {
        //drupal_set_message($this->t('Your response for this node has been updated.'), 'status');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve form values.
    $values = $form_state->getValues();

    // Get the current user object.
    $current_user = \Drupal::currentUser();
    $user_email = $current_user->getEmail();
    // Get current user's name.
    $user_name = $current_user->getAccountName();
    // Get current user's ID.
    $user_id = $current_user->id();

    //echo '<pre>',print_r($user_email),'</pre>'; exit();


    // Get the current node, if available.
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $node_id = $node->id();
    }
    else {
      $node_id = NULL;
    }

     // Check if the combination of user email and node ID already exists.
     $existing_entry = \Drupal::database()->select('rsvp_status_data', 'rsd')
     ->fields('rsd', ['id'])
     ->condition('email', $user_email)
     ->condition('node_id', $node_id)
     ->execute()
     ->fetchField();

   // If entry exists, update the existing record. Otherwise, insert a new record.
   if (!empty($existing_entry)) {
     \Drupal::database()->update('rsvp_status_data')
       ->fields([
         'status' => $values['status'],
         'uname' => $user_name,
         'uid' => $user_id,
       ])
       ->condition('email', $user_email)
       ->condition('node_id', $node_id)
       ->execute();
   }
   else{
    // Insert data into database.
    \Drupal::database()->insert('rsvp_status_data')
      ->fields([
        'email' => $user_email,
        'uname' => $user_name,
        'uid' => $user_id,
        'status' => $values['status'],
        'node_id' => $node_id,
      ])
      ->execute();
      }
    // Clear form values after submission.
    $form_state->setValues([]);
  }

}
