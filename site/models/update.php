<?php

use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Toolkit\Str;

class UpdatePage extends Page
{
    // Card photo: the first photo below the update, otherwise the first image used in the text.
    public function thumbnail(): File|null
    {
        if ($photo = $this->photos()->toFile()) {
            return $photo;
        }

        foreach ($this->text()->toBlocks() as $block) {
            $file = match ($block->type()) {
                'image' => $block->image()->toFile(),
                'gallery' => $block->images()->toFile(),
                default => null,
            };
            if ($file) {
                return $file;
            }
        }

        return null;
    }

    // Plain-text teaser for the card, taken from the text blocks only (not captions or quotes).
    public function summary(int $length = 140): string
    {
        $html = $this->text()->toBlocks()->filter(fn ($block) => in_array($block->type(), ['text', 'list']))->toHtml();

        // Space after paragraphs and list items so their words don't run together once the tags are stripped.
        return Str::excerpt(preg_replace('#</(p|li)>#', '$0 ', $html), $length);
    }
}
