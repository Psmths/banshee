<?php
    require_once "../includes/db.php";
    require_once "../includes/config.php";
    require_once "../includes/helper.php";
    require_once "../includes/query.php";
    require_once "../includes/info.php";

    $html_template = '
    <!DOCTYPE html>
    <html lang="en-US">
    <head>
        <meta charset="utf-8">
        <meta property="og:type" content="website"/>
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
                $page_contents
            </main>
        </div>
    </body>
    </html>
    ';

    $page_contents = admin_html();

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_contents,
    );
    echo(strtr($html_template, $translation_array));
?>