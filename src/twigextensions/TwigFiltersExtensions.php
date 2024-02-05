<?php

namespace conversionia\leadflex\twigextensions;

use Craft;
use craft\helpers\StringHelper;
use Twig\Extension\AbstractExtension;

use Twig\TwigFilter;

class TwigFiltersExtensions extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('slugify', [$this, 'slugify']),
        ];
    }

    public function slugify($string)
    {
        $kebab = StringHelper::toKebabCase($string);
        $ascii = StringHelper::toAscii($kebab);

        return $ascii;
    }
}

