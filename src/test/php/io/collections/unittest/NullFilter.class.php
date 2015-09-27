<?php namespace io\collections\unittest;

use io\collections\iterate\IterationFilter;

/**
 * Accept-all filter
 */
class NullFilter extends \lang\Object implements IterationFilter {

  /**
   * Accepts an element
   *
   * @param   io.collections.IOElement $element
   * @return  bool
   */
  public function accept($element) {
    return true;
  }
} 
