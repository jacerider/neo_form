<?php

declare(strict_types=1);

namespace Drupal\neo_form\EventSubscriber;

use Drupal\neo_build\Event\NeoBuildInlineEvent;
use Drupal\neo_settings\SettingsRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Act on build events.
 *
 * @package Drupal\custom_events\EventSubscriber
 */
class NeoBuildInlineEventSubscriber implements EventSubscriberInterface {

  /**
   * The settings.
   *
   * @var \Drupal\neo_settings\Plugin\SettingsInterface
   */
  private $settings;

  /**
   * Constructs a new NeoBuildEventSubscriber object.
   */
  public function __construct(
    SettingsRepositoryInterface $settings_repository,
  ) {
    $this->settings = $settings_repository->getActive();
  }

  /**
   * Subscribe to the user login event dispatched.
   *
   * We inject the CSS variables directly into the DOM so that we do not need
   * to wait for the build to complete before the CSS is applied.
   *
   * @param \Drupal\neo_build\Event\NeoBuildInlineEvent $event
   *   The neo build dev event.
   */
  public function onInlineBuild(NeoBuildInlineEvent $event) {
    $themeSettings = $this->settings->getValue(['themes', $event->getThemeName()], []);
    $event->addCacheTags(['config:neo_form.settings']);
    $contentColors = [
      '--form-item-primary' => '--form-item-primary-content',
      '--btn-color' => '--btn-content-color',
      '--btn-hover-color' => '--btn-hover-content-color',
      '--btn-primary-color' => '--btn-primary-content-color',
      '--btn-primary-hover-color' => '--btn-primary-hover-content-color',
      '--btn-secondary-color' => '--btn-secondary-content-color',
      '--btn-secondary-hover-color' => '--btn-secondary-hover-content-color',
      '--btn-accent-color' => '--btn-accent-content-color',
      '--btn-accent-hover-color' => '--btn-accent-hover-content-color',
    ];
    foreach ($themeSettings as $key => $value) {
      $isBtn = substr($key, 0, 3) === 'btn';
      $prefix = $key === 'item' ? 'form-' : '';
      if (is_array($value)) {
        foreach ($value as $subkey => $cssValue) {
          $originalValue = $cssValue;
          $cssKey = $key . '_' . $subkey;
          $cssVar = '--' . $prefix . str_replace('_', '-', $cssKey);
          $isColor = substr($cssVar, -5) === 'color';
          if ($isColor) {
            // Buttons use the -color in their CSS var. Other settings do not.
            if (!$isBtn) {
              $cssVar = str_replace('-color', '', $cssVar);
            }
            $cssValue = 'rgb(var(--color-' . $originalValue . '))';
          }
          $event->addCssValue($cssVar, $cssValue, '.form--neo');
          if ($isColor && isset($contentColors[$cssVar])) {
            $cssValue = 'rgb(var(--color-' . $originalValue . '-content))';
            $event->addCssValue($contentColors[$cssVar], $cssValue, '.form--neo');
          }
        }
      }
      else {
        $cssVar = '--' . $prefix . str_replace('_', '-', $key);
        if (substr($cssVar, -5) === 'color') {
          $cssVar = str_replace('-color', '', $cssVar);
          $value = 'rgb(var(--color-' . $value . '))';
        }
        $event->addCssValue($cssVar, $value, '.form--neo');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      NeoBuildInlineEvent::EVENT_NAME => 'onInlineBuild',
    ];
  }

}
