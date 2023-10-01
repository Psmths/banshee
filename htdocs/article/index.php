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
        <meta name="description" content="$page_description">
        <meta name="referrer" content="no-referrer">
        <meta property="og:type" content="blog">
        <meta property="og:image" content="/resource/img/logo.png">
        <meta property="og:image:type" content="image/png">
        <meta property="og:site_name" content="$blog_name">
        <meta property="og:locale" content="en_US">

        <!-- Page Settings -->
        <title>$blog_name | $page_title</title>

        <!-- RSS Link -->
        <link rel="alternate" type="application/rss+xml" title="RSS Feed" href="/rss">

        <!-- highlight.js -->
        <script src="/resource/script/highlightjs/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
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

    /**
     * build_page()
     *
     * Returns a string containing the full HTML contents of the 
     * page
     *
     * @throws Exception
     * @return array
     */
    function build_page() {
        try {
            // Is the client requesting to view an article?
            if (isset($_GET['id'])) {

                // Filter user input
                $client_url = filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                // Throw an unspecified error if the filter failed
                if ($client_url == NULL) {
                    return array(
                        'page_content' => error_500(),
                        'page_title' => "Error",
                        'page_description' => NULL
                    );
                }

                // Check if the article exists, if not, return a 404 page
                if (!article_exists($client_url)) {
                    return array(
                        'page_content' => error_404(),
                        'page_title' => "404 Not Found",
                        'page_description' => NULL
                    );
                }

                // Build the article to return to the client
                return array(
                    'page_content' => article_html($client_url),
                    'page_title' => get_article_data($client_url)["title"],
                    'page_description' => strip_tags(preg_split('#\r?\n#', get_article_data($client_url)["content"], 2)[0],'')
                );
            }

            // The client is not requesting to view an article, instead,
            // return a list of all articles.
            return array(
                'page_content' => get_article_timeline(),
                'page_title' => "Articles",
                'page_description' => NULL
            );

        } catch (Exception $e) {
            return array(
                'page_content' => error_500(),
                'page_title' => "Error",
                'page_description' => NULL
            );
        }
    }

    // Create and display the page
    $page_content = build_page();

    // Extract return values
    $page_content_body = $page_content["page_content"];
    $page_title = $page_content["page_title"];
    $page_description = $page_content["page_description"];

    $translation_array = array(
        '$theme' => BLOG_THEME,
        '$blog_name' => BLOG_TITLE,
        '$sidebar_contents' => SIDEBAR_CONTENTS,
        '$page_contents' => $page_content_body,
        '$page_title' => $page_title,
        '$page_description' => $page_description ? $page_description : BLOG_DESCRIPTION, 
    );

    echo(strtr($html_template, $translation_array));
?>