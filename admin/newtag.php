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

    // Check if the user is submitting a new article via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tag_name = $_POST["tag_name"];

        $return = create_tag($tag_name);

        if ($return) {
            $page_contents_template = '<h1>Error</h1><p>There was an issue creating this tag. Would you like to:</p><ul><li><a href="/admin/newtag.php">Try again?</a></li><li><a href="/admin">Return to the admin panel?</a></li></ul><p>The error returned was:</p><div class="error-block">$error</div>';
            $translation_array = array(
                '$error' => $return
            );
            $page_contents = strtr($page_contents_template, $translation_array);
        } else {
            $page_contents = '<h1>Success</h1><p>Tag was created! Would you like to:</p><ul><li><a href="/admin">Return to the admin panel?</a></li></ul>';
        }
    } else {
        $page_contents = tag_create_html();
    }
    
    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => strtolower(BLOG_TITLE),
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_contents,
    );
    echo(strtr($html_template, $translation_array));
?>