<?php
    require_once "./includes/db.php";
    require_once "./includes/config.php";
    require_once "./includes/helper.php";
    require_once "./includes/query.php";

    $html_template = '
    <!DOCTYPE html>
    <html lang="en-US">
    <head>
        <meta charset="utf-8">
        <meta property="og:type" content="website"/>
        <link rel="stylesheet" href="/style/$theme/style.css">
        <title>$blog_name | home</title>
    </head>
    <body>
        <div class="container">
            <header class="banner"><h1>$blog_name</h1></header>
            <section class="left">
                $sidebar_contents
            </section>
            <main class="right">
                $site_intro
                <h2>Recent Writings</h2>
                $article_list
                <p>For more, visit the <a href="/article">articles</a> directory!</p>
            </main>
        </div>
    </body>
    </html>
    ';
    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$site_intro' => SITE_INTRO,
        '$article_list' => get_article_list(get_articles(5, NULL)),
    );
    echo(strtr($html_template, $translation_array));
?>