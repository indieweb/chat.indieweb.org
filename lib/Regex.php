<?php
abstract class Regex
{
    public static $expression;
    public static $replacement;

    public static function match($text, &$matches)
    {
        return preg_match(self::$expression, $text, $matches);
    }

    public static function match_all($text, &$matches)
    {
        return preg_match_all(self::$expression, $text, $matches);
    }

    public static function replace($text)
    {
        return preg_replace(self::$expression, self::$replacement, $text);
    }

    /**
     * Returns the raw match found in $text. Assumes the match is found at \1
     * @param string $text
     * @return string The matched text
     */
    protected static function _get($text)
    {
        if(self::match($text, $match))
        {
            return $match[1];
        }
        return FALSE;
    }
}

class Regex_Phone extends Regex
{
    public static $expression = '|1?\(?([0-9]{3})[^0-9]{0,2}([0-9]{3})[^0-9]{0,1}([0-9]{4})|e';
    public static $replacement = FALSE;

    /**
     * Format the matched phone number the requested way. Can specify the separator character
     * as well as whether to parenthesize the area code.
     * @param string $text
     * @param object $opts [optional]
     * @return string The formatted phone number
     */
    public static function getFormatted($text, $opts=array())
    {
        // a bandaid for the lack of late static binding in PHP < 5.3
        parent::$expression = self::$expression;

        $separator = '-';
        $parens = FALSE;
        extract($opts);

        if(self::match($text, $match))
        {
            if($parens)
                return '(' . $match[1] . ') ' . $match[2] . $separator . $match[3];
            else
                return $match[1] . $separator . $match[2] . $separator . $match[3];
        }
        else
        {
            return $text;
        }
    }

    /**
     * Returns the raw digits of the telephone number
     * @param object $text
     * @return
     */
    public static function getDigits($text)
    {
        // a bandaid for the lack of late static binding in PHP < 5.3
        parent::$expression = self::$expression;

        if(self::match($text, $matches))
            return $matches[1] . $matches[2] . $matches[3];

        return FALSE;
    }

    /**
     * Return just the area code of the telephone number
     */
    public static function getAreaCode($text)
    {
        if(self::match($text, $matches))
            return $matches[1];

        return FALSE;
    }
}

class Regex_Twitter extends Regex
{
    public static $expression = '/(?<![a-z0-9_\/])@([a-z0-9_]+)/i';
    public static $replacement = '<a href="https://twitter.com/$1" target="_blank">@$1</a>';

    /**
     * Returns the Twitter username found in $text
     * @param string $text
     * @return string The matched text
     */
    public static function get($text)
    {
        return self::_get($text);
    }
}

class Regex_URL extends Regex
{
    public static $expression = '|(https?:\/\/[^\s]+(?<![\),]))|';
    public static $replacement = '<a href="$1" target="_blank">$1</a>';

    /**
     * Returns the raw URL found in $text
     * @param string $text
     * @return string The matched text
     */
    public static function get($text)
    {
        return self::_get($text);
    }

    public static function replace($text)
    {
        return parent::replace($text);
    }
}


class Regex_WikiPage extends Regex
{
    public static $expression = array('/(^|\s)\/([^ >*]+)/i','/\[\[([^\]]+)\]\]/');
    public static $replacement = array('$1<a href="{{wikibase}}$2" target="_blank">/$2</a>','[[<a href="{{wikibase}}$1" target="_blank">$1</a>]]');

    /**
     * Returns the raw URL found in $text
     * @param string $text
     * @return string The matched text
     */
    public static function get($text)
    {
        return self::_get($text);
    }

    public static function replace($text)
    {
        return parent::replace($text);
    }
}


class Regex_Email extends Regex
{
    public static $expression = '|([a-z0-9_\.\+\-]+@[a-z0-9_\.\+\-]+\.[a-z0-9\.]{2,4})|i';
    public static $replacement = '<a href="mailto:$1">$1</a>';

    /**
     * Returns the raw email found in $text
     * @param string $text
     * @return string The matched text
     */
    public static function get($text)
    {
        return self::_get($text);
    }
}

/**
 * Parses the name and email address out of an email recipient line.
 * May be formatted like any of the following:
 *
 * "First Last" <first.last@gmail.com>
 * First Last <first.last@gmail.com>
 * first.last@gmail.com
 */
class Regex_EmailRecipient extends Regex
{
    public static $expression = '|([a-z0-9_\.\+\-]+@[a-z0-9_\.\+\-]+\.[a-z0-9\.]{2,4})|i';
    public static $nameExpression = '|([^<>"]+)|';
    public static $replacement = '<a href="mailto:$2">$2</a>';

    /**
     * Returns the raw email found in $text
     * @param string $text
     * @return string The matched text
     */
    public static function get($text)
    {
        parent::$expression = self::$expression;
        return self::_get($text);
    }

    public static function getEmail($text)
    {
        if(preg_match(self::$expression, $text, $match))
            return $match[1];
        else
            return FALSE;
    }

    public static function getName($text)
    {
        $text = preg_replace(self::$expression, '', $text);
        if(preg_match(self::$nameExpression, $text, $match))
            return trim($match[1]);
        if(preg_match(self::$nameExpression, $text, $match))
            return trim($match[1]);
        else
            return FALSE;
    }

    public static function test()
    {
        $tests[] = '"First Last" <first.last@gmail.com>';
        $tests[] = 'First Last <first.last@gmail.com>';
        $tests[] = 'First Middle Last <first.last@gmail.com>';
        $tests[] = '<first.last@gmail.com>';
        $tests[] = 'first.last@gmail.com';
        $tests[] = 'Not an email address';

        foreach($tests as $test)
        {
            echo 'Input: <b>' . htmlspecialchars($test) . '</b><br />';
            echo htmlspecialchars('Email: /' . Regex_EmailRecipient::getEmail($test) . '/ Name: /' . Regex_EmailRecipient::getName($test)) . '/<br />';
            echo '<br />';
        }
    }
}

