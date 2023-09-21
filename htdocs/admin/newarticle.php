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
        <title>$blog_name | new article</title>

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
                $page_contents
            </main>
        </div>
    </body>
    </html>
    ';

    function build_page() {
        try {
            // Check if the user is trying to start a new article
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                return article_create_html();
            }

            // Check if the user is submitting a new article via POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $url = $_POST["url"];
                $title = $_POST["title"];
                $date = $_POST["date"];
                $content = $_POST["content"];
                $tag_names = $_POST["tags"];
                (isset($_POST["hidden"])) ? $hidden = 1 : $hidden = 0;

                // Create the article in the database
                create_article($url, $title, $date, $content, $tag_names, $hidden);

                // Return a success message
                $return_template = '<h1>Success</h1><p>Article was created! Would you like to:</p><ul><li><a href="/article/?id=$url">View it?</a></li><li><a href="/admin">Return to the admin panel?</a></li></ul>';
                $translation_array = array(
                    '$url' => $_POST["url"]
                );
                return strtr($return_template, $translation_array);
            }

            return error_html("ERROR: No valid HTTP method was supplied for this page!");
            
       } catch (Exception $e) {
            // This is an administrative page, so the error should be returned
           return error_html($e);
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