<?php

namespace Drupal\views\Plugin\views\argument;

/**
 * Argument handler for a month (MM)
 *
 * @ViewsArgument("date_month")
 */
class MonthDate extends Date {

  /**
   * {@inheritdoc}
   */
  protected $format = 'F';

  /**
   * {@inheritdoc}
   */
  protected $argFormat = 'm';

  /**
   * Provide a link to the next level of the view
   */
  public function summaryName($data) {
    $month = str_pad($data->{$this->name_alias}, 2, '0', STR_PAD_LEFT);
    $timestamp = strtotime("2005" . $month . "15" . " 00:00:00 UTC");
    if($timestamp){
      $summary_name = format_date($timestamp, 'custom', $this->format, 'UTC');
    } else {
      $summary_name = parent::summaryName($data);
    }
    return $summary_name;
  }

  /**
   * Provide a link to the next level of the view
   */
  public function title() {
    $month = str_pad($this->argument, 2, '0', STR_PAD_LEFT);
    $timestamp = strtotime("2005" . $month . "15" . " 00:00:00 UTC");
    if($timestamp){
      $title = format_date($timestamp, 'custom', $this->format, 'UTC');
    } else {
      $title = parent::title();
    }
    return $title;
  }

  public function summaryArgument($data) {
    // Make sure the argument contains leading zeroes.
    return str_pad($data->{$this->base_alias}, 2, '0', STR_PAD_LEFT);
  }

}
