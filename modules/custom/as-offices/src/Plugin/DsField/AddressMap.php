<?php
/**
 * @file
 * Contains \Drupal\as_offices\Plugin\DsField\AddressMap.
 */

namespace Drupal\as_offices\Plugin\DsField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ds\Plugin\DsField\DsFieldBase;
use Drupal\file\Entity\File;

/**
 * @DsField(
 *   id = "as_offices_address_map",
 *   title = @Translation("Address Map"),
 *   entity_type = "node"
 * )
 */
class AddressMap extends DsFieldBase {

  /**
   * Build display render array.
   *
   * @return array
   */
  public function build() {
    $address = false;
    $node = $this->entity();
    $config = $this->getConfiguration();
    $renderer = \Drupal::service('renderer');
    // Get the field from the entity.
    $field = $node->get('field_office');
    // Render the field.
    if ($field) {
      // Set render options.
      $options = ['label' => 'hidden'];
      // Get the render array.
      $markup = $field->view($options);
      // Render the render array.
      $markup = $renderer->render($markup);
      // Format the address string.
      $address = $this->format($markup);
    }
    // Return empty render array if no address.
    if (!$address) {
      return [];
    }
    // Parse the icon image.
    $icon = FALSE;
    if (isset($config['icon'][0]) && $config['icon'][0]) {
      $fid = $config['icon'][0];
      $file = File::load($fid);
      if ($file && $url = $file->url()) {
        $icon = $url;
      }
    }
    // Return the address map render array.
    return [
      '#theme' => 'address_map',
      '#address' => $address,
      '#width' => $config['width'],
      '#height' => $config['height'],
      '#styles' => $config['styles'],
      '#zoom' => $config['zoom'],
      '#disable_default_ui' => $config['disable_default_ui'],
      '#scroll_wheel' => $config['scroll_wheel'],
      '#draggable' => $config['draggable'],
      '#disable_double_click_zoom' => $config['disable_double_click_zoom'],
      '#attached' => ['library' => ['as_offices/address_map']],
      '#icon' => $icon
    ];
  }

  /**
   * Formats field markup to build address line.
   *
   * @param $markup
   * @return mixed|string
   */
  private function format($markup) {
    $output = strip_tags($markup);
    $output = trim($output);
    // Remove all new lines.
    $output = preg_replace("/\r|\n/", ", ", $output);
    $output = trim($output);
    return $output;
  }

  /**
   * Build settings form.
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return mixed
   */
  public function settingsForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    $form['height'] = [
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#default_value' => $config['height']
    ];
    $form['width'] = [
      '#type' => 'textfield',
      '#title' => t('Width'),
      '#default_value' => $config['width']
    ];
    $form['styles'] = [
      '#type' => 'textarea',
      '#title' => t('Styles'),
      '#default_value' => $config['styles']
    ];
    $form['icon'] = array(
      '#type' => 'managed_file',
      '#name' => 'icon',
      '#title' => t('Icon'),
      '#size' => 22,
      '#description' => t("Upload an image to use for the map marker icon."),
      '#upload_location' => 'public://address-map/images',
      '#default_value' => $config['icon']
    );
    $form['zoom'] = [
      '#type' => 'textfield',
      '#size' => '10',
      '#title' => t('Zoom'),
      '#default_value' => $config['zoom']
    ];
    $form['disable_default_ui'] = [
      '#type' => 'checkbox',
      '#title' => t('Disable default UI'),
      '#default_value' => $config['disable_default_ui']
    ];
    $form['disable_double_click_zoom'] = [
      '#type' => 'checkbox',
      '#title' => t('Disable double click zoom'),
      '#default_value' => $config['disable_double_click_zoom']
    ];
    $form['scroll_wheel'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable scroll wheel'),
      '#default_value' => $config['scroll_wheel']
    ];
    $form['draggable'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable dragging'),
      '#default_value' => $config['draggable']
    ];
    return $form;
  }

  /**
   * Build default configuration.
   *
   * @return array
   */
  public function defaultConfiguration() {
    $configuration = [
      'height' => '500px',
      'width' => '500px',
      'zoom' => '15',
      'disable_default_ui' => 0,
      'disable_double_click_zoom' => 0,
      'scroll_wheel' => 1,
      'draggable' => 1,
      'styles' => 0,
      'icon' => 0
    ];
    return $configuration;
  }

  /**
   * Validate DS Field context.
   *
   * @return bool
   */
  public function isAllowed() {
    $bundle = $this->bundle();
    $entityManager = \Drupal::service('entity.manager');
    // Make sure we are on the 'office' content type.
    if ('office' !== $bundle) {
      return FALSE;
    }
    // Get the field definitions from the current node bundle.
    $field_definitions = $entityManager->getFieldDefinitions('node', $bundle);
    // Loop through field definitions. If address exists, the DS Field passes.
    foreach ($field_definitions as $field_definition) {
      if ('address' == $field_definition->getType()) {
        return TRUE;
      }
    }
    // Doesn't pass by default.
    return FALSE;
  }

}
