<?php

declare(strict_types=1);

namespace Drupal\neo_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\neo_form\PreviewBuild;

/**
 * Returns responses for Neo | Form routes.
 */
final class PreviewController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    return PreviewBuild::build(FALSE);
  }

}
