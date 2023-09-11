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
                <h2>HTTP Error Code 404: Not Found!</h2>
                <p>The requested page does not exist on the server. This problem can be caused by several issues including: </p>
                <ul>
                    <li>Link is outdated.</li>
                    <li>Page was moved to a new location or was deleted.</li>
                    <li>There might be an error in the address you have entered.</li>
                    <li>The directory structure may have recently been changed or updated.</li>
                    <li>The server felt like it!</li>
                </ul>
                <p>For more information consult 
                <a class="dlink lineitem" href="https://tools.ietf.org/html/rfc7231#section-6.5.4">RFC 7231</a>. 
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