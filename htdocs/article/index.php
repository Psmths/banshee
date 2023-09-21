<?php
    require_once "../../includes/db.php";
    require_once "../../includes/config.php";
    require_once "../../includes/helper.php";
    require_once "../../includes/query.php";

    $html_template = '
    <!DOCTYPE html>
    <html lang="en-US">
    <head>
        <!-- Style Options -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="theme-color" content="#ffaa60" />
        <link rel="stylesheet" href="/style/$theme/style.css">

        <!-- Meta Properties -->
        <meta property="og:type" content="website"/>
        <meta name="application-name" content="$blog_name">
        <meta name="description" content="$blog_description">
        <meta name="referrer" content="no-referrer">
        <meta property="og:type" content="blog">
        <meta property="og:image" content="/resource/img/logo.png">
        <meta property="og:image:type" content="image/png">
        <meta property="og:site_name" content="$blog_name">
        <meta property="og:locale" content="en_US">

        <!-- Page Settings -->
        <title>$blog_name | $meta_title</title>

        <!-- RSS Link -->
        <link rel="alternate" type="application/rss+xml" title="RSS Feed" href="/rss">

        <!-- highlight.js -->
        <script src="/resource/script/highlightjs/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
    </head>
    <body>
        <div class="container">
            <header class="banner"><h1>$blog_name</h1></header>
            <section class="left">
                $sidebar_contents
            </section>
            <main class="right">
                $page_contents<br><br><br>
            </main>
        </div>
    </body>
    </html>
    ';

    // Check if the user is requesting to view an article
    $article_title = NULL; // used for the html meta title. probably a better way to do this exists?
    try {
        if (isset($_GET['id'])) {
            $client_url = filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (!article_exists($client_url)) {
                $page_contents = error_404();
            } else {
                $page_contents = article_html($client_url);
                $article_title = get_article($client_url)["title"];
                $article_content = get_article($client_url)["content"];
            }
        } else {
            $page_contents = get_article_timeline();
        }
    } catch (Exception $e) {
        $page_contents = error_500();
    }

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => BLOG_TITLE,
        '$blog_description' => $article_title ? strip_tags(preg_split('#\r?\n#', $article_content, 2)[0],'') : BLOG_DESCRIPTION,
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_contents,
        '$meta_title' => $article_title ? $article_title : 'articles'
    );
    echo(strtr($html_template, $translation_array));
?>