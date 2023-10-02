<?php
    require_once "../../includes/db.php";
    require_once "../../includes/config.php";
    require_once "../../includes/helper.php";
    require_once "../../includes/query.php";
    require_once "../../includes/info.php";

    $html_template = '
    <!DOCTYPE html>
    <html lang="en-US">
    <head>
        <meta charset="utf-8">
        <meta property="og:type" content="website"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="theme-color" content="#ffaa60" />
        <link rel="stylesheet" href="/style/$theme/style.css">
        <title>$blog_name</title>
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

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => BLOG_TITLE,
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => error_404(),
    );
    echo(strtr($html_template, $translation_array));
?>