<?php

namespace Drupal\about_us\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Class AboutUsForm.
 */
class AboutUsForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'about_us_form';
  }

  protected function getEditableConfigNames()
  {
    return ['about_us.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('about_us.settings');
    $form['#tree'] = TRUE;

    $num_leaders = $form_state->get('num_leaders');
    if ($num_leaders === NULL) {
      $num_leaders = 1;
      $form_state->set('num_leaders', $num_leaders);
    }

    $form['leaders'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Leaders'),
      '#prefix' => '<div id="leaders-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_leaders; $i++) {
      $form['leaders'][$i]['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Name'),
      ];

      $form['leaders'][$i]['designation'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Designation'),
      ];

      $form['leaders'][$i]['linkedin'] = [
        '#type' => 'url',
        '#title' => $this->t('LinkedIn Profile'),
      ];

      $form['leaders'][$i]['image'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Profile Image'),
        '#upload_location' => 'public://profile_images/',
      ];
    }

    $form['leaders']['actions'] = [
      '#type' => 'actions',
    ];

    $form['leaders']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'leaders-wrapper',
      ],
    ];

    $form['news_anchor'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'filter' => [
          'type' => 'role',
          'role' => ['news_anchor'],
        ],
      ],
      '#title' => $this->t('News Anchor'),
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  public function addOne(array $form, FormStateInterface $form_state)
  {
    $num_leaders = $form_state->get('num_leaders');
    $add_button = $num_leaders + 1;
    $form_state->set('num_leaders', $add_button);
    $form_state->setRebuild();
  }

  public function addmoreCallback(array &$form, FormStateInterface $form_state)
  {
    return $form['leaders'];
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValues();


    $news_anchor = User::load($values['news_anchor']);

    $news_anchor_name = $news_anchor->getDisplayName();


    $this->config('about_us.settings')
      ->set('leaders', $values['leaders'])
      ->set('news_anchor', ['id' => $values['news_anchor'], 'name' => $news_anchor_name])
      ->save();
  }
}
