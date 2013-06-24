<?php

class dmConsoleLog extends dmConfigurable
{

    protected $dispatcher, $formatter;

    public function __construct(sfEventDispatcher $dispatcher, array $options = array())
    {
        $this->dispatcher = $dispatcher;
        $this->initialize($options);
    }

    protected function initialize($options)
    {
        parent::configure($options);
        $formatterClass = $this->getOption('formatter_class', 'sfAnsiColorFormatter');
        $this->formatter = new $formatterClass();
    }

    /**
     * Logs a message.
     *
     * @param mixed $messages  The message as an array of lines of a single string
     */
    public function log($messages)
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        $this->dispatcher->notify(new sfEvent($this, 'command.log', $messages));
    }

    /**
     * Logs a message in a section.
     *
     * @param string $section  The section name
     * @param string $message  The message
     * @param int $size     The maximum size of a line
     * @param string $style    The color scheme to apply to the section string (INFO, ERROR, or COMMAND)
     */
    public function logSection($section, $message, $size = null, $style = 'INFO')
    {
        $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection($section, $message, $size, $style))));
    }

    /**
     * Logs a message as a block of text.
     *
     * @param string|array $messages The message to display in the block
     * @param string $style    The style to use
     */
    public function logBlock($messages, $style = 'INFO')
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        $style = str_replace('_LARGE', '', $style, $count);
        $large = (Boolean)$count;

        $len = 0;
        $lines = array();
        foreach ($messages as $message) {
            $lines[] = sprintf($large ? '  %s  ' : ' %s ', $message);
            $len = max($this->strlen($message) + ($large ? 4 : 2), $len);
        }

        $messages = $large ? array(str_repeat(' ', $len)) : array();
        foreach ($lines as $line) {
            $messages[] = $line . str_repeat(' ', $len - $this->strlen($line));
        }
        if ($large) {
            $messages[] = str_repeat(' ', $len);
        }

        foreach ($messages as $message) {
            $this->log($this->formatter->format($message, $style));
        }
    }

    /**
     * Asks a question to the user.
     *
     * @param string|array $question The question to ask
     * @param string $style    The style to use (QUESTION by default)
     * @param string $default  The default answer if none is given by the user
     *
     * @param string       The user answer
     */
    public function ask($question, $style = 'QUESTION', $default = null)
    {
        if (false === $style) {
            $this->log($question);
        } else {
            $this->logBlock($question, null === $style ? 'QUESTION' : $style);
        }

        $ret = trim(fgets(STDIN));

        return $ret ? $ret : $default;
    }

    /**
     * Asks a confirmation to the user.
     *
     * The question will be asked until the user answer by nothing, yes, or no.
     *
     * @param string|array $question The question to ask
     * @param string $style    The style to use (QUESTION by default)
     * @param Boolean $default  The default answer if the user enters nothing
     *
     * @param Boolean      true if the user has confirmed, false otherwise
     */
    public function askConfirmation($question, $style = 'QUESTION', $default = true)
    {
        $answer = 'z';
        while ($answer && !in_array(strtolower($answer[0]), array('y', 'n'))) {
            $answer = $this->ask($question, $style);
        }

        if (false === $default) {
            return $answer && 'y' == strtolower($answer[0]);
        } else {
            return !$answer || 'y' == strtolower($answer[0]);
        }
    }

    /**
     * Asks for a value and validates the response.
     *
     * Available options:
     *
     *  * value:    A value to try against the validator before asking the user
     *  * attempts: Max number of times to ask before giving up (false by default, which means infinite)
     *  * style:    Style for question output (QUESTION by default)
     *
     * @param   string|array $question
     * @param   sfValidatorBase $validator
     * @param   array $options
     *
     * @return  mixed
     */
    public function askAndValidate($question, sfValidatorBase $validator, array $options = array())
    {
        if (!is_array($question)) {
            $question = array($question);
        }

        $options = array_merge(array(
            'value' => null,
            'attempts' => false,
            'style' => 'QUESTION',
        ), $options);

        // does the provided value passes the validator?
        if ($options['value']) {
            try {
                return $validator->clean($options['value']);
            } catch (sfValidatorError $error) {
            }
        }

        // no, ask the user for a valid user
        $error = null;
        while (false === $options['attempts'] || $options['attempts']--) {
            if (null !== $error) {
                $this->logBlock($error->getMessage(), 'ERROR');
            }

            $value = $this->ask($question, $options['style'], null);

            try {
                return $validator->clean($value);
            } catch (sfValidatorError $error) {
            }
        }

        throw $error;
    }

    public function logSettings($title, array $settings = array()) {
        $this->logHorizontalRule();
        $this->logBlock($title);
        $this->logHorizontalRule();

        $maxLength = 0;
        foreach ($settings as $key => $value) {
            $maxLength = max($maxLength, strlen($key));
        }

        foreach ($settings as $key => $value) {
            $message = $key;
            if (strlen($message) < $maxLength) {
                $message .=':   ';
                $counter = $maxLength - strlen($message);
                for ($i = 0; $i <= $counter+3; $i++) {
                    $message .= ' ';
                }
            } else {
                $message .= ':   ';
            }
            $message .= $value;
            $this->logBlock($message);
        }
        $this->logHorizontalRule();

    }

    public function logHorizontalRule($width = 50, $style = 'INFO', $char = '-') {
        $hr = '';
        for ($i = 0; $i < $width; $i++) $hr .= $char;
        $this->logBlock($hr, $style);
    }

    public function logBreakLine($count = 1)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->log('');
        }
    }

    public function logStatus($title, array $statuses = array(), $time = null)
    {
        $this->logHorizontalRule();
        $this->logBlock($title);
        $this->logHorizontalRule();

        $maxLength = 0;
        foreach ($statuses as $key => $value) {
            $maxLength = max($maxLength, strlen($key));
        }

        foreach ($statuses as $key => $value) {
            $message = $key;
            if (strlen($message) < $maxLength) {
                $message .=':   ';
                $counter = $maxLength - strlen($message);
                for ($i = 0; $i <= $counter+3; $i++) {
                    $message .= ' ';
                }
            } else {
                $message .= ':   ';
            }
            if (is_array($value)) {
                $message .= $value['message'];
                $this->logBlock($message, $value['style']);
            } else {
                $message .= $value;
                $this->logBlock($message);
            }

        }
        $this->logHorizontalRule();
        if (!is_null($time)) {
            $this->logBlock('TOTAL TIME: ' . $time . ' seconds'); // TODO Translate?
            $this->logHorizontalRule();
        }
    }

    protected function strlen($string)
    {
        if (!function_exists('mb_strlen')) {
            return strlen($string);
        }

        if (false === $encoding = mb_detect_encoding($string)) {
            return strlen($string);
        }

        return mb_strlen($string, $encoding);
    }
}