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
        <title>$blog_name | delete tag</title>

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
            // Deletion confirmation
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Check if the user is requesting to delete a tag
                if (isset($_GET['tag_name'])) {
                    $client_tag_name = filter_var($_GET['tag_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    if (!tag_name_exists($client_tag_name)) {
                        return error_html("ERROR: A tag with the specified name does not exist!");
                    } else {
                        return tag_delete_confirm_html($client_tag_name);
                    }
                }
            }

            // Check if the user is confirming deletion of a tag
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['tag_name'])) {
                    $tag_name = filter_var($_POST["tag_name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    delete_tag($tag_name);
                    return '<h1>Success</h1><p>Tag was deleted! Would you like to:</p><ul><li><a href="/admin">Return to the admin panel?</a></li></ul>';
                }
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