<?php

namespace Mionote\Command\Note;

use Evernote\Enml\Converter\EnmlToHtmlConverter;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Mionote\Note\Note;

class Find extends BaseCommand
{
    protected function configure()
    {
        $this->setName("note:find");
        $this->setDescription("查找一些笔记")->setHelp("查找一些笔记");

        $this->addArgument("title", InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');

        $io = new SymfonyStyle($input, $output);

        $notes = Note::find($title);

        $count = count($notes);

        if ($count === 0) {
            $io->caution("没有找到与“{$title}”相关的笔记");
            return null;
        }

        if ($count === 1) {
            $content = self::showNote($notes[0]['guid']);
            $io->title($notes[0]['title']);
            return $io->writeln($content);
        }


        $choices = [];
        $table   = [];

        $io->success("找到{$count}条笔记");

        foreach ($notes as $k => $note) {
            $created_at = date('Y-m-d H:i:s', $note['created_at'] / 1000);
            $updated_at = date('Y-m-d H:i:s', $note['updated_at'] / 1000);
            $choices[$k] = $k;

            array_push($table, [
                '序号' => $k,
                '标题' => $note['title'],
                '创建时间' => $created_at,
                '更新时间' => $updated_at,
                'GUID'    => $note['guid']
            ]);
        }

        $io->table(['序号', '标题', '创建时间', '更新时间', 'GUID'], $table);

        $choice = $io->choice("请输入笔记序号：", $choices, 0);

        $io->title($notes[$choice]['title']);

        $io->writeln(self::showNote($notes[$choice]['guid']));
    }

    public static function showNote(string $guid)
    {
        $note = Note::getNote($guid);
        $content = $note->getContent();
        $convert = new EnmlToHtmlConverter();

        return \Html2Text\Html2Text::convert($convert->convertToHtml($content));
    }
}