<?php
echo "Initializing an environment variables file with a given project handle. \n\n";

$handle = $argv[1] ?? readline('Project handle: ');

// Script must be run in project root directory


// Uncomment if environment variables should be set by DDEV
// $inputFile = './setup/config.craft.yaml';
// $outputFile = './.ddev/config.craft.yaml';

$inputFile = './setup/.env';
$outputFile = './.env';


file_put_contents($outputFile, str_replace(
    [
        '$HANDLE$',
        '$UC_HANDLE$',
        '$SECURITY_KEY$',
	    '$RANDOM$'
    ],
    [
        $handle,
        ucfirst($handle),
        bin2hex(random_bytes(32)),
        bin2hex(random_bytes(8))
    ],
    file_get_contents($inputFile)
));

echo "$outputFile written\n";

