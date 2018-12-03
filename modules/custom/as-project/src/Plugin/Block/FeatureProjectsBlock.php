<?php
/**
 * @file
 * Contains \Drupal\as_project\Plugin\Block\CategoryBlock.
 */

namespace Drupal\as_project\Plugin\Block;

use Drupal\Component\Utility\SortArray;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
use Drupal\views\Views;
use Drupal\Core\Entity;
use Drupal\taxonomy\Entity\Term;

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
        $query = \Drupal::entityQuery('taxonomy_term');
        $query->condition('vid', "framework");
        $tids = $query->execute();
        $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
        $resultarray = array();
        $frameworksarray = array();
        if($terms){
            foreach ($terms as $term_key => $term_value) {
                $temparray['id']=$term_key;
                $temparray['name']= $term_value->getName();
                $temparray['url']=$term_value->url();
                // strip out all whitespace
                $term_clean = preg_replace('/\s*/', '', $term_value->getName());
                // convert the string to all lowercase
                $term_lower = strtolower($term_clean);
                $temparray['term_data']=$term_lower;
                $frameworksarray[] = $temparray;
            }    
        }        
        $view_results = views_get_view_result('projects','featured_projects');
        if($view_results){
            foreach ($view_results as $key => $view_value) {
                $nids = $view_value->nid;
                $node = \Drupal\node\Entity\Node::load($nids);
                /*$nodearray = $node->toArray();
                https://www.metaltoad.com/blog/drupal-8-entity-api-cheat-sheet
                */                
                $tem = array();
                   
                $tem['title'] = $node->get('title')->value; 
                $tem['short_desc'] = $node->get('body')->summary;
                $tem['full_desc'] = $node->get('body')->value;
                $term = Term::load($node->field_php_framework->target_id);
                $name = $term->getName();
                // strip out all whitespace
                $zname_clean = preg_replace('/\s*/', '', $name);
                // convert the string to all lowercase
                $zname_clean = strtolower($zname_clean);

                $tem['category'] = $zname_clean;            
                $file = $node->field_thumbnail->entity;               
                $thumbnail ='';
                if($file){
                    $thumbnail = file_url_transform_relative(file_create_url($file->getFileUri()));
                }
                $tem['thumbnail'] = $thumbnail;                
                
                $filedownload = $node->field_download_case_study->entity;
                $case_study = '';
                if($filedownload){
                    $case_study=file_url_transform_relative(file_create_url($filedownload->getFileUri()));    
                }
                $tem['case_study']=$case_study;

                $tem['website'] = $node->field_website->uri?$node->field_website->uri:'';

                /*Getting multiple images*/
                $img_urls = '';
                $i = 1;
                $len = count($node->field_gallery);                
                foreach ($node->field_gallery as $item) {
                    if ($item->entity) {
                        $img_urls .= $item->entity->url();    
                        if ($len > $i) {
                           $img_urls .= ',';     
                           $i++;
                        }                
                    }                    
                }
                $tem['gallery'] = $img_urls;
                //kint($img_urls);
                //kint($node->field_gallery->entity);
                // $gallery = array();
                // if($file){
                //     $thumbnail = file_url_transform_relative(file_create_url($file->getFileUri()));
                // }
                // $tem['thumbnail'] = $thumbnail;


                //kint($nodearray['field_php_framework']['target_id']);
                $resultarray[] = $tem;
                //$node->get($fieldName)->getValue();
                //$resultarray[]= $node->getTitle();
                //$resultarray[] = $node->get('field_download_case_study')->getValue();
                //echo "<pre>";var_dump($node->toArray());
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
