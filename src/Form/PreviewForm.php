<?php

declare(strict_types=1);

namespace Drupal\neo_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Neo | Form form.
 */
final class PreviewForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'neo_form_preview';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $options = [
      'option1' => $this->t('Option 1'),
      'option2' => $this->t('Option 2'),
      'option3' => $this->t('Option 3'),
    ];

    $optionStyles = [
      'inline' => $this->t('Inline'),
      'inline_buttons' => $this->t('Inline Buttons'),
      'inline_elements' => $this->t('Inline Elements'),
      'grid_buttons' => $this->t('Grid Buttons'),
    ];

    $sizes = [
      'xs' => $this->t('Extra Small'),
      'sm' => $this->t('Small'),
      'md' => $this->t('Medium'),
      'lg' => $this->t('Large'),
      'xl' => $this->t('Extra Large'),
    ];

    $form['tabs'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['textfield'] = [
      '#type' => 'details',
      '#title' => $this->t('Textfield'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    foreach ($sizes as $size => $label) {
      $form['textfield'][$size] = [
        '#type' => 'textfield',
        '#title' => $this->t('Text Field (@size)', ['@size' => $label]),
        '#description' => $this->t('This is a text field.'),
        '#placeholder' => $this->t('Placeholder...'),
        '#default_value' => '',
        '#neo_size' => $size,
        '#required' => TRUE,
      ];
    }

    $form['checkbox'] = [
      '#type' => 'details',
      '#title' => $this->t('Checkbox'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $form['checkbox']['simple'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Checkbox (@size)', ['@size' => 'Simple']),
      '#description' => $this->t('This is a checkbox.'),
      '#default_value' => FALSE,
      '#neo_style' => 'simple',
    ];

    foreach ($sizes as $size => $label) {
      $form['checkbox'][$size] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Checkbox (@size)', ['@size' => $label]),
        '#description' => $this->t('This is a checkbox.'),
        '#default_value' => FALSE,
        '#neo_size' => $size,
      ];
    }

    $form['checkboxes'] = [
      '#type' => 'details',
      '#title' => $this->t('Checkboxes'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    foreach ($sizes as $size => $label) {
      $form['checkboxes'][$size] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Checkboxes (@size)', ['@size' => $label]),
      ];
      $form['checkboxes'][$size]['default'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Style: Default'),
        '#description' => $this->t('This is a checkbox.'),
        '#options' => $options,
        '#default_value' => [key($options)],
        '#neo_size' => $size,
      ];

      foreach ($optionStyles as $style => $style_label) {
        $form['checkboxes'][$size][$style] = [
          '#type' => 'checkboxes',
          '#title' => $this->t('Style: (@style)', ['@style' => $style_label]),
          '#options' => $options,
          '#default_value' => [key($options)],
          '#neo_size' => $size,
          '#neo_style' => $style,
        ];
      }
    }

    $form['radios'] = [
      '#type' => 'details',
      '#title' => $this->t('Radios'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    foreach ($sizes as $size => $label) {
      $form['radios'][$size] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Checkboxes (@size)', ['@size' => $label]),
      ];
      $form['radios'][$size]['default'] = [
        '#type' => 'radios',
        '#title' => $this->t('Style: Default'),
        '#description' => $this->t('This is a checkbox.'),
        '#options' => $options,
        '#default_value' => key($options),
        '#neo_size' => $size,
      ];

      foreach ($optionStyles as $style => $style_label) {
        $form['radios'][$size][$style] = [
          '#type' => 'radios',
          '#title' => $this->t('Style: (@style)', ['@style' => $style_label]),
          '#options' => $options,
          '#default_value' => key($options),
          '#neo_size' => $size,
          '#neo_style' => $style,
        ];
      }
    }

    $form['select'] = [
      '#type' => 'details',
      '#title' => $this->t('Select'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];
    foreach ($sizes as $size => $label) {
      $form['select'][$size] = [
        '#type' => 'select',
        '#title' => $this->t('Select (@size)', ['@size' => $label]),
        '#options' => $options,
        '#empty_option' => $this->t('- Select -'),
        '#neo_size' => $size,
      ];
    }

    $form['containers'] = [
      '#type' => 'details',
      '#title' => $this->t('Containers'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $form['containers']['details']['default'] = [
      '#type' => 'details',
      '#title' => $this->t('Details'),
      '#field_prefix' => $this->t('Field prefix can be provided.'),
      'content' => [
        '#markup' => $this->t('<p>This is a fieldset container.</p>'),
      ],
      'textfield' => [
        '#type' => 'textfield',
        '#title' => $this->t('Text Field'),
        '#description' => $this->t('This is a text field inside a details container.'),
        '#placeholder' => $this->t('Placeholder...'),
        '#default_value' => '',
      ],
    ];

    foreach ($sizes as $size => $label) {
      $form['containers']['details'][$size] = [
        '#type' => 'details',
        '#title' => $this->t('Fieldset (@size)', ['@size' => $label]),
        '#neo_size' => $size,
        'content' => [
          '#markup' => $this->t('<p>This is a fieldset container.</p>'),
        ],
      ];
    }

    $form['containers']['fieldset']['no_title'] = [
      '#type' => 'fieldset',
      'content' => [
        '#markup' => $this->t('<p>This is a fieldset without a title.</p>'),
      ],
    ];

    foreach ($sizes as $size => $label) {
      $form['containers']['fieldset'][$size] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Fieldset (@size)', ['@size' => $label]),
        '#neo_size' => $size,
        'content' => [
          '#markup' => $this->t('<p>This is a fieldset container.</p>'),
        ],
      ];
    }

    $form['containers']['fieldset_with_legend'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Fieldset (with legend content)'),
      '#description' => $this->t('This is a fieldset container with legend content.'),
      '#description_display' => 'before',
      'checkbox_first' => [
        '#type' => 'checkbox',
        '#description' => $this->t('This is a checkbox inside a fieldset container.'),
        '#default_value' => FALSE,
        '#neo_style' => 'simple',
        '#neo_fieldset_region' => 'legend_start',
      ],
      'checkbox_end' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Checkbox End'),
        '#description' => $this->t('This is a checkbox inside a fieldset container.'),
        '#default_value' => FALSE,
        '#neo_size' => 'xs',
        '#neo_fieldset_region' => 'legend_end',
      ],
      'textfield' => [
        '#type' => 'textfield',
        '#title' => $this->t('Text Field'),
        '#description' => $this->t('This is a text field inside a details container.'),
        '#placeholder' => $this->t('Placeholder...'),
        '#default_value' => '',
      ],
    ];

    $form['buttons'] = [
      '#type' => 'details',
      '#title' => $this->t('Buttons'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $form['buttons']['actions'] = [
      '#type' => 'actions',
      '#attributes' => [
        'class' => ['grid grid-cols-2 gap-2'],
      ],
    ];

    foreach ([
      'btn',
      'btn-outline',
      'btn-primary',
      'btn-primary-outline',
      'btn-secondary',
      'btn-secondary-outline',
      'btn-accent',
      'btn-accent-outline',
      'btn-success',
      'btn-success-outline',
      'btn-warning',
      'btn-warning-outline',
      'btn-alert',
      'btn-alert-outline',
      'btn-text',
      'btn-text-primary',
      'btn-text-secondary',
      'btn-text-accent',
      'btn-text-success',
      'btn-text-warning',
      'btn-text-alert',
    ] as $button_class) {
      $form['buttons']['actions'][$button_class] = [
        '#type' => 'submit',
        '#value' => $button_class,
        '#attributes' => [
          'class' => ['btn', $button_class],
        ],
      ];
    }

    $form['other'] = [
      '#type' => 'details',
      '#title' => $this->t('Other'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $form['other']['textarea'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Text Area'),
      '#description' => $this->t('This is a text area.'),
      '#required' => FALSE,
    ];

    $form['other']['file'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload a file'),
      '#description' => $this->t('Upload a file to be included in the search.'),
      '#upload_location' => 'public://search_uploads/',
      '#required' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $form_state->setRedirect('<front>');
  }

}
