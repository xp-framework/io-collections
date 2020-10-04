<?php namespace io\collections\unittest;

use io\collections\{ArchiveCollection, IOCollection, IOElement};
use io\{IOException, TempFile};
use lang\archive\Archive;
use unittest\{Expect, Test};

class ArchiveCollectionTest extends \unittest\TestCase {
  private $file, $archive;

  /**
   * Sets up test case (creates temporary xar archive)
   *
   * @return void
   */
  public function setUp() {
    $this->file= new TempFile();
    $this->archive= new Archive($this->file);
    $this->archive->open(Archive::CREATE);
    $this->archive->addBytes('lang/Object.xp', 'class Object { }');
    $this->archive->addBytes('lang/Type.xp', 'class Type extends Object { }');
    $this->archive->addBytes('lang/reflect/Method.xp', 'class Method extends Object { }');
    $this->archive->addBytes('lang/reflect/Ctor.xp', 'class Ctor extends Object { }');
    $this->archive->addBytes('lang/types/String.xp', 'class String extends Object { }');
    $this->archive->addBytes('lang/types/map/Uint.xp', 'class Uint extends Object { }');
    $this->archive->addBytes('lang/Runnable.xp', 'interface Runnable { }');
    $this->archive->create();
  }
  
  /**
   * Tears down test case (removes temporary xar archive)
   *
   * @return void
   */
  public function tearDown() {
    try {
      $this->file->isOpen() && $this->file->close();
      $this->file->unlink();
    } catch (IOException $ignored) {
      // Can't really do much about it..
    }
  }
  
  /**
   * Assertion helper
   *
   * @param   string name
   * @param   string uri
   * @throws  unittest.AssertionFailedError
   */
  protected function assertXarUri($name, $uri) {
    $this->assertEquals('xar://', substr($uri, 0, 6));
    $this->assertEquals($name, substr($uri, -strlen($name)), $uri);
  }
  
  #[Test]
  public function entriesInBase() {
    $c= new ArchiveCollection($this->archive);
    try {
      $c->open();
      $first= $c->next();
      $this->assertInstanceOf(IOCollection::class, $first);
      $this->assertXarUri('lang/', $first->getURI());
      $this->assertEquals(0, $first->getSize());
      $this->assertEquals(null, $c->next());
    } finally {
      $c->close();
    }
  }
  
  #[Test]
  public function entriesInLang() {
    $c= new ArchiveCollection($this->archive, 'lang');
    
    try {
      $c->open();
      $expect= [
        'lang/Object.xp'    => IOElement::class,
        'lang/Type.xp'      => IOElement::class,
        'lang/reflect/'     => IOCollection::class,
        'lang/types/'       => IOCollection::class,
        'lang/Runnable.xp'  => IOElement::class
      ];
      for (reset($expect); $element= $c->next(), $name= key($expect); next($expect)) {
        $this->assertInstanceOf($expect[$name], $element);
        $this->assertXarUri($name, $element->getURI());
      }
      $this->assertEquals(null, $c->next());
    } finally {
      $c->close();
    }
  }

  /**
   * Returns first element in a given collection
   *
   * @param   io.IOCollection collection
   * @return  io.IOElement 
   * @throws  unittest.AssertionFailedError if no elements are available
   */
  protected function firstElement(\io\collections\IOCollection $collection) {
    $collection->open();
    $first= $collection->next();
    $collection->close();
    $this->assertNotEquals(null, $first, 'No first element in '.$collection->toString());
    return $first;
  }

  #[Test]
  public function readTwice() {
    $c= new ArchiveCollection($this->archive);
    $this->assertEquals($this->firstElement($c), $this->firstElement($c));
  }

  #[Test]
  public function readLangTwice() {
    $c= new ArchiveCollection($this->archive, 'lang');
    $this->assertEquals($this->firstElement($c), $this->firstElement($c));
  }

  #[Test]
  public function readObjectEntry() {
    with ($first= $this->firstElement(new ArchiveCollection($this->archive, 'lang'))); {
      $this->assertEquals(
        'class Object { }', 
        $first->getInputStream()->read($first->getSize())
      );
    }
  }

  #[Test, Expect(IOException::class)]
  public function writeObjectEntry() {
    $this->firstElement(new ArchiveCollection($this->archive, 'lang'))->getOutputStream();
  }

  #[Test, Expect(IOException::class)]
  public function readLangEntry() {
    $this->firstElement(new ArchiveCollection($this->archive))->getInputStream();
  }

  #[Test, Expect(IOException::class)]
  public function writeLangEntry() {
    $this->firstElement(new ArchiveCollection($this->archive))->getOutputStream();
  }

  #[Test]
  public function collections_origin() {
    $base= new ArchiveCollection($this->archive, 'lang');
    $this->assertEquals($base, $this->firstElement($base)->getOrigin());
  }

  #[Test]
  public function collections_name() {
    $base= new ArchiveCollection($this->archive, 'lang');
    $this->assertEquals('lang', $base->getName());
  }

  #[Test]
  public function collections_uri() {
    $base= new ArchiveCollection($this->archive, 'lang');
    $this->assertXarUri('lang/', $base->getUri());
  }

  #[Test]
  public function elements_name() {
    $element= $this->firstElement(new ArchiveCollection($this->archive, 'lang'));
    $this->assertEquals('Object.xp', $element->getName());
  }

  #[Test]
  public function elements_uri() {
    $element= $this->firstElement(new ArchiveCollection($this->archive, 'lang'));
    $this->assertXarUri('lang/Object.xp', $element->getUri());
  }
}