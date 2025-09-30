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
   * Subscribe to the Neo build event dispatched.
   *
   * We inject the CSS variables directly into the DOM so that we do not need
   * to wait for the build to complete before the CSS is applied.
   *
   * @param \Drupal\neo_build\Event\NeoBuildInlineEvent $event
   *   The neo build dev event.
   */
  public function onInlineBuild(NeoBuildInlineEvent $event) {
    $event->addCacheTags(['config:neo_form.settings']);
    if (!$this->settings->getValue(['status', $event->getThemeName()])) {
      return;
    }
    $themeSettings = $this->settings->getValue(['themes', $event->getThemeName()], []);
    if (!$themeSettings) {
      return;
    }
    $contentColors = [
      '--form-item-primary' => '--form-item-primary-content',
      '--btn-bg-color' => '--btn-content-color',
      '--btn-bg-color-hover' => '--btn-content-color-hover',
      '--btn-primary-bg-color' => '--btn-primary-content-color',
      '--btn-primary-bg-color-hover' => '--btn-primary-content-color-hover',
      '--btn-secondary-bg-color' => '--btn-secondary-content-color',
      '--btn-secondary-bg-color-hover' => '--btn-secondary-content-color-hover',
      '--btn-accent-bg-color' => '--btn-accent-content-color',
      '--btn-accent-bg-color-hover' => '--btn-accent-content-color-hover',
    ];
    foreach ($themeSettings as $key => $value) {
      $isBtn = substr($key, 0, 3) === 'btn';
      $prefix = $key === 'item' ? 'form-' : '';
      if (is_array($value)) {
        foreach ($value as $subkey => $cssValue) {
          $originalValue = $cssValue;
          $cssKey = $key . '_' . $subkey;
          $cssVar = '--' . $prefix . str_replace('_', '-', $cssKey);
          $isColor = str_contains($cssVar, 'color');
          if ($isColor) {
            // Buttons use the -color in their CSS var. Other settings do not.
            if (!$isBtn) {
              $cssVar = str_replace('-color', '', $cssVar);
            }
            $cssValue = $originalValue === 'transparent' ? 'transparent' : 'rgb(var(--color-' . $originalValue . '))';
          }
          $event->addCssValue($cssVar, (string) $cssValue, '.form--neo');
          if ($isColor && isset($contentColors[$cssVar])) {
            $cssValue = 'rgb(var(--color-' . $originalValue . '-content))';
            $event->addCssValue($contentColors[$cssVar], $cssValue, '.form--neo');
          }
        }
      }
      else {
        $cssVar = '--' . $prefix . str_replace('_', '-', $key);
        if (str_contains($cssVar, 'color')) {
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
