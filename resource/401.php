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
                <h2>HTTP Error Code 401: Unauthorized!</h2>
                <p>The request has not been applied because it lacks valid authentication credentials for the target resource.</p>
                <p>For more information consult 
                <a class="dlink lineitem" href="https://datatracker.ietf.org/doc/html/rfc7235#section-3.1">RFC 7231</a>. 
            </main>
        </div>
    </body>
    </html>
    ';

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_contents,
    );
    echo(strtr($html_template, $translation_array));
?>