#!/usr/bin/env php

<?php

exec('D:\UniServerZ\core\php54\php.exe D:\TEST-PHP\vendor\phpunit\phpunit\phpunit --configuration D:\code-source\dev-01\test\test-selenium\', $output, $returnCode);

if ($returnCode !== 0) {
  $minimalTestSummary = array_pop($output);
  printf("Test suite for %s failed: ");
  printf("( %s ) %s%2\$s", $minimalTestSummary, PHP_EOL);
  printf("ABORTING COMMIT!\n");
  exit(1);
}

exit(0);