<?php
    require_once "../includes/db.php";
    require_once "../includes/config.php";
    require_once "../includes/helper.php";
    require_once "../includes/query.php";

    $html_template = '
    <!DOCTYPE html>
    <html lang="en-US">
    <head>
        <meta charset="utf-8">
        <meta property="og:type" content="website"/>
        <link rel="stylesheet" href="/style/$theme/style.css">
        <title>$blog_name | $meta_title</title>
    </head>
    <body>
        <div class="container">
            <header class="banner"><h1>$blog_name</h1></header>
            <section class="left">
                $sidebar_contents
            </section>
            <main class="right">
                $page_contents
            </main>
        </div>
    </body>
    </html>
    ';

    // Check if the user is requesting to view an article
    $article_title = NULL; // used for the html meta title. probably a better way to do this exists?
    if (isset($_GET['id'])) {
        $client_url = filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!article_exists($client_url)) {
            $page_contents = error_404();
        } else {
            $page_contents = article_html($client_url);
            $article_title = get_article($client_url)["title"];
        }
    } else {
        $page_contents = get_article_timeline();
    }

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_contents,
        '$meta_title' => $article_title ? strtolower($article_title) : 'articles'
    );
    echo(strtr($html_template, $translation_array));
?>