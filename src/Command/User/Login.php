<?php

namespace Mionote\Command\User;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command for Set Your Evenote API token
 *
 * Examples:
 * mionote user:token:set
 */
class Login extends  BaseCommand
{
    protected function configure() {
        $this->setName("user:token:set")
            ->setDescription("Set your Evernote API Token")
            ->setHelp("Usage: mionote user:token:set");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->title("Set API Token");

        $token = $io->ask('Evernote API Token', null, function($token) {
            return $token;
        });

        if (empty($token)) {
            return $io->warning("Input your Evernote Token please");
        }

        if(\Mionote\Common\File::writeToken($token)) {
            $io->success("Your token has been set");
        } else {
            $io->caution('Set API token failed, check whether your HOME directory is writable');
        }
    }
}
