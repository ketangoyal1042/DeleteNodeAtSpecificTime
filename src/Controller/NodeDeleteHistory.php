<?php

namespace Drupal\delete_node_at_specific_time\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;
use DateTime;


class NodeDeleteHistory extends ControllerBase
{

    function ListOfDeletedNodes() {
        $query = \Drupal::database()->select('deleted_node_history', 'dn');
        $query->fields('dn', ['node_data']);
        $result = $query->execute();
        $header = array('#Row No.', 'Node ID', 'Title', 'Time of Deletion');
        $deleted_nodes = [];
        $rownum = 0;
        foreach ($result as $row) {
          $node_blob = Json::decode($row->node_data);
          $daleted_time = new DateTime($node_blob['deletion_time_field'][0]['value']);
          $deleted_nodes[] = [
            'rowno' => ++$rownum,
            'nid' => $node_blob['nid'][0]['value'],
            'title' => $node_blob['title'][0]['value'],
            'deleted_time' =>  $daleted_time->format('d-m-Y H:i:s'),
          ];
        }
        $built['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $deleted_nodes,
            '#empty' => t('<b><center><p style="color:red">No Any Nodes has been deleted yet</p></center></b>')
        ];

        $built['flush_button'] = [
            '#type' => 'form',
            '#prefix' => '<div class="flush-button">',
            '#suffix' => '</div>',
            'submit' => [
                '#type' => 'submit',
                '#value' => $this->t('Flush Data'),
                '#submit' => ['::flushData'],
            ]
          ];

        return[
            '#title' => 'Book DB List',
            '#type' => 'markup',
            $built
        ];
      }


    public function flushData(array &$form, FormStateInterface $form_state) {
        // Perform the deletion of all data in the table.
        \Drupal::database()->truncate('deleted_node_history')->execute();
    
        // Clear the form cache to reflect the updated data.
        \Drupal::formBuilder()->clearCache();
    
        // Redirect back to the list of deleted nodes.
        $form_state->setRedirect('delete_node_at_specific_time.historylist');
    }
}