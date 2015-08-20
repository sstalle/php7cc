<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->exclude(array(
        'vendor',
        'test/resource',
    ));

$fixers = array(
    '-phpdoc_to_comment',
    '-concat_without_spaces',
    'concat_with_spaces',
    'newline_after_open_tag',
);

return Symfony\CS\Config\Config::create()
    ->finder($finder)
    ->fixers($fixers)
;