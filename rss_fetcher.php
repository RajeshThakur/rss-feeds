<?php
function fetchRssFeed($url) {
    $rss = @simplexml_load_file($url);
    if (!$rss) return [];

    $namespaces = $rss->getNamespaces(true);
    $items = [];

    foreach ($rss->channel->item as $entry) {
        $title = (string) $entry->title;
        $link = (string) $entry->link;
        $pubDate = (string) $entry->pubDate;
        $description = (string) $entry->description;

        $image = null;

        // Case 1: media:thumbnail or media:content
        if (isset($namespaces['media'])) {
            $media = $entry->children($namespaces['media']);
            if (isset($media->thumbnail)) {
                $image = (string) $media->thumbnail->attributes()->url;
            } elseif (isset($media->content)) {
                $image = (string) $media->content->attributes()->url;
            }
        }

        // Case 2: extract first <img> from description
        if (!$image && preg_match('/<img.*?src=["\'](.*?)["\']/', $description, $matches)) {
            $image = $matches[1];
        }

        $items[] = compact('title', 'link', 'pubDate', 'description', 'image');
    }

    return $items;
}
