<?php

namespace Drupal\drem\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Drupal\Console\Command\Shared\CommandTrait;
use Drupal\Console\Style\DrupalStyle;

/**
 * Class DrushCommand.
 *
 * @package Drupal\drem
 */
class DrushCommand extends BaseCommand {

  use CommandTrait;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName("d")
      ->setDescription('Wrapper to run drush commands from the Console')
      //@todo use translatables
      ->addArgument('drush_command', InputArgument::REQUIRED, "Drush Command")
      ->addArgument('drush_argument', InputArgument::OPTIONAL, "Check");
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $command = "drush {$input->getArgument('drush_command')}";
    if ($argument = $input->getArgument('drush_argument')) {
      $command .= ' ' . $argument;
    }
    $process = new Process($command);

    // Need this to run interactive command
    $process->setTty(TRUE);
    $process->run(function($type, $buffer) {
      echo $buffer;
    });

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    $io->write($process->getOutput());
  }
}
