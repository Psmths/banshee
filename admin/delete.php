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

    // Check if the user is confirming deletion of an article
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $url = $_POST["url"]; // Unique key to identify an article

        $rowCount = delete_article($url);

        if ($rowCount == 0) {
            $page_contents_template = '<h1>Error</h1><p>There was an issue deleting this article. Would you like to:</p><ul><li><a href="/admin">Return to the admin panel?</a></li></ul>';
            $translation_array = array(
                '$url' => $_POST["url"]
            );
            $page_contents = strtr($page_contents_template, $translation_array);
        } else {
            $page_contents_template = '<h1>Success</h1><p>Article was deleted! Would you like to:</p><ul><li><a href="/admin">Return to the admin panel?</a></li></ul>';
            $translation_array = array(
                '$url' => $_POST["url"]
            );
            $page_contents = strtr($page_contents_template, $translation_array);
        }
    }

    // Check if the user is requesting to view an article
    if (isset($_GET['id'])) {
        $client_url = filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!article_exists($client_url)) {
            $page_contents = error_404();
        } else {
            $page_contents = article_delete_confirm_html($client_url);
        }
    }

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_contents,
    );
    echo(strtr($html_template, $translation_array));
?>