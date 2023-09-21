<?php
    require_once "../../includes/db.php";
    require_once "../../includes/config.php";
    require_once "../../includes/helper.php";
    require_once "../../includes/query.php";

    $xml_template = '<?xml version="1.0" encoding="UTF-8" ?>
    <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
    <title>$blog_name</title>
    <description>$blog_description</description>
    <link>$blog_link</link>
    <atom:link href="$blog_link/rss" rel="self" type="application/rss+xml" />
    $article_feed
    </channel>
    </rss> 
    ';
    
    // Return 404 if RSS is not enabled
    if (!ENABLE_RSS) {
        error_404();
        return;
    }

    header('Content-type: application/xml');

    $articles = get_articles(NULL, NULL);
    $article_feed_xml = '';
    foreach ($articles as $article) {
        $article_template = '
        <item>
        <title>$article_title</title>
        <link>$article_link</link>
        <description>$article_description</description>
        <pubDate>$article_publish_date</pubDate>
        <guid>$article_url</guid>
        </item>';
        $translation_array = array(
            '$article_title' => $article['title'],
            '$article_link' => 'https://' . BLOG_DOMAIN . '/' . $article['url'],
            '$article_description' => strip_tags(preg_split('#\r?\n#', $article['content'], 2)[0]),
            '$article_publish_date' => fmt_rfc822_timestamp($article['timestamp']),
            '$article_url' => 'https://' . BLOG_DOMAIN . '/' . $article['url'],
        );
        $article_feed_xml .= strtr($article_template, $translation_array);
    }

    $translation_array = array(
        '$blog_name' => BLOG_TITLE,
        '$blog_description' => BLOG_DESCRIPTION,
        '$blog_link' => 'https://' . BLOG_DOMAIN,
        '$article_feed' => $article_feed_xml,
    );

    echo(strtr($xml_template, $translation_array));
?>