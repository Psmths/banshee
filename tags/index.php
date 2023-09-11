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
        <title>$blog_name | tags</title>
    </head>
    <body>
        <div class="container">
            <header class="banner"><h1>$blog_name</h1></header>
            <section class="left">
                $sidebar_contents
            </section>
            <main class="right">
                $taglist
                $results_header
                $results
            </main>
        </div>
    </body>
    </html>
    ';

    $results_header = NULL;
    $results = NULL;

    // Check if the user is requesting to view an article
    if (isset($_GET['tag'])) {
        $client_tag = filter_var($_GET['tag'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (tag_name_exists($client_tag)) {
            $results_header = "<h2>Articles tagged $client_tag</h2>";
            $results = get_article_list(get_articles(NULL, get_tag_id($client_tag)));
        } 
    }

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$taglist' => taglist_html(),
        '$results_header' => $results_header,
        '$results' => $results,
    );

    echo(strtr($html_template, $translation_array));
?>