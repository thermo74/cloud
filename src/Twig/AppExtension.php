<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class AppExtension extends AbstractExtension
{
    public function getFunctions() : array
    {
        return [
            new TwigFunction('icon', [$this, 'icon'], array('is_safe' => array('html'))),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('readableFilesize', [$this, 'readableFilesize'])
        ];
    }

    public function icon(string $icon, string $class = null) : string
    {
        return "<svg class='icon icon-{$icon} {$class}'><use xlink:href='{$_ENV['ROOT_URL']}/images/icons/sprite.svg#{$icon}'></svg>";
    }

    public function readableFilesize($size, $precision = 2, $space = ' ')
    {
        if( $size <= 0 ) {
            return '0' . $space . 'KB';
        }

        if( $size === 1 ) {
            return '1' . $space . 'byte';
        }

        $mod = 1024;
        $units = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');

        for( $i = 0; $size > $mod && $i < count($units) - 1; ++$i ) {
            $size /= $mod;
        }

        return round($size, $precision) . $space . $units[$i];
    }
}