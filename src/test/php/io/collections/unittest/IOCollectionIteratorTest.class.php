<?php namespace io\collections\unittest;

use io\collections\IOElement;
use io\collections\iterate\{AccessedAfterFilter, AccessedBeforeFilter, CreatedAfterFilter, CreatedBeforeFilter, ExtensionEqualsFilter, FilteredIOCollectionIterator, IOCollectionIterator, IterationFilter, ModifiedAfterFilter, ModifiedBeforeFilter, NameEqualsFilter, NameMatchesFilter, SizeBiggerThanFilter, SizeEqualsFilter, SizeSmallerThanFilter, UriMatchesFilter};
use unittest\Test;
use util\Filters;

/**
 * Unit tests for I/O collection iterator classes
 *
 * @see   xp://io.collections.IOCollectionIterator
 * @see   xp://io.collections.FilteredIOCollectionIterator
 */
class IOCollectionIteratorTest extends AbstractCollectionTest {

  /**
   * Helper method
   *
   * @param   io.collections.iterate.Filter $filter
   * @param   bool $recursive default FALSE
   * @return  string[] an array of the elements' URIs
   */
  protected function filterFixtureWith($filter, $recursive= false) {
    $elements= [];
    for (
      $it= new FilteredIOCollectionIterator($this->fixture, $filter, $recursive);
      $it->hasNext(); 
    ) {
      $elements[]= $it->next()->getURI();
    }
    return $elements;
  }

  #[Test]
  public function iteration() {
    for ($it= new IOCollectionIterator($this->fixture), $i= 0; $it->hasNext(); $i++) {
      $element= $it->next();
      $this->assertInstanceOf(IOElement::class, $element);
    }
    $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
  }

  #[Test]
  public function recursiveIteration() {
    for ($it= new IOCollectionIterator($this->fixture, true), $i= 0; $it->hasNext(); $i++) {
      $element= $it->next();
      $this->assertInstanceOf(IOElement::class, $element);
    }
    $this->assertEquals($this->total, $i);
  }

  #[Test]
  public function foreachLoop() {
    foreach (new IOCollectionIterator($this->fixture) as $i => $element) {
      $this->assertInstanceOf(IOElement::class, $element);
    }
    $this->assertEquals($this->sizes[$this->fixture->getURI()]- 1, $i);
  }

  #[Test]
  public function foreachLoopRecursive() {
    foreach (new IOCollectionIterator($this->fixture, true) as $i => $element) {
      $this->assertInstanceOf(IOElement::class, $element);
    }
    $this->assertEquals($this->total- 1, $i);
  }

  #[Test]
  public function filteredIteration() {
    $this->assertEquals(
      $this->sizes[$this->fixture->getURI()],
      sizeof($this->filterFixtureWith(new NullFilter(), false))
    );
  }

  #[Test]
  public function filteredRecursiveIteration() {
    $this->assertEquals(
      $this->total,
      sizeof($this->filterFixtureWith(new NullFilter(), true))
    );
  }

  #[Test]
  public function nameMatches() {
    $this->assertEquals(
      ['./first.txt', './second.txt'],
      $this->filterFixtureWith(new NameMatchesFilter('/\.txt$/'), false)
    );
  }

  #[Test]
  public function nameMatchesRecursive() {
    $this->assertEquals(
      ['./first.txt', './second.txt', './sub/IMG_6100.txt'],
      $this->filterFixtureWith(new NameMatchesFilter('/\.txt$/'), true)
    );
  }

  #[Test]
  public function nameEquals() {
    $this->assertEquals(
      [], 
      $this->filterFixtureWith(new NameEqualsFilter('__xp__.php'), false)
    );
  }

  #[Test]
  public function nameEqualsRecursive() {
    $this->assertEquals(
      ['./sub/sec/__xp__.php'],
      $this->filterFixtureWith(new NameEqualsFilter('__xp__.php'), true)
    );
  }

  #[Test]
  public function extensionEquals() {
    $this->assertEquals(
      [], 
      $this->filterFixtureWith(new ExtensionEqualsFilter('.php'), false)
    );
  }

  #[Test]
  public function extensionEqualsRecursive() {
    $this->assertEquals(
      ['./sub/sec/lang.base.php', './sub/sec/__xp__.php'],
      $this->filterFixtureWith(new ExtensionEqualsFilter('.php'), true)
    );
  }

  #[Test]
  public function uriMatches() {
    $this->assertEquals(
      ['./first.txt', './second.txt'],
      $this->filterFixtureWith(new UriMatchesFilter('/\.txt$/'), false)
    );
  }

  #[Test]
  public function uriMatchesRecursive() {
    $this->assertEquals(
      ['./sub/', './sub/IMG_6100.jpg', './sub/IMG_6100.txt', './sub/sec/', './sub/sec/lang.base.php', './sub/sec/__xp__.php'],
      $this->filterFixtureWith(new UriMatchesFilter('/sub/'), true)
    );
  }

  #[Test]
  public function uriMatchesDirectorySeparators() {
    with ($src= $this->addElement($this->fixture, new MockCollection('./sub/src'))); {
      $this->addElement($src, new MockElement('./sub/src/Generic.xp')); 
    }
    $this->assertEquals(
      ['./sub/src/Generic.xp'],
      $this->filterFixtureWith(new UriMatchesFilter('/sub\/src\/.+/'), true)
    );
  }

