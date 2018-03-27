<?php

namespace Mongolium\Core\Helper;

trait Slug
{
    public function makeSlug(string $title): string
    {
        $slug = str_replace('--', '-',
            preg_replace('|[^a-z0-9\-]|', '', str_replace(' ', '-', strtolower($title)))
        );

        if (preg_match('|--|', $slug)) {
            $slug = $this->makeSlug($slug);
        }

        return $slug;
    }
}
