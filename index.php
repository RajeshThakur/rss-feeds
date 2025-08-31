<?php
// Define categories and their corresponding RSS feeds
$categories = [
    'TechCrunch' => ['https://techcrunch.com/feed/'],
    'Wired' => ['https://www.wired.com/feed/rss'],
    'The Verge' => ['https://www.theverge.com/rss/index.xml'],
    'Mashable' => ['https://mashable.com/feed'],
    'Gizmodo' => ['https://gizmodo.com/rss'],
    'CNET' => ['https://www.cnet.com/rss/all/'],
    'Engadget' => ['https://www.engadget.com/rss.xml'],
    'ZDNet' => ['https://www.zdnet.com/news/rss.xml'],
    'Recode' => ['https://www.vox.com/recode/rss/index.xml'],
    'ReadWrite' => ['https://readwrite.com/feed/'],
    'VentureBeat' => ['https://venturebeat.com/feed/'],
    'MIT Technology Review' => ['https://www.technologyreview.com/feed/'],
    'Digital Trends' => ['https://www.digitaltrends.com/feed/'],
    'Ars Technica' => ['https://feeds.arstechnica.com/arstechnica/index'],
    'Slashdot' => ['http://rss.slashdot.org/Slashdot/slashdot'],
    'TechRadar' => ['https://www.techradar.com/rss'],
    'Computerworld' => ['https://www.computerworld.com/index.rss'],
    'Network World' => ['https://www.networkworld.com/index.rss'],
    'TechRepublic' => ['https://www.techrepublic.com/rssfeeds/articles/'],
    'Tom’s Hardware' => ['https://www.tomshardware.com/feeds/all'],
    'ExtremeTech' => ['https://www.extremetech.com/feed'],
    'Lifehacker' => ['https://lifehacker.com/rss'],
    'How-To Geek' => ['https://www.howtogeek.com/feed/'],
    'MakeUseOf' => ['https://www.makeuseof.com/feed/'],
    'Android Authority' => ['https://www.androidauthority.com/feed'],
    '9to5Mac' => ['https://9to5mac.com/feed/'],
    'AppleInsider' => ['https://appleinsider.com/rss/news'],
    'MacRumors' => ['https://www.macrumors.com/macrumors.xml'],
    'iMore' => ['https://www.imore.com/rss'],
    'Windows Central' => ['https://www.windowscentral.com/rss'],
    'XDA Developers' => ['https://www.xda-developers.com/feed/'],
    'AnandTech' => ['https://www.anandtech.com/rss'],
    'TechSpot' => ['https://www.techspot.com/backend.xml'],
    'BGR' => ['https://bgr.com/feed/'],
    'TechCrunch Startups' => ['https://techcrunch.com/startups/feed/'],
    'Product Hunt' => ['https://www.producthunt.com/feed'],
    'NextWeb' => ['https://thenextweb.com/feed/'],
    'Homes of Silicon Valley' => ['https://homesofsiliconvalley.com/blog/feed/']
];

// Load feeds for selected category
$selectedCategory = $_GET['category'] ?? array_keys($categories)[0];
$feeds = $categories[$selectedCategory];

function fetchFeedItems($urls, $maxItems = 20) {
    $items = [];
    foreach ($urls as $url) {
        $rss = @simplexml_load_file($url);
        if ($rss && isset($rss->channel->item)) {
            foreach ($rss->channel->item as $item) {
                $ns = $item->children('media', true);
                $thumbnail = '';
                if ($ns->content && $ns->content->attributes()->url) {
                    $thumbnail = (string)$ns->content->attributes()->url;
                } else {
                    preg_match('/<img[^>]+src="([^">]+)"/', $item->description, $matches);
                    $thumbnail = $matches[1] ?? 'assets/placeholder.png';
                }

                $items[] = [
                    'title' => (string) $item->title,
                    'link' => (string) $item->link,
                    'description' => strip_tags((string) $item->description),
                    'pubDate' => (string) $item->pubDate,
                    'thumbnail' => $thumbnail
                ];
            }
        }
    }
    usort($items, fn($a, $b) => strtotime($b['pubDate']) - strtotime($a['pubDate']));
    return array_slice($items, 0, $maxItems);
}

$items = fetchFeedItems($feeds);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($selectedCategory) ?> News</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 0; background: #f2f2f2; }
        .header { background: #1a1a1a; color: #fff; padding: 1em; text-align: center; }
        .categories { background: #333; padding: 10px; display: flex; flex-wrap: wrap; justify-content: center; position: sticky; top: 0; z-index: 999; }
        .categories a {
            color: #fff; margin: 0 10px; text-decoration: none;
            padding: 6px 12px; border-radius: 4px;
        }
        .categories a.active, .categories a:hover { background: #007BFF; }
        .grid { display: flex; flex-wrap: wrap; padding: 20px; gap: 20px; justify-content: center; }
        .item {
            background: white; border-radius: 6px;
            width: 300px; overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex; flex-direction: column;
        }
        .item img { width: 100%; height: 180px; object-fit: cover; }
        .item-content { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
        .item-content h3 { margin: 0 0 10px; font-size: 18px; }
        .item-content p { flex-grow: 1; color: #555; font-size: 14px; }
        .read-more { text-align: right; margin-top: 10px; }
        .read-more a {
            color: #007BFF; text-decoration: none;
            font-weight: bold; font-size: 13px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Top Tech Feeds: <?= htmlspecialchars($selectedCategory) ?></h1>
</div>

<div class="categories">
    <?php foreach ($categories as $cat => $_): ?>
        <a href="?category=<?= urlencode($cat) ?>" class="<?= $cat === $selectedCategory ? 'active' : '' ?>">
            <?= htmlspecialchars($cat) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="grid">
    <?php foreach ($items as $item): ?>
        <div class="item">
            <img src="<?= htmlspecialchars($item['thumbnail']) ?>" alt="Thumbnail">
            <div class="item-content">
                <h3><a href="<?= htmlspecialchars($item['link']) ?>" target="_blank"><?= htmlspecialchars($item['title']) ?></a></h3>
                <p><?= mb_strimwidth($item['description'], 0, 120, '...') ?></p>
                <div class="read-more">
                    <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank">Read more →</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
