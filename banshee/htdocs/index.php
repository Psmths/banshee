<?php
    require_once "../includes/db.php";
    require_once "../includes/config.php";
    require_once "../includes/helper.php";
    require_once "../includes/query.php";

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
        <meta name="description" content="$page_description">
        <meta name="referrer" content="no-referrer">
        <meta property="og:type" content="blog">
        <meta property="og:image" content="/resource/img/logo.png">
        <meta property="og:image:type" content="image/png">
        <meta property="og:site_name" content="$blog_name">
        <meta property="og:locale" content="en_US">

        <!-- Page Settings -->
        <title>$blog_name | $page_title</title>

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

    /**
     * build_page()
     *
     * Returns a string containing the full HTML contents of the 
     * page
     *
     * @throws Exception
     * @return array
     */
    function build_page() {
        try {
            $page_content_template = '
                $site_intro
                <h2>Recent Writings</h2>
                $article_list
                <p>For more, visit the <a href="/article">articles</a> directory!</p>
            ';
            $translation_array = array(
                '$site_intro' => SITE_INTRO,
                '$article_list' => get_article_list(get_articles(5, NULL)),
            );
            $page_content = strtr($page_content_template, $translation_array);
            return array(
                'page_content' => $page_content,
                'page_title' => "Home",
                'page_description' => NULL
            );
       } catch (Exception $e) {
            return array(
                'page_content' => error_500(),
                'page_title' => "Error",
                'page_description' => NULL
            );
       }
    }

    // Create and display the page
    $page_content = build_page();

    // Extract return values
    $page_content_body = $page_content["page_content"];
    $page_title = $page_content["page_title"];
    $page_description = $page_content["page_description"];
    
    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => BLOG_TITLE,
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_content_body,
        '$page_title' => $page_title,
        '$page_description' => $page_description ? $page_description : BLOG_DESCRIPTION,
    );

    echo(strtr($html_template, $translation_array));
?>