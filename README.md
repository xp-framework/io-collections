I/O Collections
===============

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-framework/io-collections.svg)](http://travis-ci.org/xp-framework/io-collections)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_5plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/io-collections/version.png)](https://packagist.org/packages/xp-framework/io-collections)

Example
-------
This finds all JPEG files inside the directory `/home/thekid/multimedia`:

```
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\ExtensionEqualsFiler;
use util\cmd\Console;

$origin= new FileCollection('/home/thekid/multimedia');
$files= new FilteredIOCollectionIterator($origin, new ExtensionEqualsFilter('.jpg'), true);
foreach ($files as $file) {
  Console::writeLine('Element ', $file);
}
```

See also
--------
* [RFC #0196: I/O Collections random access](https://github.com/xp-framework/rfc/issues/196)
* [RFC #0174: io.collections interface additions / io.streams integration](https://github.com/xp-framework/rfc/issues/174)
* [RFC #0077: I/O Collections Extensions](https://github.com/xp-framework/rfc/issues/77)
* [RFC #0075: I/O Collections](https://github.com/xp-framework/rfc/issues/75)