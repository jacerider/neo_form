<?php

declare(strict_types=1);

namespace Drupal\neo_form\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\neo_build\Event\NeoBuildInlineEvent;
use Drupal\neo_color\PalletInterface;
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
    foreach ($themeSettings as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $subkey => $subValue) {
          $cssVar = '--form-' . str_replace('_', '-', $key) . '-' . str_replace('_', '-', $subkey);
          if (substr($cssVar, -6) === '-color') {
            $subValue = 'rgb(var(--color-' . $subValue . '))';
          }
          $event->addCssValue($cssVar, $subValue, '.neo-form');
        }
      }
      else {
        $cssVar = '--form-' . str_replace('_', '-', $key);
        if (substr($cssVar, -6) === '-color') {
          $value = 'rgb(var(--color-' . $value . '))';
        }
        $event->addCssValue($cssVar, $value, '.neo-form');
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
