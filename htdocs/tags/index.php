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
            // Grab taglist contents
            $taglist_html = taglist_html();

            // Client just wants to see a list of tags
            if (!isset($_GET['tag'])) {
                return array(
                    'page_content' => $taglist_html,
                    'page_title' => "Tags",
                    'page_description' => NULL
                );
            }

            // Grab the client's requested tag and sanitize
            $client_tag = filter_var($_GET['tag'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Throw an unspecified error if the filter failed
            if ($client_tag == NULL) {
                return array(
                    'page_content' => error_500(),
                    'page_title' => "Error",
                    'page_description' => NULL
                );
            }

            // Check if the tag exists, if not, return a 404 page
            if (!tag_name_exists($client_tag)) {
                return array(
                    'page_content' => error_404(),
                    'page_title' => "404 Not Found",
                    'page_description' => NULL
                );
            }
            
            // Build page containing the taglist as well as articles containing
            // the specified tag
            $page_content_template = '
            $taglist
            $results_header
            $results
            ';
            $translation_array = array(
                '$taglist' => $taglist_html,
                '$results_header' => "<h2>Articles tagged $client_tag</h2>",
                '$results' => get_article_list(get_articles(NULL, get_tag_id($client_tag))),
            );
            $page_content = strtr($page_content_template, $translation_array);
            return array(
                'page_content' => $page_content,
                'page_title' => "Articles Tagged " . $client_tag,
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