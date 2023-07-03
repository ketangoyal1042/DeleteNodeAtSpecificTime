<?php

namespace Drupal\delete_node_at_specific_time\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

class bundle_selection_form extends ConfigFormBase {
    
    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
      return ['delete_node_at_specific_time_admin.settings'];
    }
    
  /**
   * @return string
   */
  public function getFormId()
  {
    return 'delete_node_selection_form';
  }



    /**
     * Gets the configuration names that will be editable.
     *
     * @return array
     *   An array of configuration object names that are editable if called in
     *   conjunction with the trait's config() method.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Get all available content types.
        $contentTypes = NodeType::loadMultiple();
    
        // Prepare options array for the dropdown.
        $options = [];
        foreach ($contentTypes as $contentType) {
          $options[$contentType->id()] = $contentType->label();
        }
    
        $config = \Drupal::getContainer()->get('config.factory')->getEditable('delete_node_at_specific_time_admin.settings');
    
        $form['selected_content_types'] = [
          '#type' => 'checkboxes',
          '#title' => $this->t('Select Content Types'),
          '#options' => $options,
          '#multiple' => TRUE,
          '#default_value' => $config->get('selected_content_types'),
        ];
    
        return parent::buildForm($form, $form_state);
      }
    
      /**
       * {@inheritdoc}
       */
      public function submitForm(array &$form, FormStateInterface $form_state) {
        \Drupal::getContainer()->get('config.factory')->getEditable('delete_node_at_specific_time_admin.settings')
        ->set('selected_content_types', $form_state->getValue('selected_content_types'))
        ->save();
    
        parent::submitForm($form, $form_state);
        // drupal_set_message($this->t('The configuration has been saved.'));
      }
}