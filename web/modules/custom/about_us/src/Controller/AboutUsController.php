<?php

namespace Drupal\about_us\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Link;

class AboutUsController extends ControllerBase
{

  public function content()
  {
    $config = \Drupal::config('about_us.settings');

    $leaders = $config->get('leaders');
    $news_anchor = $config->get('news_anchor');
    $news_anchor_id = $news_anchor['id'];
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'news_page')
      ->condition('field_anchor', $news_anchor_id)
      ->sort('created', 'DESC')
      ->range(0, 3)->accessCheck(FALSE);
    $nids = $query->execute();

    $news_pages = Node::loadMultiple($nids);

    $build = [];
    $build[] = [
      '#markup' => '<h2>Leaders</h2>',
    ];
    foreach ($leaders as $leader) {
      if (isset($leader['name']) && isset($leader['designation'])) {
        $build[] = [
          '#markup' => '<p>' . $leader['name'] . ', ' . $leader['designation'] . '</p>',
        ];
      }
    }
    $build[] = [
      '#markup' => '<h2>News Anchor</h2>',
    ];
    $build[] = [
      '#markup' => '<p>' . $news_anchor['name'] . '</p>',
    ];
    $build[] = [
      '#markup' => '<h2>Latest News</h2>',
    ];
    foreach ($news_pages as $news_page) {
      $url = $news_page->toUrl();
      $link = Link::fromTextAndUrl($news_page->getTitle(), $url)->toString();
      $build[] = [
        '#markup' => '<p>' . $link . '</p>',
      ];
    }

    return $build;
  }
}
