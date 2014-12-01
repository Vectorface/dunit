#!/usr/bin/env php
<?php
// dunit.php

require_once __DIR__.'/../vendor/autoload.php';

$application = new \Vectorface\Dunit\DunitApplication();
$application->run();

