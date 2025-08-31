<?php
$feeds = require 'feed_list.php';
require 'rss_fetcher.php';

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = 9;

$allArticles = [];

foreach ($feeds as $source => $url) {
    $articles = fetchRssFeed($url);
    foreach (array_slice($articles, 0, 5) as $article) {
        $allArticles[] = [
            'title' => $article['title'],
            'link' => $article['link'],
            'pubDate' => date("d M Y", strtotime($article['pubDate'])),
            'image' => $article['image'],
            'source' => $source
        ];
    }
}

// Sort by date desc (optional)
usort($allArticles, function($a, $b) {
    return strtotime($b['pubDate']) - strtotime($a['pubDate']);
});

$paginated = array_slice($allArticles, $offset, $limit);

header('Content-Type: application/json');
echo json_encode($paginated);
