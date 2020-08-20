<?php

namespace Drupal\eventtime\Plugin\Block;

use Drupal\Core\Block\Blockbase;

/**
 * Provides a 'Event time Block' Block
 * @Block(
 *  id = "eventtime_block",
 *  admin_label =  @Translation("Event time block"),
 *  category = @Translation("Event time block"),
 *  
 * )
 * 
 */
class EventTimeBlock extends BlockBase {
  
  public function __construct(){
  }
  /**
   * {@inheritdoc}
   */
  public function build(){
    return [
      '#markup' => $this->getEventDate(),
      '#cache' => [
        'max-age' => 0
      ]
    ];
  }

  /**
   * returns date event
   */
  public function getEventDate(){
    $database = \Drupal::database();
    
    /**
     * get current node id
     */
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = $node->id();

    /**
     * check if current node type is "event"
     */
    $query = $database->select('node_field_data', 'nfd');

    $query->condition('nfd.nid', $nid, '=');
    $query->fields('nfd', ['type', 'vid']);

    $result = $query->execute();

    $number_of_rows = $query->countQuery()->execute()->fetchField();
    
    if( $number_of_rows == 1 ){
      $record = $result->fetch();
    }

    if( $record->type == 'event'){
      /**
       * get event date
       */

      $date_query = $database->select('node_revision__field_event_date','nrfed');
      
      $date_query->condition('nrfed.revision_id', $record->vid, '=');
      $date_query->fields('nrfed', ['field_event_date_value']);

      $date_result = $date_query->execute();

      $date_record = $date_result->fetch();

      $event_date = $date_record->field_event_date_value;

      //$event_date = strtotime($event_date);
      //$event_date = date("Y-m-d", $event_date);

      return $this->dayDifference($event_date);
    }
    else{
      return 'This node is not of type "event"';
    }
  }

  /**
   * returns difference in day between current date and $date
   */
  public function dayDifference($date){
    $current_timestamp = time();
    $current_date = date("Y-m-d", $current_timestamp);
    
    $event_timestamp = strtotime($date);
    $event_date = date("Y-m-d", $event_timestamp);

    if( $event_date == $current_date ){
      return "This event is happening today.";
    }
    else{
      $date_difference = $current_timestamp - $event_timestamp;

      $date_difference = round($date_difference / (60*60*24));

      if( $date_difference > 0 ){
        return "This event has already passed.";
      }
      else{
        return abs($date_difference) . " days left until event starts";
      }
    }
  }
}