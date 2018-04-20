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
    protected function configure()
    {
        $this->setName("user:token:set")
            ->setDescription("设置你的Token")
            ->setHelp("通过这个命令来设置你的Evernote Token");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("设置Token");

        $token = $io->ask('输入你在Evernote上设置的Token', null, function($token) {
            return $token;
        });

        if (empty($token)) {
            return $io->warning("请输入您的Token");
        }

        if(\Mionote\Common\File::writeToken($token)) {
            $io->success("设置成功");
        } else {
            $io->caution('设置失败，请检查$HOME目录是否可写');
        }
    }
}
