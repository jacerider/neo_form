<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_form\Kernel;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_build\Event\NeoBuildInlineEvent;
use Drupal\neo_build\Scope;
use Drupal\neo_form\EventSubscriber\NeoBuildInlineEventSubscriber;
use Drupal\neo_settings\Plugin\SettingsInterface;
use Drupal\neo_settings\SettingsRepositoryInterface;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that neo_form keys its form styling by build scope.
 *
 * This module is the only out-of-package reader of the inline event's scope,
 * and it looked it up under the name the event used to give it — a theme name.
 * It reads the scope now, and its settings form narrows to match: the generator
 * only ever emits per scope, so every other installed theme the form used to
 * offer was an afternoon a site builder could spend on styling that would
 * never be emitted anywhere.
 *
 * The stored `themes:` key keeps its name, and nothing migrates. A stray entry
 * for another theme is as inert after this change as it was before it.
 *
 * Nothing here saves the settings: the assertions are against the built form
 * array and the subscriber's own output, and this site has no
 * `neo_form.settings` object to disturb.
 */
#[Group('neo_form')]
class NeoFormScopeTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'neo',
    'neo_settings',
    'neo_color',
    'neo_build',
    'neo_form',
  ];

  /**
   * A neo_form settings plugin carrying the given values.
   */
  protected function settings(array $values): SettingsInterface {
    /** @var \Drupal\neo_settings\Plugin\SettingsInterface $settings */
    $settings = $this->container->get('plugin.manager.neo_settings')->createInstance('neo_form');
    $settings->setValues($values);
    return $settings;
  }

  /**
   * The subscriber, reading the given settings.
   */
  protected function subscriber(array $values): NeoBuildInlineEventSubscriber {
    $settings = $this->settings($values);
    $repository = new class($settings) implements SettingsRepositoryInterface {

      /**
       * Constructs the stub.
       */
      public function __construct(private readonly SettingsInterface $settings) {}

      /**
       * {@inheritdoc}
       */
      public function getActive($checkAccess = TRUE) {
        return $this->settings;
      }

      /**
       * {@inheritdoc}
       */
      public function get($variationId, $checkAccess = TRUE) {
        return $this->settings;
      }

      /**
       * {@inheritdoc}
       */
      public function getAll($checkAccess = TRUE) {
        return [$this->settings];
      }

      /**
       * {@inheritdoc}
       */
      public function getCore() {
        return $this->settings;
      }

      /**
       * {@inheritdoc}
       */
      public function getVariations($checkAccess = TRUE) {
        return [];
      }

      /**
       * {@inheritdoc}
       */
      public function getVariationEntities() {
        return [];
      }

    };
    return new NeoBuildInlineEventSubscriber($repository);
  }

  /**
   * Dispatches the subscriber for one scope and returns the CSS it produced.
   */
  protected function cssFor(Scope $scope, array $values): string {
    $event = new NeoBuildInlineEvent($scope);
    $this->subscriber($values)->onInlineBuild($event);
    return $event->getCss();
  }

  /**
   * Settings for one scope, keyed the way the form stores them.
   */
  protected function valuesFor(Scope $scope, bool $status): array {
    return [
      'status' => [$scope->value => $status],
      'themes' => [
        $scope->value => [
          'form' => ['spacing' => '1.5rem'],
        ],
      ],
    ];
  }

  /**
   * A scope whose status is off emits nothing.
   */
  public function testEmitsNothingWhenTheScopeStatusIsOff(): void {
    $css = $this->cssFor(Scope::Front, $this->valuesFor(Scope::Front, FALSE));

    $this->assertSame('', $css);
  }

  /**
   * A scope whose status is on emits its CSS variables.
   */
  public function testEmitsTheCssVariablesWhenTheScopeStatusIsOn(): void {
    $css = $this->cssFor(Scope::Front, $this->valuesFor(Scope::Front, TRUE));

    $this->assertStringContainsString('.form--neo{', $css);
    $this->assertStringContainsString('--form-spacing: 1.5rem;', $css);
  }

  /**
   * One scope's settings do not leak into the other scope's CSS.
   */
  public function testReadsTheDispatchedScopesSettingsAndNoOthers(): void {
    $values = $this->valuesFor(Scope::Front, TRUE);

    $this->assertNotSame('', $this->cssFor(Scope::Front, $values));
    $this->assertSame('', $this->cssFor(Scope::Back, $values));
  }

  /**
   * The settings form offers the scopes, and no other installed theme.
   */
  public function testTheSettingsFormOffersExactlyTheScopes(): void {
    // An installed theme that is not a scope: before the narrowing, the form
    // listed it and let a site builder style it into nothing.
    $this->container->get('theme_installer')->install(['olivero']);

    $form_state = new FormState();
    $form = $this->settings([])->buildSettingsForm(['#parents' => ['settings']], $form_state);

    $expected = array_map(static fn (Scope $scope): string => $scope->value, Scope::cases());
    $this->assertSame($expected, array_keys($form['status']['#options']));
  }

}
