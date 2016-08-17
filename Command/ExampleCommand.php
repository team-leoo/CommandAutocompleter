<?php

namespace LeooTeam\CommandAutocompleterBundle\Command;

use LeooTeam\CommandAutocompleterBundle\Command\Style\AutocompleterStyle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ExampleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('example:autocompleter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new AutocompleterStyle($input, $output);
 
        $io->title('A usage example.');

        $io->block('Ask Questions');
        $answers = array('John', 'Peter', 'Raphael');
        $io->note('Values available: ' . implode(',', $answers));
        $question = new Question('First question: what is your firstname?');
        $name = $io->autocompleter($question, $answers);
        $io->writeln($name);

        $answers = array('Tyson');
        $moreAnswers = array('Doe', 'Kennedy');
        $io->note('Values available: ' . implode(',', array_merge($answers, $moreAnswers)));
        $question = new Question('Second question: what is your lastname?', 'Doe');
        $question->setAutocompleterValues($moreAnswers);
        $name = $io->autocompleter($question, $answers);
        $io->writeln($name);

        $io->note('Values available: ' . implode(',', $answers));
        $name = $io->autocompleter('Third question, could you please repeat your lastname?', 'ask', $answers);
        $io->writeln($name);

        $io->block('Choice Questions');
        $answers = array('Fish...', 'Pizza!');
        $io->note('Values available: ' . implode(',', $answers));
        $question = new ChoiceQuestion('What would you prefer?', $answers);
        $name = $io->autocompleter($question);
        $io->writeln($name);
        $answers = array('OMG YES', 'Definitively!', 'Not really...');
        $io->note('Values available: ' . implode(',', $answers));
        $name = $io->autocompleter('Are you sure...?', 'choice', $answers);
        $io->writeln($name);

        $io->block('Confirmation Questions');
        $question = new ConfirmationQuestion('Can you read this?');
        $name = $io->autocompleter($question);
        $io->writeln($name);
        $name = $io->autocompleter('Oh. Then do you think this example is over?', 'confirm');
        $io->writeln($name);

        $io->success('Well done!');
    }
}

