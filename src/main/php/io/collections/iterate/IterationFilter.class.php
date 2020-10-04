<?php namespace io\collections\iterate;

use lang\Generic;
use util\Filter;

/**
 * Iteration filter
 *
 * @see  xp://io.collections.iterate.FilteredIOCollectionIterator
 */
#[Generic(extends: ['io.collections.IOElement'])]
interface IterationFilter extends Filter {

}