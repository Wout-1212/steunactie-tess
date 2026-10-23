<?php

Kirby::plugin('tess/typography', [
    'hooks' => [
        // Apply the name styling to template text and editable Panel content alike.
        'page.render:after' => function (string $html, string $contentType): string {
            if ($contentType !== 'html') {
                return $html;
            }

            $document = new DOMDocument('1.0', 'UTF-8');
            if (!$document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET)) {
                return $html;
            }
            $xpath = new DOMXPath($document);
            $nodes = $xpath->query('//body//text()[not(ancestor::script or ancestor::style or ancestor::textarea or ancestor::select or ancestor::svg)]');
            $textNodes = iterator_to_array($nodes);
            foreach ($textNodes as $node) {
                if (!preg_match('/\bTess\b/iu', $node->nodeValue)) {
                    continue;
                }
                $parent = $node->parentNode;
                if ($parent instanceof DOMElement && str_contains(' ' . $parent->getAttribute('class') . ' ', ' tess-name ')) {
                    continue;
                }
                $fragment = $document->createDocumentFragment();
                foreach (preg_split('/(\bTess\b)/iu', $node->nodeValue, -1, PREG_SPLIT_DELIM_CAPTURE) as $part) {
                    if (preg_match('/^Tess$/iu', $part)) {
                        $span = $document->createElement('span');
                        $span->setAttribute('class', 'tess-name font-name text-[1.25em] leading-[.85] font-bold tracking-normal not-italic');
                        $span->appendChild($document->createTextNode($part));
                        $fragment->appendChild($span);
                    } else {
                        $fragment->appendChild($document->createTextNode($part));
                    }
                }
                $parent->replaceChild($fragment, $node);
            }
            return $document->saveHTML();
        },
    ],
]);
