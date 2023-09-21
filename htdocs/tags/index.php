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
        <title>$blog_name | tags</title>

        <!-- RSS Link -->
        <link rel="alternate" type="application/rss+xml" title="RSS Feed" href="/rss">
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

    function build_page() {
        // Client just wants to see a list of tags
        if (!isset($_GET['tag'])) {
            $taglist_template = '
            $taglist
            ';
            $translation_array = array(
                '$taglist' => taglist_html(),
            );
            return strtr($taglist_template, $translation_array);
        }

        // Grab the client's requested tag
        $client_tag = filter_var($_GET['tag'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Check if the tag exists
        if (!tag_name_exists($client_tag)) {
            return error_404();
        }
        
        // Get a list of articles tagged with the requested tag
        try {
            $page_content_template = '
            $taglist
            $results_header
            $results
            ';
            $translation_array = array(
                '$taglist' => taglist_html(),
                '$results_header' => "<h2>Articles tagged $client_tag</h2>",
                '$results' => get_article_list(get_articles(NULL, get_tag_id($client_tag))),
            );
            return strtr($page_content_template, $translation_array);
       } catch (Exception $e) {
           return error_500();
       }
    }

    $page_content = build_page();
    
    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => BLOG_TITLE,
        '$blog_description' => BLOG_DESCRIPTION,
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_content,
    );

    echo(strtr($html_template, $translation_array));
?>