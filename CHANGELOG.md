## Version 1.2-dev
* Fixed false positives for nested by-reference foreach loops wrapped in functions or class methods
* Added detection of continue/break outside of loop/switch contexts

## Version 1.1.0
* Added warning for setting cookies with empty names
* Added color differentiation between error messages and warnings
* Added the ```--level (-l)``` option to specify minimum reported issue level
* Added the ```--relative-paths (-r)``` option to output file paths relative to checked directories
* Added the ```--integer-size``` option to specify integer size of the target system
* Fixed detection of non-lowercase function names

## Version 1.0.2
* Fixed notice in ```PHP4ConstructorVisitor``` caused by anonymous classes

## Version 1.0.1
* Fixed ```ReflectionException``` in ```ContainerBuilder::addVisitors``` on PHP 5.3.3
