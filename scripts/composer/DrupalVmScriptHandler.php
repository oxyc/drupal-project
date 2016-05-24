<?php

/**
 * @file
 * Contains \DrupalVm\composer\DrupalVmScriptHandler.
 */

namespace DrupalVm\composer;

use Composer\Installer\PackageEvent;
use Symfony\Component\Filesystem\Filesystem;

class DrupalVmScriptHandler {

  public static function createConfigFile(PackageEvent $event) {
    $package = $event->getOperation()->getPackage();
    if ($package->getName() !== 'geerlingguy/drupal-vm') {
      return;
    }

    $cwd = getcwd();
    $options = self::getOptions($event->getComposer());
    $configPath = $options['config-dir'] . '/config.yml';
    $drupalvmPath = $event->getComposer()->getInstallationManager()->getInstallPath($package);

    $fs = new Filesystem();
    if (!$fs->exists("$cwd/$configPath") || $options['overwrite']) {
      $fs->copy("$drupalvmPath/example.config.yml", "$cwd/$configPath");
      $fs->chmod("$cwd/$configPath", 0666);
      $event->getIO()->write("Create a default $configPath file with chmod 0666");
    }
  }

  protected static function getOptions($composer) {
    $extra = $composer->getPackage()->getExtra() + ['drupal-vm' => []];
    return $extra['drupal-vm'] + [
      'config-dir' => 'config',
      'overwrite' => FALSE
    ];
  }

}
