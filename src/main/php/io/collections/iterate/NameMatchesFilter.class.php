<?php namespace io\collections\iterate;

/**
 * Name filter
 *
 * @see   php://preg_match
 */
class NameMatchesFilter extends \lang\Object implements IterationFilter {
  public $pattern= '';
    
  /**
   * Constructor
   *
   * @param   string pattern a Perl-compatible regular expression
   */
  public function __construct($pattern) {
    $this->pattern= $pattern;
  }

  /**
   * Accepts an element
   *
   * @param   io.collections.IOElement element
   * @return  bool
   */
  public function accept($element) {
    return (bool)preg_match($this->pattern, $element->getName());
  }

  /**
   * Creates a string representation of this iterator
   *
   * @return  string
   */
  public function toString() {
    return nameof($this).'('.$this->pattern.')';
  }
}
