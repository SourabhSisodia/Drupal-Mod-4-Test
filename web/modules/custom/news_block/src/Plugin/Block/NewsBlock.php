<?php

namespace Drupal\your_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'NewsBlock' block.
 *
 * @Block(
 *  id = "news_block",
 *
 * )
 */
class NewsBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $build = [];


    $build[] = [
      '#markup' => "hello world",
    ];


    return $build;
  }
}
