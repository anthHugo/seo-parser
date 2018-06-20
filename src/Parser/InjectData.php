<?php
/**
 * @author      Hugo Casabella <hugo.casabella@gmail.com>
 * @license     MIT
 */
declare(strict_types = 1);

namespace Seo\Parser;

class InjectData implements FilterInterface
{
    const avecCrochets  = 1;

	const sansCrochets  = 2;

    private $separators = '-. ,';

    /**
     * Le séparateur pour chaque élément de tableau par défaut
     * @var string
     */
    public static $defaultArraySeparator = ',';

    /**
     * La limite des résultats du tableau à afficher
     * @var int
     */
    public static $defaultArrayLimit = 10;

    private $data = [];

    public function __construct(array $data = [])
    {
        $this->sort($data);
        $this->data = $data;
    }

    public function apply(string $string): string
    {
        $separators = preg_quote($this->separators);

        foreach($this->data as $cle => $valeur)
        {
            // Si c'est un string, on trim
            if (is_string($valeur)) $valeur = trim($valeur);

            if ((is_array($valeur) && ! empty($valeur)) || is_callable($valeur))
            {
                // Traitement du cas avec des crochets
                $patternAvecCrochets = '/(?:\[){1}([^\[\]]*)
                    ({'.$cle.'(?:\((['.$separators.'])\)?)?
                    (?:<([0-9]+)>)?})
                    ([^\[\]\|]*)
                    (?:\|(?:[^\[\]]*))?
                    (?:\]){1}/xi';
                $callbackAvecCrochets = function($matches) use (&$valeur)
                {
                    return self::callbackParseFonctionOuTableau($matches, $valeur, self::avecCrochets);
                };
                $string = preg_replace_callback($patternAvecCrochets, $callbackAvecCrochets, $string);

                // Traitement du cas sans crochets
                $patternSansCrochets = '/({'.$cle.'(?:\((['.$separators.'])\)?)?(?:<([0-9]+)>)?(?:\!(.*)\!)?})/xi';
                $callbackSansCrochets = function($matches) use (&$valeur)
                {
                    return self::callbackParseFonctionOuTableau($matches, $valeur, self::sansCrochets);
                };
                $string = preg_replace_callback($patternSansCrochets, $callbackSansCrochets, $string);
            }
            else if (! empty($valeur))
            {
                $string = preg_replace('/(\[){1}([^\[\]]*)({'.$cle.'})([^\[\]\|]*)(\|([^\[\]]*))?(\]){1}/i', '${2}'.$valeur.'${4}', $string);
                $string = preg_replace('/({'.$cle.'})/i', $valeur, $string);
            }
        }

        return $string;
    }

    public static function callbackParseFonctionOuTableau($matches, $valeur, $type)
    {
        // Echappement des caractères spéciaux dans la liste des séparateurs.
        $defaultSeparator = preg_quote(self::$defaultArraySeparator);
        $defaultLimit     = self::$defaultArrayLimit;

        $return = NULL;

        if ( ! empty($valeur))
        {
            switch ($type)
            {
                /**
                 * [0] = La chaine compléte
                 * [1] = Partie de la chaine avant la variable
                 * [2] = {Variable}
                 * [3] = Séparateur
                 * [4] = Limite de résultat
                 * [5] = Chaîne après la variable
                 */
                case self::avecCrochets:
                    $limit     = empty($matches[4]) ? $defaultLimit : $matches[4];
                    $separator = empty($matches[3]) ? $defaultSeparator : $matches[3];
                    break;
                /**
                 * [0] = La chaine compléte
                 * [1] = {Variable}
                 * [2] = Séparateur
                 * [3] = Limite de résultat
                 */
                case self::sansCrochets:
                    $limit     = empty($matches[3]) ? $defaultLimit : $matches[3];
                    $separator = empty($matches[2]) ? $defaultSeparator : $matches[2];
                    break;
            }

            $separator = self::makeSeparatorPresentation($separator);

            if (is_array($valeur))
            {
                // Si !expression! negative
                if(preg_match('/\!(.*)\!/', $matches[0], $negative))
                {
                    $valeur = array_flip($valeur);
                    unset($valeur[$negative[1]]);
                    $valeur = array_flip($valeur);
                }

                $array 	= array_slice($valeur, 0, $limit);
                $return = implode($separator, $array);
            }
            elseif (is_callable($valeur))
            {
                if (($callbackResult = $valeur($limit, $separator)) != NULL)
                    $return = $callbackResult;
            }
        }

        if ($type == self::avecCrochets)
            $return = $matches[1].$return.$matches[5];

        return $return;
    }


    public static function makeSeparatorPresentation($separator)
    {
        switch ($separator) {
            case ',':
                $separator = ', ';
                break;
            case '-':
                $separator = ' - ';
                break;
            case '+':
                $separator = ' + ';
                break;
        }

        return $separator;
    }

    private function sort(&$data): void
    {
        uasort($data, function($a, $b){
            if (is_array($a) && is_array($b)) {
                return 0;
            }

    		if (is_array($a)) {
                return 1;
            }

    		if (is_array($b)) {
                return -1;
            }
        });
	}
}
