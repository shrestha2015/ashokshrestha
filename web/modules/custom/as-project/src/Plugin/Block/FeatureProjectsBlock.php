<?php
/**
 * @file
 * Contains \Drupal\as_project\Plugin\Block\CategoryBlock.
 */

namespace Drupal\as_project\Plugin\Block;

use Drupal\Core\Entity;
use Drupal\Component\Utility\SortArray;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
use Drupal\views\Views;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Image\Image;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "as_project_feature_block",
 *   admin_label = @Translation("Feature Projects Block"),
 * )
 */
class FeatureProjectsBlock extends BlockBase {
    public function build() {
        $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
        $taxonomy_tree = $manager->loadTree('platform', 0, NULL, TRUE);
        $results_terms = [];
        $resultarray = array();
        $frameworksarray = array();
        if($taxonomy_tree){
            foreach ($taxonomy_tree as $term_key => $term_value) {
                // echo $term_value->getName();
                // echo "<pre>";print_r($term_value);exit;
                $temparray['id']=$term_key;
                $temparray['name']= $term_value->getName();
                //$temparray['url']=$term_value->url();
                // strip out all whitespace
                $term_clean = preg_replace('/\s*/', '', $term_value->getName());
                // convert the string to all lowercase
                $term_lower = strtolower($term_clean);
                $temparray['term_data']=$term_lower;
                $frameworksarray[] = $temparray;
            }    
        }
        
        $view_results = views_get_view_result('project','feature_project');
        if($view_results){
            foreach ($view_results as $key => $view_value) {
                $nids = $view_value->nid;
                $node = \Drupal\node\Entity\Node::load($nids);                   

                /*$nodearray = $node->toArray();
                https://www.metaltoad.com/blog/drupal-8-entity-api-cheat-sheet
                */                
                $tem = array();

                $tem['title'] = $node->field_project_name->value ?? ''; 
                $tem['short_desc'] = $node->field_project_description->summary;
                $tem['full_desc'] = $node->field_project_description->value;
                $term = Term::load($node->field_platform->target_id);
                $name = $term->name->value;;                

                // strip out all whitespace
                $zname_clean = preg_replace('/\s*/', '', $name);
                // convert the string to all lowercase
                $zname_clean = strtolower($zname_clean);
                $tem['category'] = $zname_clean;    

                $file = $node->field_project_thumbnail->entity;               
                $thumbnail ='';
                if($file){
                    $absolute_url_string = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
                    $url_string = \Drupal::service('file_url_generator')->generateString($file->getFileUri());            
                    $thumbnail = $absolute_url_string;
                }
                   
                $tem['thumbnail'] = $thumbnail;                
                
                $filedownload = $node->field_project_case_study->entity;
                $case_study = '';
                if($filedownload){
                    $case_study=file_url_transform_relative(file_create_url($filedownload->getFileUri()));    
                }
                $tem['case_study']=$case_study;

                $tem['website'] = $node->field_project_link->uri?$node->field_project_link->uri:'';
                       
                 /*Fetching Muiltiple Gallery images*/
                // foreach ($node->field_gallery as $item) {
                //     if ($item->entity) {
                                        
                //     }                    
                // }
                $tem['gallery'] = '';
                $img_urls = '';
                $i = 1;
                $len = count($node->field_project_gallery->entity->field_gallery_image);                
                if($node->field_project_gallery->entity->field_gallery_image){
                    foreach ($node->field_project_gallery->entity->field_gallery_image as $para) {                    
                        $gal_image = File::load($para->target_id);
                        $absolute_gallery_url = \Drupal::service('file_url_generator')->generateAbsoluteString($gal_image->getFileUri());                        
                        $img_urls .= $absolute_gallery_url;    
                        if ($len > $i) {
                           $img_urls .= ',';     
                           $i++;
                        }
                    }
                }
                $tem['gallery'] = $img_urls;
                $resultarray[] = $tem;
            }    
        }

        //kint($resultarray);
        return array(
            '#theme' => 'featured_projects',
            '#frameworks' => $frameworksarray,
            '#results'=> $resultarray,
        );
    }
}
