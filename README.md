I/O Collections
===============

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-framework/io-collections.svg)](http://travis-ci.org/xp-framework/io-collections)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_5plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/io-collections/version.png)](https://packagist.org/packages/xp-framework/io-collections)

API
---
The entry point for accessing I/O collections are the `io.collections.IOCollection` implementations:

* `io.collections.FileCollection` - files in a given filesystem path
* `io.collections.ArchiveCollection` - files inside a XAR archive

To access the files from more than one collection, use the `io.collections.CollectionComposite` class.

Iteration
---------
The subpackage `io.collections.iterate` allows iterating I/O collections.

* `io.collections.iterate.IOCollectionIterator` - iterate a given I/O collection, optionally recursive
* `io.collections.iterate.FilteredIOCollectionIterator` - as above, but with an optional filter

### Filters
The following filters are available:

Date-based:

* `io.collections.iterate.AccessedAfterFilter(util.Date $date)`
* `io.collections.iterate.AccessedBeforeFilter(util.Date $date)`
* `io.collections.iterate.CreatedAfterFilter(util.Date $date)`
* `io.collections.iterate.CreatedBeforeFilter(util.Date $date)`
* `io.collections.iterate.ModifiedAfterFilter(util.Date $date)`
* `io.collections.iterate.ModifiedBeforeFilter(util.Date $date)`

Size-based:

* `io.collections.iterate.SizeEqualsFilter(int $compare)`
* `io.collections.iterate.SizeBiggerThanFilter(int $limit)`
* `io.collections.iterate.SizeSmallerThanFilter(int $limit)`

Name-based:

* `io.collections.iterate.ExtensionEqualsFilter(string $compare)`
* `io.collections.iterate.NameEqualsFilter(string $compare)`
* `io.collections.iterate.NameMatchesFilter(string $pattern)`
* `io.collections.iterate.UriMatchesFilter(string $pattern)`

Type-based:

* `io.collections.iterate.CollectionFilter()`

To combine filters, use the `util.Filters` class.

Example
-------
This finds all JPEG files inside the directory `/home/thekid/multimedia`:

```php
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\ExtensionEqualsFiler;
use util\cmd\Console;
use util\Filters;

$iterator= new FilteredIOCollectionIterator(
  new FileCollection('/home/thekid/multimedia'),
  Filters::allOf([new ExtensionEqualsFilter('.jpg'), new ExtensionEqualsFilter('.JPG')]), 
  true
);

foreach ($iterator as $file) {
  Console::writeLine($file);
}
```

See also
--------
* [RFC #0196: I/O Collections random access](https://github.com/xp-framework/rfc/issues/196)
* [RFC #0174: io.collections interface additions / io.streams integration](https://github.com/xp-framework/rfc/issues/174)
* [RFC #0077: I/O Collections Extensions](https://github.com/xp-framework/rfc/issues/77)
* [RFC #0075: I/O Collections](https://github.com/xp-framework/rfc/issues/75)