<?php

namespace Drupal\as_social_links\Plugin\Block;

use Drupal\Component\Utility\SortArray;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;

/**
 * Provides a 'SocialPlatformsBlock' block.
 *
 * @Block(
 *  id = "social_platforms_block",
 *  admin_label = @Translation("Social Platforms Block"),
 * )
 */

class SocialPlatformsBlock extends BlockBase{
	/**
	 * Social media platforms
	 */
	public $platforms = array(
		'facebook' => 'Facebook',
		'youtube' => 'YouTube',
		'twitter' => 'Twitter',
		'linkedin' => ' LinkedIn',
		'pinterest' => 'PInterest',
		'google-plus' => 'Google Plus+',
		'tumblr' => 'Tumblr',
		'instagram' => 'Instagram'
	);

	/**
	* Build default block config.
	*
	* @return array
	*/
	public function defaultConfiguration() { 
  	$config = [];
  	foreach ($this->platforms as $platform_id => $platform_label) {
  		$config['platforms'][$platform_id] = [
  			'url' => '',
  			'weight' => 0,
  			'label' => $platform_label
  		];
  	}
  	return $config;
	}

	/**
   * Build theme render array.
   *
   * @return array
   */
  public function build() {
    $build = [];
    $config = $this->getConfiguration();
    // Create item list.
    $build['ashok_social_platforms_block'] = [
      '#theme' => 'item_list',
      '#attributes' => [
      'class' => 'as-social-platforms-block'
      ]
    ];

    uasort($config['platforms'], ['Drupal\Component\Utility\SortArray', 'sortByWeightElement']);
    // Create item list items.
    foreach ($config['platforms'] as $platform_id => $platform) {

      if ($platform['url']) {
        $title = t('Follow us on ') . $platform['label'];
        $class = 'external';
        switch ($platform_id) {
          case 'facebook':
          $title=t('<i class="fa fa-facebook"></i>');
          $class = 'external facebook';
          break;

          case 'instagram':
          $title=t('<i class="fa fa-instagram"></i>');
          $class = 'external instagram';
          break;

          case 'google-plus':
          $title=t('<i class="fa fa-google-plus"></i>');
          $class = 'external gplus';
          break;

          case 'youtube':
          $title=t('<i class="fa fa-youtube"></i>');
          $class = 'external ytube';
          break;

          case 'twitter':
          $title=t('<i class="fa fa-twitter"></i>');
          $class = 'external twitter';
          break;

          case 'linkedin':
          $title=t('<i class="fa fa-linkedin"></i>');
          $class = 'external linkedin';
          break;

          case 'tumblr':
          $title=t('<i class="fa fa-tumblr"></i>');
          $class = 'external tumblr';
          break;

          case 'pinterest':
          $title=t('<i class="fa fa-pinterest"></i>');
          $class = 'external pinterest';
          break;

          default:
          $title = t('Follow us on ') . $platform['label'];
          break;
        }
        $build['ashok_social_platforms_block']['#items'][$platform_id] = [
        '#title' => $title,
        '#type' => 'link',
        '#url' => Url::fromUri($platform['url']),
        '#attributes' => [
        'target' => '_blank',
        'class' => $class,
        'data-animate' => 'fadeInUp',
        'data-animate-hover' => 'shake'
        ]
        ];
      }
    }
    return $build;
  }

	/**
   * Build block config form.
   *
   * @url https://www.drupal.org/node/1876710
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return array
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    // Build social platforms table.
    $form['platforms'] = [
    '#type' => 'table',
    '#header' => [t('Name'), t('URL'), t('Weight')],
    '#tabledrag' => [
    [
    'action' => 'order',
    'relationship' => 'sibling',
    'group' => 'social-platforms-block-order-weight',
    ],
    ],
    ];
    // Create platform table row.
    foreach ($config['platforms'] as $platform_id => $platform) {
      $form['platforms'][$platform_id] = [
      '#weight' => $platform['weight'],
      '#attributes' => [
      'class' => ['draggable']
      ]
      ];
      $form['platforms'][$platform_id]['label'] = [
      '#markup' => $platform['label']
      ];
      $form['platforms'][$platform_id]['url'] = [
      '#type' => 'textfield',
      '#default_value' => $platform['url'],
      '#element_validate' => [
          // Workaround for:
          // https://www.drupal.org/node/2649746
      [$this, 'url_validate']
      ]
      ];
      // Used for tabledrag weight.
      $form['platforms'][$platform_id]['weight'] = [
      '#type' => 'weight',
      '#title' => t('Weight for @title', ['@title' => $platform['label']]),
      '#title_display' => 'invisible',
      '#default_value' => $platform['weight'],
      '#attributes' => [
      'class' => ['social-platforms-block-order-weight']
      ],
      ];
    }
    //kint($form['platforms']);
    // Sort the table by weight.
    uasort($form['platforms'], ['Drupal\Component\Utility\SortArray', 'sortByWeightProperty']);
    return $form;
  }

  /**
   * Validate url.
   *
   * @param $element
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param $form
   */
  public function url_validate($element, FormStateInterface &$form_state, $form) {
    $absolute_url = TRUE;
    if ($element['#value'] && !UrlHelper::isValid($element['#value'], $absolute_url)) {
      $form_state->setError(
        $element,
        t('@label: URL needs to be absolute, eg: http://example.com.', ['@label' => $element['#attributes']['data-label'][0]])
        );
    }
  }

  /**
   * Submit handler.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('platforms', $form_state->getValue('platforms'));
  }	
}