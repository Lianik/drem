<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


/**
 * Implements hook_drush_command().
 */
function drem_drush_command() {
  $prefix = 'd:';
  $items = array();

  $process = new Process('drupal list --format=json');
  $process->run();
  if (!$process->isSuccessful()) {
    // Silently stop commands introduction.
    return $items;
  }

  $commands = json_decode($process->getOutput(), TRUE);

  foreach ($commands['commands'] as $command) {
    $items[$prefix . $command['name']] = array(
      'description' => $command['description'],
      'callback' => 'drem_general_callback',
      'callback arguments' => array('console_command' => $command['name']),
      'arguments' => array(
        'test_arg' => "Test Arg",
      ),
    );
  }

  return $items;
}

function drem_general_callback() {
  $args = func_get_args();

  $args = implode(' ', $args);
  $process = new Process("drupal {$args}");
  $process->setTty(TRUE);
  $process->run(function($type, $buffer) {
    echo $buffer;
  });
  if (!$process->isSuccessful()) {
    throw new ProcessFailedException($process);
  }

  return $process->getOutput();
}
