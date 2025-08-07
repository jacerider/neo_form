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
      'inline_elements' => t('Inline Elements'),
      'grid_buttons' => t('Grid Buttons'),
    ];

    $sizes = [
      'xs' => t('Extra Small'),
      'sm' => t('Small'),
      'md' => t('Medium'),
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
        '#options' => $options,
        '#empty_option' => t('- Select -'),
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
        '#neo_fieldset_region' => 'legend_start',
      ],
      'checkbox_end' => [
        '#type' => 'checkbox',
        '#title' => t('Checkbox End'),
        '#description' => t('This is a checkbox inside a fieldset container.'),
        '#default_value' => FALSE,
        '#neo_size' => 'xs',
        '#neo_fieldset_region' => 'legend_end',
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

    $build['buttons']['actions'] = [
      '#type' => 'actions',
      '#attributes' => [
        'class' => ['grid grid-cols-5 gap-2'],
      ],
    ];

    // We utilize 'BTN' as a replacement for 'btn' so that tailwind compile all
    // of this.
    foreach ([
      'BTN',
      'BTN-outline',
      'BTN-reset',
      'BTN-primary',
      'BTN-secondary',
      'BTN-accent',
      'BTN-success',
      'BTN-warning',
      'BTN-alert',
      'BTN-primary-outline',
      'BTN-secondary-outline',
      'BTN-accent-outline',
      'BTN-success-outline',
      'BTN-warning-outline',
      'BTN-alert-outline',
      'BTN-text',
      'BTN-text-primary',
      'BTN-text-secondary',
      'BTN-text-accent',
      'BTN-text-success',
      'BTN-text-warning',
      'BTN-text-alert',
    ] as $button_class) {
      $button_class = str_replace('BTN', 'btn', $button_class);
      foreach ($sizes as $size => $label) {
        $build['buttons']['actions'][$button_class][$size] = [
          '#type' => 'submit',
          '#value' => t('@size: @button_class', [
            '@size' => $label,
            '@button_class' => '.' . $button_class . '.btn-' . $size,
          ]),
          '#neo_size' => $size,
          '#neo_style' => str_replace('btn-', '', $button_class),
          '#attributes' => [
            'class' => ['btn'],
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
          $element['#description_display'] = $element['#description_display'] ?? 'before';
        }
        self::processElements($element);
      }
    }
  }

}
