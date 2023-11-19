<?php
$projectPath = __DIR__ ;

// Declare directories which contains php code
$scanDirectories = [
   $projectPath . '/app/',
];

// Optionally declare standalone files
$scanFiles = [
];


return [
  'composerJsonPath' => $projectPath . '/composer.json',
  'vendorPath' => $projectPath . '/vendor/',
  'scanDirectories' => $scanDirectories,
  'scanFiles' => $scanFiles,
  // whitelist
  'skipPackages' => [
    'predis/predis',
    'doctrine/dbal'], 
];
