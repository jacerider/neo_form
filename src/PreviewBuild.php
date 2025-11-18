<?php

namespace Drupal\neo_form;

use Drupal\Core\Render\Element;

/**
 * Defines an object that passes safe strings through the render system.
 *
 * This object should only be constructed with a known safe string. If there is
 * any risk that the string contains user-entered data that has not been
 * filtered first, it must not be used.
 *
 * @internal
 *   This object is marked as internal because it should only be used whilst
 *   rendering.
 *
 * @see \Drupal\Core\Template\TwigExtension::escapeFilter
 * @see \Twig\Markup
 */
final class PreviewBuild {

  /**
   * Builds the preview build.
   *
   * @param bool $isForm
   *   Whether the build is for a form or not. Defaults to TRUE.
   *
   * @return array
   *   The build array.
   */
  public static function build($isForm = TRUE): array {
    $build = [];

    $options = [
      'option1' => t('Option 1'),
      'option2' => t('Option 2'),
      'option3' => t('Option 3'),
    ];

    $optionStyles = [
      'inline' => t('Inline'),
      'inline_buttons' => t('Inline Buttons'),
      'inline_buttons_outline' => t('Inline Buttons Outline'),
      'inline_elements' => t('Inline Elements'),
      'grid_buttons' => t('Grid Buttons'),
    ];

    $sizes = [
      'md' => t('Medium'),
      'xs' => t('Extra Small'),
      'sm' => t('Small'),
      'lg' => t('Large'),
      'xl' => t('Extra Large'),
    ];

    if ($isForm) {
      $build['tabs'] = [
        '#type' => 'vertical_tabs',
      ];
    }

    $build['textfield'] = [
      '#type' => 'details',
      '#title' => t('Textfield'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    foreach ($sizes as $size => $label) {
      $build['textfield'][$size] = [
        '#type' => 'textfield',
        '#title' => t('Text Field (@size)', ['@size' => $label]),
        '#description' => t('This is a text field.'),
        '#placeholder' => t('Placeholder...'),
        '#default_value' => '',
        '#neo_size' => $size,
        '#required' => TRUE,
      ];
    }

    $build['checkbox'] = [
      '#type' => 'details',
      '#title' => t('Checkbox'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $build['checkbox']['reset'] = [
      '#type' => 'checkbox',
      '#title' => t('Checkbox (@size)', ['@size' => 'Reset']),
      '#description' => t('This is a checkbox.'),
      '#default_value' => FALSE,
      '#neo_style' => 'reset',
    ];

    foreach ($sizes as $size => $label) {
      $build['checkbox'][$size] = [
        '#type' => 'checkbox',
        '#title' => t('Checkbox (@size)', ['@size' => $label]),
        '#description' => t('This is a checkbox.'),
        '#default_value' => FALSE,
        '#neo_size' => $size,
      ];
    }

    $build['checkboxes'] = [
      '#type' => 'details',
      '#title' => t('Checkboxes'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    foreach ($sizes as $size => $label) {
      $build['checkboxes'][$size] = [
        '#type' => 'fieldset',
        '#title' => t('Checkboxes (@size)', ['@size' => $label]),
        '#neo_size' => $size,
      ];
      $build['checkboxes'][$size]['default'] = [
        '#type' => 'checkboxes',
        '#title' => t('Style: Default'),
        '#description' => t('This is a checkbox.'),
        '#options' => $options,
        '#default_value' => [key($options)],
        '#neo_size' => $size,
      ];

      foreach ($optionStyles as $style => $style_label) {
        $build['checkboxes'][$size][$style] = [
          '#type' => 'checkboxes',
          '#title' => t('Style: (@style)', ['@style' => $style_label]),
          '#description' => t('This is a checkbox.'),
          '#options' => $options,
          '#default_value' => [key($options)],
          '#neo_size' => $size,
          '#neo_style' => $style,
        ];
      }
    }

    $build['radios'] = [
      '#type' => 'details',
      '#title' => t('Radios'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    foreach ($sizes as $size => $label) {
      $build['radios'][$size] = [
        '#type' => 'fieldset',
        '#title' => t('Checkboxes (@size)', ['@size' => $label]),
        '#neo_size' => $size,
      ];
      $build['radios'][$size]['default'] = [
        '#type' => 'radios',
        '#title' => t('Style: Default'),
        '#description' => t('This is a checkbox.'),
        '#options' => $options,
        '#default_value' => key($options),
        '#neo_size' => $size,
      ];

      foreach ($optionStyles as $style => $style_label) {
        $build['radios'][$size][$style] = [
          '#type' => 'radios',
          '#title' => t('Style: (@style)', ['@style' => $style_label]),
          '#options' => $options,
          '#default_value' => key($options),
          '#neo_size' => $size,
          '#neo_style' => $style,
        ];
      }
    }

    $build['select'] = [
      '#type' => 'details',
      '#title' => t('Select'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];
    foreach ($sizes as $size => $label) {
      $build['select'][$size] = [
        '#type' => 'select',
        '#title' => t('Select (@size)', ['@size' => $label]),
        '#description' => t('This is a select element.'),
        '#options' => $options,
        '#empty_option' => t('- Select -'),
        '#neo_size' => $size,
      ];
    }

    $build['autocomplete'] = [
      '#type' => 'details',
      '#title' => t('Autocomplete'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];
    foreach ($sizes as $size => $label) {
      $build['autocomplete']['single_' . $size] = [
        '#title' => t('Single Autocomplete (@size)', ['@size' => $label]),
        '#type' => 'entity_autocomplete',
        '#description' => t('Autocomplete element'),
        '#target_type' => 'node',
        '#selection_handler' => 'default',
        '#neo_size' => $size,
      ];
    }
    foreach ($sizes as $size => $label) {
      $build['autocomplete']['multiple_' . $size] = [
        '#title' => t('Multiple Autocomplete (@size)', ['@size' => $label]),
        '#type' => 'entity_autocomplete',
        '#description' => t('Autocomplete element (multiple)'),
        '#target_type' => 'node',
        '#selection_handler' => 'default',
        '#tags' => TRUE,
        '#neo_size' => $size,
      ];
    }

    $build['containers'] = [
      '#type' => 'details',
      '#title' => t('Containers'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $build['containers']['details']['default'] = [
      '#type' => 'details',
      '#title' => t('Details'),
      '#field_prefix' => t('Field prefix can be provided.'),
      'content' => [
        '#markup' => t('<p>This is a details container.</p>'),
      ],
      'textfield' => [
        '#type' => 'textfield',
        '#title' => t('Text Field'),
        '#description' => t('This is a text field inside a details container.'),
        '#placeholder' => t('Placeholder...'),
        '#default_value' => '',
      ],
    ];

    foreach ($sizes as $size => $label) {
      $build['containers']['details'][$size] = [
        '#type' => 'details',
        '#title' => t('Details (@size)', ['@size' => $label]),
        '#neo_size' => $size,
        'content' => [
          '#markup' => t('<p>This is a details container.</p>'),
        ],
      ];
    }

    $build['containers']['fieldset']['no_title'] = [
      '#type' => 'fieldset',
      'content' => [
        '#markup' => t('<p>This is a fieldset without a title.</p>'),
      ],
    ];

    foreach ($sizes as $size => $label) {
      $build['containers']['fieldset'][$size] = [
        '#type' => 'fieldset',
        '#title' => t('Fieldset (@size)', ['@size' => $label]),
        '#neo_size' => $size,
        'content' => [
          '#markup' => t('<p>This is a fieldset container.</p>'),
        ],
      ];
    }

    $build['containers']['fieldset_with_legend'] = [
      '#type' => 'fieldset',
      '#title' => t('Fieldset (with legend content)'),
      '#description' => t('This is a fieldset container with legend content.'),
      '#description_display' => 'before',
      'checkbox_first' => [
        '#type' => 'checkbox',
        '#description' => t('This is a checkbox inside a fieldset container.'),
        '#default_value' => FALSE,
        '#neo_style' => 'reset',
        '#neo_region' => 'legend_start',
      ],
      'checkbox_end' => [
        '#type' => 'checkbox',
        '#title' => t('Checkbox End'),
        '#description' => t('This is a checkbox inside a fieldset container.'),
        '#default_value' => FALSE,
        '#neo_size' => 'xs',
        '#neo_region' => 'legend_end',
      ],
      'textfield' => [
        '#type' => 'textfield',
        '#title' => t('Text Field'),
        '#description' => t('This is a text field inside a details container.'),
        '#placeholder' => t('Placeholder...'),
        '#default_value' => '',
      ],
    ];

    $build['buttons'] = [
      '#type' => 'details',
      '#title' => t('Buttons'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $build['buttons']['group'] = [
      '#type' => 'actions',
      '#title' => t('Button Group'),
      '#attributes' => [
        'class' => ['btn-group'],
      ],
    ];

    $build['buttons']['group']['one'] = [
      '#type' => 'submit',
      '#value' => t('One'),
    ];

    $build['buttons']['group']['two'] = [
      '#type' => 'submit',
      '#value' => t('Two'),
    ];

    $build['buttons']['group']['three'] = [
      '#type' => 'submit',
      '#value' => t('Three'),
    ];

    $build['buttons']['actions'] = [
      '#type' => 'actions',
      '#attributes' => [
        'class' => ['grid grid-cols-5 gap-2'],
      ],
    ];

    foreach ([
      'btn',
      'btn-outline',
      'btn-reset',
      'btn-primary',
      'btn-secondary',
      'btn-accent',
      'btn-success',
      'btn-warning',
      'btn-alert',
      'btn-outline-primary',
      'btn-outline-secondary',
      'btn-outline-accent',
      'btn-outline-success',
      'btn-outline-warning',
      'btn-outline-alert',
      'btn-text',
      'btn-text-primary',
      'btn-text-secondary',
      'btn-text-accent',
      'btn-text-success',
      'btn-text-warning',
      'btn-text-alert',
    ] as $button_class) {
      foreach ([
        'btn-xs' => 'XS',
        'btn-sm' => 'SM',
        'btn-md' => 'MD',
        'btn-lg' => 'LG',
        'btn-xl' => 'XL',
      ] as $size => $label) {
        $build['buttons']['actions']['full_' . $button_class][$size] = [
          '#type' => 'submit',
          '#value' => t('@size: @button_class', [
            '@size' => $label,
            '@button_class' => '.' . $button_class . '.btn-' . $size,
          ]),
          // '#neo_size' => $size,
          // '#neo_style' => str_replace('btn-', '', $button_class),
          '#attributes' => [
            'class' => ['btn', $button_class, $size],
          ],
        ];
      }
    }

    $build['other'] = [
      '#type' => 'details',
      '#title' => t('Other'),
      '#tree' => TRUE,
      '#group' => 'tabs',
    ];

    $build['other']['textarea'] = [
      '#type' => 'textarea',
      '#title' => t('Text Area'),
      '#description' => t('This is a text area.'),
      '#required' => FALSE,
    ];

    $build['other']['file'] = [
      '#type' => 'file',
      '#title' => t('Upload a file'),
      '#description' => t('Upload a file to be included in the search.'),
      '#upload_location' => 'public://search_uploads/',
      '#required' => FALSE,
    ];

    $build['titles'] = [
      '#type' => 'details',
      '#title' => t('Titles'),
      '#tree' => TRUE,
    ];

    foreach ([
      'xs' => t('Extra Small'),
      'sm' => t('Small'),
      'md' => t('Medium'),
      'lg' => t('Large'),
      'xl' => t('Extra Large'),
      '2xl' => t('2X Large'),
      '3xl' => t('3X Large'),
    ] as $size => $label) {
      $build['titles'][$size] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['card', 'title-' . $size],
        ],
      ];
      $build['titles'][$size]['supertitle'] = [
        '#markup' => '<div class="component-supertitle">This is a ' . $label . ' supertitle</div>',
      ];
      $build['titles'][$size]['title'] = [
        '#markup' => '<h2 class="component-title">This is a ' . $label . ' title</h2>',
      ];
      $build['titles'][$size]['subtitle'] = [
        '#markup' => '<div class="component-subtitle">This is a ' . $label . ' subtitle</div>',
      ];
    }

    self::processElements($build);
    return $build;
  }

  /**
   * Processes the elements in the build array.
   *
   * @param array $build
   *   The build array to process.
   */
  protected static function processElements(array &$build): void {
    foreach (Element::children($build) as $key) {
      $element = &$build[$key];
      if (is_array($element)) {
        if (isset($element['#type'])) {
          $element['#id'] = $element['#id'] ?? 'neo-' . $key;
          $element['#value'] = $element['#value'] ?? NULL;
          $element['#title_display'] = $element['#title_display'] ?? 'before';
          $element['#description_display'] = $element['#description_display'] ?? 'after';
        }
        self::processElements($element);
      }
    }
  }

}
