<?php

namespace Mongolium\Core\Helper;

trait Slug
{
    public function makeSlug(string $title): string
    {
        $slug = str_replace('--', '-',
            str_replace(
                ' ',
                '-',
                str_replace(
                    ['!', '£', '$', '%', '^', '&', '*', '@', '+', '=', '#', '?', ',', '.', '>', '<', '~', '"', '|', '/', '\''],
                    '',
                    strtolower($title)
                )
            )
        );

        if (preg_match('|--|', $slug)) {
            $slug = $this->makeSlug($slug);
        }

        return $slug;
    }
}
