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
        $tag_name = $_POST["tag_name"]; // Unique key to identify a tag

        $rowCount = delete_tag($tag_name);

        if ($rowCount == 0) {
            $page_contents = '<h1>Error</h1><p>There was an issue deleting this tag. Would you like to:</p><ul><li><a href="/admin">Return to the admin panel?</a></li></ul>';
        } else {
            $page_contents = '<h1>Success</h1><p>Tag was deleted! Would you like to:</p><ul><li><a href="/admin">Return to the admin panel?</a></li></ul>';
        }
    }

    // Check if the user is requesting to delete a tag
    if (isset($_GET['tag_name'])) {
        $client_tag_name = filter_var($_GET['tag_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!tag_name_exists($client_tag_name)) {
            $page_contents = error_404();
        } else {
            $page_contents = tag_delete_confirm_html($client_tag_name);
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