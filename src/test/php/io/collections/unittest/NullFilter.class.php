<?php namespace io\collections\unittest;

/** Accept-all filter */
class NullFilter implements \io\collections\iterate\IterationFilter {

  /** Accepts an element */
  public function accept($element) { return true; }
} 