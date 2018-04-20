<?php

namespace Mionote\Command\User;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Mionote\Common\File;

/**
 * Command for Set Your Evenote API token
 *
 * Examples:
 * mionote user:editor:set --path={$editor_path}
 */
class Editor extends BaseCommand
{
    protected function configure() {
        $this->setName("user:editor:set")
            ->setDescription("Set an editor for create an modify notes")
            ->setHelp("Usage: mionote user:editor:set /usr/bin/emacs");

        $this->addArgument("path", InputArgument::REQUIRED, "Editor path");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->title("Set Editor");

        $path = $input->getArgument("path");

        clearstatcache();

        $unilike = !(PATH_SEPARATOR === ';');

        if ($unilike) {
            $command = exec(escapeshellcmd("which {$path}"));

            if ($command === '') {
                $io->error("It looks like {$path} is not installed, If it is installed, you can specify a specific executable file");

                return null;
            }

            if (!is_executable($command)) {
                $io->error("Editor {$path} is already installed, but it looks like it is note executable");

                return null;
            }

            $path = $command;
        } else {
            if (is_executable($path) === false) {
                $io->error("It looks like \"{$path}\" is not executable");

                return null;
            }
        }

        if (File::writeEditor($path)) {
            $io->success("Editor has been configured, enjoy it");
        } else {
            $io->error("Configure editor failed");
        }

        return null;
    }
}
