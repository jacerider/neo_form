<?php

namespace Drupal\neo_form\Settings;

use Drupal\Core\Form\FormStateInterface;
use Drupal\neo_settings\Plugin\SettingsBase;

/**
 * Module settings.
 *
 * @Settings(
 *   id = "neo_form",
 *   label = @Translation("Form"),
 *   config_name = "neo_form.settings",
 *   menu_title = @Translation("Forms"),
 *   route = "/admin/config/neo/form",
 *   admin_permission = "administer neo_form",
 * )
 */
class FormSettings extends SettingsBase {
  /**
   * {@inheritdoc}
   *
   * The `status` value is a list, so clearing it must replace the stored
   * value rather than deep-merge with it. key([]) is NULL, so
   * mergeDeepStrict() would otherwise recurse and swallow the empty array,
   * silently restoring the previous selection.
   */
  protected $strictParents = [
    ['status'],
  ];


  /**
   * {@inheritdoc}
   *
   * Instance settings are settings that are set both in the base form and the
   * variation form. They are editable in both forms and the values are merged
   * together.
   */
  protected function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\Core\Extension\ThemeHandlerInterface $themeHandler */
    $themeHandler = \Drupal::service('theme_handler');
    $themes = $themeHandler->listInfo();
    // Sort options array with 'front' and 'back' themes first if those keys
    // exist.
    $front = $themes['front'] ?? NULL;
    $back = $themes['back'] ?? NULL;
    $themes = array_filter($themes, static fn($key) => !in_array($key, ['front', 'back']), ARRAY_FILTER_USE_KEY);
    $themes = array_merge(['front' => $front, 'back' => $back], $themes);
    $options = array_map(
      static fn($theme) => $theme->info['name'],
      $themes
    );

    $form['status'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Themes'),
      '#neo_style' => 'inline_elements',
      '#options' => $options,
      '#default_value' => array_keys($this->getValue('status', [])),
    ];

    $form['tabs'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['themes'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    foreach ($themes as $theme) {
      $themeId = $theme->getName();
      $themeLabel = $theme->info['name'] ?? $themeId;

      if ($this->getValue(['status', $themeId]) !== TRUE) {
        continue;
      }
      $subform = [];

      $subform['form'] = [
        '#type' => 'details',
        '#title' => $this->t('Form'),
        '#open' => FALSE,
      ];

      $subform['form']['spacing'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Spacing'),
        '#description' => $this->t('The spacing inside a form in px or rem. Example: 1.5rem'),
        '#default_value' => $this->getValue(['themes', $themeId, 'form', 'spacing']),
      ];

      $subform['item'] = [
        '#type' => 'details',
        '#title' => $this->t('Form Item'),
        '#open' => FALSE,
      ];
      foreach ([
        'base_color' => $this->t('Base Color'),
        'base_50_color' => $this->t('Base 50 Color'),
        'base_100_color' => $this->t('Base 100 Color'),
        'base_200_color' => $this->t('Base 200 Color'),
        'primary_color' => $this->t('Primary Color'),
        'primary_active_color' => $this->t('Primary Active Color'),
        'accent_color' => $this->t('Accent Color'),
        'content_color' => $this->t('Content/Input Color'),
        'label_color' => $this->t('Label Color'),
        'description_color' => $this->t('Description Color'),
        'placeholder_color' => $this->t('Placeholder Color'),
        'ring_color' => $this->t('Ring Color'),
        'border_color' => $this->t('Border Color'),
        'border_color_hover' => $this->t('Border Hover Color'),
        'border_color_focus' => $this->t('Border Focus Color'),
      ] as $key => $label) {
        $defaultValue = $this->getValue(['themes', $themeId, 'item', $key]);
        $subform['item'][$key] = [
          '#type' => 'neo_color',
          '#title' => $label,
          '#default_value' => $defaultValue === 'transparent' ? NULL : $defaultValue,
          '#empty_option' => $this->t('Transparent'),
        ];
      }

      $subform['item']['border_width'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Border Width'),
        '#description' => $this->t('The width of the border in px or rem. Example: 2px or 0.25rem'),
        '#default_value' => $this->getValue(['themes', $themeId, 'item', 'border_width']),
      ];

      $subform['item']['border_radius'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Border Radius'),
        '#description' => $this->t('The radius of the border corners in px or rem. Example: 4px or 0.25rem'),
        '#default_value' => $this->getValue(['themes', $themeId, 'item', 'border_radius']),
      ];

      $subform['item']['spacing'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Spacing'),
        '#description' => $this->t('The spacing between form elements in px or rem. Example: 1.5rem'),
        '#default_value' => $this->getValue(['themes', $themeId, 'item', 'spacing']),
      ];

      $subform['item']['inner_spacing'] = [
        '#type' => 'number',
        '#title' => $this->t('Inner Spacing Multiplier'),
        '#description' => $this->t('The multiplier for the inner spacing of form elements. This value is multiplied by the base spacing to determine the inner spacing. Example: 2'),
        '#default_value' => $this->getValue(['themes', $themeId, 'item', 'inner_spacing']),
        '#step' => 0.1,
        '#min' => 0,
      ];

      foreach ([
        'btn' => $this->t('Button: Default'),
        'btn_primary' => $this->t('Button: Primary'),
        'btn_secondary' => $this->t('Button: Secondary'),
        'btn_accent' => $this->t('Button: Accent'),
      ] as $btnKey => $btnLabel) {
        $subform[$btnKey] = [
          '#type' => 'details',
          '#title' => $btnLabel,
          '#open' => FALSE,
        ];
        if ($btnKey === 'btn') {
          $subform[$btnKey]['border_radius'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Border Radius'),
            '#description' => $this->t('The radius of the border corners in px or rem. Example: 4px or 0.25rem'),
            '#default_value' => $this->getValue(['themes', $themeId, $btnKey, 'border_radius']),
          ];
        }
        foreach ([
          'bg_color' => $this->t('Color'),
          'bg_color_hover' => $this->t('Hover Color'),
          'border_color' => $this->t('Border Color'),
          'border_color_hover' => $this->t('Border Hover Color'),
        ] as $key => $label) {
          $subform[$btnKey][$key] = [
            '#type' => 'neo_color',
            '#title' => $label,
            '#default_value' => $this->getValue(['themes', $themeId, $btnKey, $key]),
            '#empty_option' => $this->t('Transparent'),
          ];
        }
      }

      $form['themes'][$themeId] = [
        '#type' => 'details',
        '#title' => $this->t('@theme Theme', ['@theme' => $themeLabel]),
        '#open' => $themeId === 'front',
        '#group' => implode('][', $form['#parents']) . '][tabs',
      ] + $subform;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSettingsForm(array $form, FormStateInterface $form_state) {
    $enabled = array_filter($form_state->getValue('status'));
    $enabled = array_map(
      static fn($item) => TRUE,
      $enabled
    );
    $form_state->setValue('status', $enabled);

    $settings = $form_state->getValue('themes', []);
    $settings = array_intersect_key($settings, $enabled);
    foreach ($settings as $themeId => $themeSettings) {
      foreach ($themeSettings as $groupKey => $group) {
        foreach ($group as $key => $value) {
          if (empty($value) && str_contains($key, 'color')) {
            $settings[$themeId][$groupKey][$key] = 'transparent';
          }
        }
      }
    }

    $form_state->setValue('themes', $settings);

    parent::validateSettingsForm($form, $form_state);
  }

}
