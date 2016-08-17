<?php

namespace LeooTeam\CommandAutocompleterBundle\Command\Style;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Style\StyleInterface;

class AutocompleterStyle extends SymfonyStyle implements AutocompleterStyleInterface, StyleInterface
{
    private static $defaultConfirmationValues = array(true => 'yes', false => 'no');

    public function autocompleter()
    {
        /** @var array $arguments */
        $arguments = func_get_args();
        /** @var Question|string $question */
        $question  = array_shift($arguments);

        if ($question instanceof Question) {
            if ($question instanceof ChoiceQuestion) {
                $question->setAutocompleterValues($question->getChoices());
            } elseif ($question instanceof ConfirmationQuestion) {
                $question->setAutocompleterValues(self::$defaultConfirmationValues);
            } elseif ($choices = current($arguments)) {
                if (!is_array($choices) or 1 > count($choices)) {
                    throw new \InvalidArgumentException('(1) Invalid arguments.');
                }
                $question->setAutocompleterValues(array_merge((array)$question->getAutocompleterValues(), $choices));
            }
            return $this->askQuestion($question);
        }

        $method = array_shift($arguments);
        if (!in_array($method, array('ask', 'choice', 'confirm'))) {
            throw new \InvalidArgumentException('(2) Invalid arguments.');
        }

        $choices = current($arguments);
        if (false === $choices and 'confirm' == $method) {
            $choices = self::$defaultConfirmationValues;
        }

        array_unshift($arguments, $question);
        $question = call_user_func_array(array($this, $method.'Autocompleter'), $arguments);
        $question->setAutocompleterValues($choices);

        return $this->askQuestion($question);
    }

    protected function askAutocompleter($question, array $choices, $default = null, $validator = null)
    {
        $question = new Question($question, $default);
        $question->setValidator($validator);
        $question->setAutocompleterValues($choices);

        return $question;
    }

    protected function choiceAutocompleter($question, array $choices, $default = null)
    {
        if (null !== $default) {
            $values = array_flip($choices);
            $default = $values[$default];
        }

        return new ChoiceQuestion($question, $choices, $default);
    }

    protected function confirmAutocompleter($question, $default = true)
    {
        return new ConfirmationQuestion($question, $default);
    }
}

