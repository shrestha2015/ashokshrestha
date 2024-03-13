<?php
/**
 * @file
 * Contains \Drupal\as_showcase\Plugin\Block\ShowCaseBlock.
 */


/* 
https://www.drupal.org/forum/support/module-development-and-code-questions/2018-02-24/custom-module-block-configuration-how
*/

namespace Drupal\as_showcase\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;


/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "as_showcase_block",
 *   admin_label = @Translation("Show Case Block"),
 * )
 */
class ShowCaseBlock extends BlockBase {
    public function blockForm($form, FormStateInterface $formState){     
        // $form['showcase_title'] = array(
        //     '#type' => 'textfield',
        //     '#title' => t('Heading'),
        //     '#description' => t('Enter the main heading'),
        //     '#default_value' => isset($this->configuration['showcase_title']) ? $this->configuration['showcase_title'] : '' 
        // );
        //echo "<pre>";print();exit;
        $form['showcase_backgroundimage'] = array(
            '#type' => 'managed_file',
            '#upload_location' => 'public://images/showcase',
            '#title' => t('Background image for Showcase'),
            '#upload_validators' => [
                'file_validate_extensions' => ['jpg', 'jpeg', 'png', 'gif']
            ],
            '#default_value' => isset($this->configuration['showcase_backgroundimage']) ? $this->configuration['showcase_backgroundimage'] : '',
            '#description' => t('The image to display'),
            '#required' => true
        );

        $form['showcase_text'] = array(
            '#type' => 'text_format',
            '#title' => t('Showcase Text'),
            '#description' => t('Main body'),
            '#format' => 'full_html',
            '#rows' => 10,
            '#default_value' => isset($this->configuration['showcase_text']['value']) ? $this->configuration['showcase_text']['value'] : ''
        );

        
        return $form;
    }
    
    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $formState){
        // Save image as permanent.
        $image = $formState->getValue('showcase_backgroundimage');
        if ($image != $this->configuration['showcase_backgroundimage']) {
          if (!empty($image[0])) {
            $file = File::load($image[0]);
            $file->setPermanent();
            $file->save();
          }
        }
        // Save configurations.
        $this->configuration['showcase_text'] = $formState->getValue('showcase_text');        
        $this->configuration['showcase_backgroundimage'] = $formState->getValue('showcase_backgroundimage');
    }

    /**
     * {@inheritdoc}
     */
    public function build(){   
        $resultarray = [];
        $image = $this->configuration['showcase_backgroundimage'];
        if (!empty($image[0])) {
            if ($file = File::load($image[0])) {
                $resultarray['image'] = [
                    '#theme' => 'image_style',
                    '#style_name' => 'medium',
                    '#uri' => $file->getFileUri(),
                ];
                $absolute_url_string = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
                    $url_string = \Drupal::service('file_url_generator')->generateString($file->getFileUri());            
                    
                $resultarray['showcase_backgroundimage'] = $url_string;
            }
        }
        $resultarray['text'] = $this->configuration['showcase_text']['value'];
        return array(
            '#theme' => 'showcase_parallax',           
            '#results' => $resultarray,
        );
    }
}