  #[Test]
  public function uriMatchesPlatformDirectorySeparators() {
    $mockName= '.'.DIRECTORY_SEPARATOR.'sub'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Generic.xp';
    with ($src= $this->addElement($this->fixture, new MockCollection('.'.DIRECTORY_SEPARATOR.'sub'.DIRECTORY_SEPARATOR.'src'))); {
      $this->addElement($src, new MockElement($mockName));
    }
    $this->assertEquals(
      [$mockName],
      $this->filterFixtureWith(new UriMatchesFilter('/sub\/src\/.+/'), true)
    );
  }
  
  #[Test]
  public function zeroBytes() {
    $this->assertEquals(
      ['./zerobytes.png'],
      $this->filterFixtureWith(new SizeEqualsFilter(0), false)
    );
  }

  #[Test]
  public function bigFiles() {
    $this->assertEquals(
      ['./sub/IMG_6100.jpg'],
      $this->filterFixtureWith(new SizeBiggerThanFilter(500000), true)
    );
  }

  #[Test]
  public function smallFiles() {
    $this->assertEquals(
      ['./second.txt', './zerobytes.png'],
      $this->filterFixtureWith(new SizeSmallerThanFilter(500), true)
    );
  }

  #[Test]
  public function accessedAfter() {
    $this->assertEquals(
      ['./first.txt', './second.txt', './sub/sec/lang.base.php', './sub/sec/__xp__.php'],
      $this->filterFixtureWith(new AccessedAfterFilter(new \util\Date('Oct  1  2006')), true)
    );
  }

  #[Test]
  public function accessedBefore() {
    $this->assertEquals(
      ['./third.jpg', './zerobytes.png'],
      $this->filterFixtureWith(new AccessedBeforeFilter(new \util\Date('Dec 14  2004')), true)
    );
  }

  #[Test]
  public function modifiedAfter() {
    $this->assertEquals(
      ['./sub/sec/lang.base.php', './sub/sec/__xp__.php'],
      $this->filterFixtureWith(new ModifiedAfterFilter(new \util\Date('Oct  7  2006')), true)
    );
  }

  #[Test]
  public function modifiedBefore() {
    $this->assertEquals(
      ['./third.jpg', './zerobytes.png'],
      $this->filterFixtureWith(new ModifiedBeforeFilter(new \util\Date('Dec 14  2004')), true)
    );
  }

  #[Test]
  public function createdAfter() {
    $this->assertEquals(
      ['./sub/sec/__xp__.php'],
      $this->filterFixtureWith(new CreatedAfterFilter(new \util\Date('Jul  1  2006')), true)
    );
  }

  #[Test]
  public function createdBefore() {
    $this->assertEquals(
      ['./sub/sec/lang.base.php'],
      $this->filterFixtureWith(new CreatedBeforeFilter(new \util\Date('Feb 22  2002')), true)
    );
  }

  #[Test]
  public function allOf() {
    $this->assertEquals(
      ['./third.jpg'],
      $this->filterFixtureWith(Filters::allOf([
        new ModifiedBeforeFilter(new \util\Date('Dec 14  2004')),
        new ExtensionEqualsFilter('jpg')
      ]), true)
    );
  }

  #[Test]
  public function anyOf() {
    $this->assertEquals(
      ['./first.txt', './second.txt', './zerobytes.png', './sub/IMG_6100.txt'],
      $this->filterFixtureWith(Filters::anyOf([
        new SizeSmallerThanFilter(500),
        new ExtensionEqualsFilter('txt')
      ]), true)
    );
  }

  #[Test]
  public function noneOf() {
    $this->assertEquals(
      ['./third.jpg', './sub/', './sub/IMG_6100.jpg', './sub/sec/', './sub/sec/lang.base.php', './sub/sec/__xp__.php'],
      $this->filterFixtureWith(Filters::noneOf([
        new SizeSmallerThanFilter(500),
        new ExtensionEqualsFilter('txt')
      ]), true)
    );
  }

  #[Test]
  public function originBasedOn() {
    $c= $this->newCollection('/home', [
      new MockElement('.nedit'),
      $this->newCollection('/home/bin', [
        new MockElement('xp')
      ])
    ]);
    
    foreach (new IOCollectionIterator($c, true) as $i => $e) {
      $this->assertOriginBasedOn($c, $e->getOrigin());
    }
  }

  #[Test]
  public function originEqualsBase() {
    $c= $this->newCollection('/home', [
      new MockElement('.nedit'),
      $this->newCollection('/home/bin', [
        new MockElement('xp')
      ])
    ]);
    
    foreach (new IOCollectionIterator($c) as $i => $e) {
      $this->assertEquals($c, $e->getOrigin());
    }
  }

  #[Test]
  public function originEquals() {
    $c= $this->newCollection('/home', [
      new MockElement('.nedit'),
      $bin= $this->newCollection('/home/bin', [
        new MockElement('xp.exe')
      ])
    ]);
    
    foreach (new FilteredIOCollectionIterator($c, new ExtensionEqualsFilter('.exe'), true) as $i => $e) {
      $this->assertNotEquals($c, $e->getOrigin());
      $this->assertEquals($bin, $e->getOrigin());
    }
  }
}