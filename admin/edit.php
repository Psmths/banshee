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

    // Check if the user is submitting changes to an article via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $url = $_POST["url"]; // Unique key to identify an article
        $title = $_POST["title"];
        $date = $_POST["date"];
        $content = $_POST["content"];
        $tag_names = $_POST["tags"];
        
        (isset($_POST["hidden"])) ? $hidden = 1 : $hidden = 0;

        $rowCount = update_article($url, $title, $date, $content, $tag_names, $hidden);

        if ($rowCount == 0) {
            $page_contents_template = '<h1>Error</h1><p>There was an issue updating this article. Would you like to:</p><ul><li><a href="/admin/edit.php?id=$url">Try again?</a></li><li><a href="/article/?id=$url">View it?</a></li><li><a href="/admin">Return to the admin panel?</a></li></ul>';
            $translation_array = array(
                '$url' => $_POST["url"]
            );
            $page_contents = strtr($page_contents_template, $translation_array);
        } else {
            $page_contents_template = '<h1>Success</h1><p>Article was updated! Would you like to:</p><ul><li><a href="/article/?id=$url">View it?</a></li><li><a href="/admin">Return to the admin panel?</a></li></ul>';
            $translation_array = array(
                '$url' => $_POST["url"]
            );
            $page_contents = strtr($page_contents_template, $translation_array);
        }
    } elseif (isset($_GET['id'])) { // Check if the user is requesting to view an article
        $client_url = filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!article_exists($client_url)) {
            $page_contents = error_404();
        } else {
            $page_contents = article_edit_html($client_url);
        }
    } else {
        $page_contents = error_404();
    }

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_contents,
    );
    echo(strtr($html_template, $translation_array));
?>