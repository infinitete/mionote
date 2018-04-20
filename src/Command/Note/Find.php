<?php

namespace Mionote\Command\Note;

use Evernote\Enml\Converter\EnmlToHtmlConverter;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Mionote\Note\Note;

/**
 * The Command for Find some notes
 *
 * Examples:
 * mionote note:find Hello
 */
class Find extends BaseCommand
{
    protected function configure()
    {
        $this->setName("note:find");
        $this->setDescription("Find some notes")->setHelp("Find some notes by title");

        $this->addArgument("title", InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');

        $io = new SymfonyStyle($input, $output);

        $notes = Note::find($title);

        $count = count($notes);

        if ($count === 0) {
            $io->caution("Found No One about \"{$title}\"");
            return null;
        }

        if ($count === 1) {
            $content = self::showNote($notes[0]['guid']);
            $io->title($notes[0]['title']);
            return $io->writeln($content);
        }


        $choices = [];
        $table   = [];

        $io->success("Find {$count} note(s)");

        foreach ($notes as $k => $note) {
            $created_at = date('Y-m-d H:i:s', $note['created_at'] / 1000);
            $updated_at = date('Y-m-d H:i:s', $note['updated_at'] / 1000);
            $choices[$k] = $k;

            array_push($table, [
                'Sort' => $k,
                'Title' => $note['title'],
                'Created At' => $created_at,
                'Updated At' => $updated_at,
                'GUID'    => $note['guid']
            ]);
        }

        $io->table(['Sort', 'Title', 'Created At', 'Updated At', 'GUID'], $table);

        $choice = $io->choice("Choice：", $choices, 0);

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
