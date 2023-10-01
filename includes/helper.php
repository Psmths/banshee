<?php
    /**
     * fmt_sql_timestamp(sql_timestamp)
     *
     * Given a timestamp returned by SQL, convert to 
     * human-friendly formatting (M j, Y).
     * 
     * @param string $sql_timestamp
     * @return string
     */
    function fmt_sql_timestamp($sql_timestamp) {
        return date('F j, Y', strtotime($sql_timestamp));
    }

     /**
     * fmt_sql_timestamp_date_form(sql_timestamp)
     *
     * Given a timestamp returned by SQL, convert to 
     * HTML form-friendly formatting (Y-m-d).
     * 
     * @param string $sql_timestamp
     * @return string
     */
    function fmt_sql_timestamp_date_form($sql_timestamp) {
        return date('Y-m-d', strtotime($sql_timestamp));
    }

    /**
     * fmt_rfc822_timestamp(sql_timestamp)
     *
     * Given a timestamp returned by SQL, convert to 
     * RFC 822 format.
     * 
     * @param string $sql_timestamp
     * @return string
     */
    function fmt_rfc822_timestamp($sql_timestamp) {
        return date(DateTime::RFC822, strtotime($sql_timestamp));
    }

    /**
     * sql_timestamp_get_year(sql_timestamp)
     *
     * Given a timestamp returned by SQL, convert to 
     * just a year (yyyy).
     * 
     * @param string $sql_timestamp
     * @return string
     */
    function sql_timestamp_get_year($sql_timestamp) {
        return date('Y', strtotime($sql_timestamp));
    }

    /**
     * format_bytes(bytes)
     *
     * Given a number of bytes, return in a human 
     * human-friendly formatting (B, KB, MB, etc.).
     * 
     * @param string $bytes
     * @return string
     */
    function format_bytes($bytes) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        $bytes /= (1 << (10 * $pow)); 

        return implode(" ", Array(round($bytes, 2), $units[$pow])); 
    } 

    // error_404()
    //
    // Initiates a 404 error and returns the error page content
    function error_404() {
        header("HTTP/1.1 404 Not Found");
        $html_template = '
        <h2>HTTP Error Code 404: Not Found</h2>
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
        ';
        return $html_template;
    }

    // error_401()
    //
    // Initiates a 401 error and returns the error page content
    function error_401() {
        header("HTTP/1.1 401 Unauthorized");
        $html_template = '
        <h2>HTTP Error Code 401: Unauthorized</h2>
        <p>The request has not been applied because it lacks valid authentication credentials for the target resource.</p>
        ';
        return $html_template;
    }

    // error_500()
    //
    // Initiates a 500 Internal Server Error and returns the error page content
    function error_500() {
        header("HTTP/1.1 500 Internal Server Error");
        $html_template = '
        <h2>HTTP Error Code 500: Internal Server Error</h2>
        <p>An internal server error has occurred. This is a generic error message indicating that something went wrong on the server. The server might be experiencing technical difficulties, or there could be an issue with the web application.</p>
        <p>Please try again later, or contact the website administrator for assistance.</p>
        ';
        return $html_template;
    }

    // tags_csv_string()
    // 
    // Returns a CSV string of article tags by article URL column
    function tags_csv_string($url) {
        // Get tags and convert to CSV string
        $article_tag_array = get_article_tags($url);
        $article_tag_str_array = Array();
        foreach ($article_tag_array as $tag_id) {
            array_push($article_tag_str_array, '<a href="/tags/?tag=' . get_tag_name($tag_id) . '">' . get_tag_name($tag_id) . '</a>');
        }
        $tags_csv = implode(", ", $article_tag_str_array);
        return $tags_csv;
    }

    // taglist_html()
    // 
    // Returns an html listing of all tags
    function taglist_html() {
        // Get tags and convert to CSV string
        $tag_names = get_all_tag_names();

        // Construct all the links
        $html_tag_link_template = '<a href="/tags/?tag=$tag_name">$tag_name ($tag_count)</a><br>';
        $tag_links = "";
        foreach ($tag_names as $tag_name) {
            // Only list tags that have articles associated with them
            if ($tag_name["quantity"] > 0) {
                $translation_array = array(
                    '$tag_name' => $tag_name["tag_name"],
                    '$tag_count' => $tag_name["quantity"]
                );
                $tag_links .= strtr($html_tag_link_template, $translation_array);
            }
        }

        // Construct and return the taglist
        $html_article_template = '
        <h1>
            Tags
        </h1>
        $tag_links
        ';

        $translation_array = array(
            '$tag_links' => $tag_links
        );
        return strtr($html_article_template, $translation_array);
    }

    // article_html()
    // 
    // Returns HTML for an article if the article exists by URL
    // 
    // Parameters
    // url
    function article_html($url) {
        $article = get_article_data($url);

        // Construct and return the HTML for the article body
        $html_article_template = '
        <h1 class="nomarginbottom">$title</h1>
        <i class="tags">$date</i>
        $content
        <i class="tags"><svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M.5 7.5l-.354-.354a.5.5 0 000 .708L.5 7.5zm7 7l-.354.354a.5.5 0 00.708 0L7.5 14.5zm7-7l.354.354A.5.5 0 0015 7.5h-.5zm-7-7V0a.5.5 0 00-.354.146L7.5.5zM.146 7.854l7 7 .708-.708-7-7-.708.708zm7.708 7l7-7-.708-.708-7 7 .708.708zM15 7.5v-6h-1v6h1zM13.5 0h-6v1h6V0zM7.146.146l-7 7 .708.708 7-7-.708-.708zM15 1.5A1.5 1.5 0 0013.5 0v1a.5.5 0 01.5.5h1zM10.5 5a.5.5 0 01-.5-.5H9A1.5 1.5 0 0010.5 6V5zm.5-.5a.5.5 0 01-.5.5v1A1.5 1.5 0 0012 4.5h-1zm-.5-.5a.5.5 0 01.5.5h1A1.5 1.5 0 0010.5 3v1zm0-1A1.5 1.5 0 009 4.5h1a.5.5 0 01.5-.5V3z" fill="currentColor"></path></svg>&emsp;$tags</i><br><br>
        ';

        $translation_array = array(
            '$title' => $article["title"],
            '$date' => fmt_sql_timestamp($article["timestamp"]),
            '$content' => $article["content"],
            '$tags' => tags_csv_string($article["url"])
        );
        return strtr($html_article_template, $translation_array);
    }

    // get_article_list()
    // 
    // Parameters
    //  articles_array      array of articles to display
    function get_article_list($articles_array) {
        $html = "";
        $html_article_template = '
        <div class="articlebody">
            <h3 class="nomarginbottom">
                $date ｜ <a class="headerlink" href="/article/?id=$url">$title</a>
            </h3>
            <i class="tags">
            <svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M.5 7.5l-.354-.354a.5.5 0 000 .708L.5 7.5zm7 7l-.354.354a.5.5 0 00.708 0L7.5 14.5zm7-7l.354.354A.5.5 0 0015 7.5h-.5zm-7-7V0a.5.5 0 00-.354.146L7.5.5zM.146 7.854l7 7 .708-.708-7-7-.708.708zm7.708 7l7-7-.708-.708-7 7 .708.708zM15 7.5v-6h-1v6h1zM13.5 0h-6v1h6V0zM7.146.146l-7 7 .708.708 7-7-.708-.708zM15 1.5A1.5 1.5 0 0013.5 0v1a.5.5 0 01.5.5h1zM10.5 5a.5.5 0 01-.5-.5H9A1.5 1.5 0 0010.5 6V5zm.5-.5a.5.5 0 01-.5.5v1A1.5 1.5 0 0012 4.5h-1zm-.5-.5a.5.5 0 01.5.5h1A1.5 1.5 0 0010.5 3v1zm0-1A1.5 1.5 0 009 4.5h1a.5.5 0 01.5-.5V3z" fill="currentColor"></path></svg>&emsp;$tags_csv
            </i>
            $first_paragraph
        </div>
        ';

        foreach ($articles_array as $article) {
            // Get first p element for preview
            $content = $article["content"];
            $first_paragraph = preg_split('#\r?\n#', $article["content"], 2)[0];

            $translation_array = array(
                '$title' => $article["title"],
                '$url' => $article["url"],
                '$date' => fmt_sql_timestamp($article["timestamp"]),
                '$first_paragraph' => $first_paragraph,
                '$tags_csv' => tags_csv_string($article["url"]),
            );
            $html .= strtr($html_article_template, $translation_array);
        }
        return $html;
    }

    // get_article_timeline()
    // 
    // Returns a timeline of all articles by year (the pretty version of the list!)
    function get_article_timeline() {
        $html = "<h1>Index of Articles</h1>";
        $html_article_template = '
        <div class="article-list">
            <div class="article-list-date">
                $date
            </div>
            <div class="article-list-link">
                <a href="/article/?id=$url">$title</a>
            </div>
        </div>
        ';

        $result = get_articles(NULL, NULL);

        $current_year = "";
        foreach ($result as $article) {
            // Check if a new yearly header is required
            $article_year = sql_timestamp_get_year($article["timestamp"]);
            if ($article_year != $current_year) {
                $current_year = $article_year;
                $html .= "<h3>$current_year</h3>";
            }

            // Add the article listing to the html content
            $translation_array = array(
                '$title' => $article["title"],
                '$url' => $article["url"],
                '$date' => fmt_sql_timestamp($article["timestamp"]),
            );
            $html .= strtr($html_article_template, $translation_array);
        }
    
        return $html;
    }

    // article_admin_list()
    function article_admin_list($articles_array) {
        $html = '';
        $html_article_template = '
        <div class="aeitem">
        <svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M.5 7.5l-.464-.186a.5.5 0 000 .372L.5 7.5zm14 0l.464.186a.5.5 0 000-.372L14.5 7.5zm-7 4.5c-2.314 0-3.939-1.152-5.003-2.334a9.368 9.368 0 01-1.449-2.164 5.065 5.065 0 01-.08-.18l-.004-.007v-.001L.5 7.5l-.464.186v.002l.003.004a2.107 2.107 0 00.026.063l.078.173a10.368 10.368 0 001.61 2.406C2.94 11.652 4.814 13 7.5 13v-1zm-7-4.5l.464.186.004-.008a2.62 2.62 0 01.08-.18 9.368 9.368 0 011.449-2.164C3.56 4.152 5.186 3 7.5 3V2C4.814 2 2.939 3.348 1.753 4.666a10.367 10.367 0 00-1.61 2.406 6.05 6.05 0 00-.104.236l-.002.004v.001H.035L.5 7.5zm7-4.5c2.314 0 3.939 1.152 5.003 2.334a9.37 9.37 0 011.449 2.164 4.705 4.705 0 01.08.18l.004.007v.001L14.5 7.5l.464-.186v-.002l-.003-.004a.656.656 0 00-.026-.063 9.094 9.094 0 00-.39-.773 10.365 10.365 0 00-1.298-1.806C12.06 3.348 10.186 2 7.5 2v1zm7 4.5a68.887 68.887 0 01-.464-.186l-.003.008-.015.035-.066.145a9.37 9.37 0 01-1.449 2.164C11.44 10.848 9.814 12 7.5 12v1c2.686 0 4.561-1.348 5.747-2.665a10.366 10.366 0 001.61-2.407 6.164 6.164 0 00.104-.236l.002-.004v-.001h.001L14.5 7.5zM7.5 9A1.5 1.5 0 016 7.5H5A2.5 2.5 0 007.5 10V9zM9 7.5A1.5 1.5 0 017.5 9v1A2.5 2.5 0 0010 7.5H9zM7.5 6A1.5 1.5 0 019 7.5h1A2.5 2.5 0 007.5 5v1zm0-1A2.5 2.5 0 005 7.5h1A1.5 1.5 0 017.5 6V5z" fill="currentColor"></path></svg>&emsp;<a href="/article?id=$url">View</a>
        <svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 8.5l-.354-.354L4 8.293V8.5h.5zm4-4l.354-.354a.5.5 0 00-.708 0L8.5 4.5zm2 2l.354.354a.5.5 0 000-.708L10.5 6.5zm-4 4v.5h.207l.147-.146L6.5 10.5zm-2 0H4a.5.5 0 00.5.5v-.5zm3 3.5A6.5 6.5 0 011 7.5H0A7.5 7.5 0 007.5 15v-1zM14 7.5A6.5 6.5 0 017.5 14v1A7.5 7.5 0 0015 7.5h-1zM7.5 1A6.5 6.5 0 0114 7.5h1A7.5 7.5 0 007.5 0v1zm0-1A7.5 7.5 0 000 7.5h1A6.5 6.5 0 017.5 1V0zM4.854 8.854l4-4-.708-.708-4 4 .708.708zm3.292-4l2 2 .708-.708-2-2-.708.708zm2 1.292l-4 4 .708.708 4-4-.708-.708zM6.5 10h-2v1h2v-1zm-1.5.5v-2H4v2h1z" fill="currentColor"></path></svg>&emsp;<a href="/admin/edit.php?id=$url">Edit</a>
        <svg class="svg-button-label-red" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 .5H1.5a1 1 0 00-1 1V4M6 .5h3m2 0h2.5a1 1 0 011 1V4M.5 6v3m14-3v3m-14 2v2.5a1 1 0 001 1H4M14.5 11v2.5a1 1 0 01-1 1H11m-7-7h7m-5 7h3" stroke="currentColor"></path></svg>&emsp;<a href="/admin/delete.php?id=$url" class="danger">Delete</a>
            <p class="$class">$title</p>
        </div>
        ';
        foreach ($articles_array as $article) {
            $translation_array = array(
                '$title' => $article["title"],
                '$url' => $article["url"],
                '$class' => ($article["hidden"] != 1) ? 'thinp' : 'thinp hidden-item',
            );
            $html .= strtr($html_article_template, $translation_array);
        }
        return $html;
    }

    // tags_admin_list()
    function tags_admin_list($tags_array) {
        $html = "";
        $html_taglist_template = '
        <div class="aeitem">
        <svg class="svg-button-label-red" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 .5H1.5a1 1 0 00-1 1V4M6 .5h3m2 0h2.5a1 1 0 011 1V4M.5 6v3m14-3v3m-14 2v2.5a1 1 0 001 1H4M14.5 11v2.5a1 1 0 01-1 1H11m-7-7h7m-5 7h3" stroke="currentColor"></path></svg>&emsp;<a href="/admin/deletetag.php?tag_name=$tag_name" class="danger">Delete</a>
            <a style="text-decoration: none;">( $tag_count )</a><p class="thinp">$tag_name</p>
        </div>
        ';
        foreach ($tags_array as $tag_name) {
            $translation_array = array(
                '$tag_name' => $tag_name["tag_name"],
                '$tag_count' => count_tag($tag_name["tag_name"], True),
            );
            $html .= strtr($html_taglist_template, $translation_array);
        }
        return $html;
    }

    // article_edit_html()
    function article_edit_html($url) {
        $article = get_article_data($url);

        // Construct and return the HTML
        $html_article_template = '
        <h1>Edit Article</h1>
        <form method="post" action="/admin/edit.php">
            <label for="title">Title</label><br>
            <input type="text" name="title" value="$title" id="title" required><br>

            <label for="date">Date</label><br>
            <input type="date" name="date" value="$date" id="date" required><br>

            <label for="tags">Tags</label><br>
            <select name="tags[]" multiple required>
                $tag_options
            </select><br>

            <label for="content">Content (HTML)</label><br>
            <textarea type="text" name="content" id="content" required>$content</textarea><br>

            <input type="hidden" name="url" value="$url" />

            <input type="checkbox" id="hidden" name="hidden" $checked />
            <label for="hidden">Hidden</label><br>

            <input type="submit" value="Update Article"><br><br>
        </form>
        ';

        $tags = get_all_tag_names();
        $tag_options = "";
        foreach ($tags as $tag) {
            $tag_options_template = '<option $selected>$tag_name</option>';
            $translation_array = array(
                '$tag_name' => $tag["tag_name"],
                '$selected' => is_article_tagged($url, $tag["tag_name"]) ? "selected" : ""
            );
            $tag_options .= strtr($tag_options_template, $translation_array);
        }

        $translation_array = array(
            '$title' => htmlspecialchars($article["title"]),
            '$url' => htmlspecialchars($article["url"]),
            '$date' => fmt_sql_timestamp_date_form($article["timestamp"]),
            '$content' => htmlspecialchars($article["content"]),
            '$tag_options' => $tag_options,
            '$checked' => ($article["hidden"] == 1) ? 'checked' : '',
        );
        return strtr($html_article_template, $translation_array);
    }

    // settings_edit_html()
    function settings_edit_html() {

        // Construct and return the HTML
        $html_article_template = '
        <h1>Edit Settings</h1>
        <form method="post" action="/admin/settings.php">
            <div class="aeitem">
                <label for="title">Blog Title</label>
                <input type="text" name="title" value="$title" id="title" required><br>
            </div>

            <div class="aeitem">
                <label for="title">Blog Domain Name</label>
                <input type="text" name="title" value="$domain" id="title" required><br>
            </div>

            <div class="aeitem">
                <label for="title">Blog Theme</label>
                <select name="theme" required>
                    <option>Rhino</option>
                    <option>Wukong</option>
                    <option>Shabu Shabu</option>
                </select>
            </div>

            <br>

            <label for="content">Blog Description</label>
            <small>&emsp;This will appear in the HTML meta tags.</small>
            <textarea class="smaller-textarea" type="text" name="content" id="content" required>$description</textarea><br>

            <label for="content">Sidebar Content (HTML)</label>
            <small>&emsp;This will appear in the sidebar of your blog.</small>
            <textarea class="smaller-textarea" type="text" name="content" id="content" required>$sidebar_content_html</textarea><br>

            <label for="content">Intro (HTML)</label>
            <small>&emsp;This will appear on the homepage of your blog.</small>
            <textarea class="smaller-textarea" type="text" name="content" id="content" required>$intro_content_html</textarea><br>

            <br>

            <input type="checkbox" id="rss_enabled" name="rss_enabled" checked />
            <label for="rss_enabled">Enable RSS</label><br>

            <br>
            <input type="submit" value="Save Changes"><br><br>
        </form>
        ';

        $translation_array = array(
        );
        return strtr($html_article_template, $translation_array);
    }

    // article_create_html()
    function article_create_html() {
        // Construct and return the HTML
        $html_article_template = '
        <h1>Create New Article</h1>
        <form method="post" action="/admin/newarticle.php">
            <label for="title">Title</label><br>
            <input type="text" name="title" value="" placeholder="Enter your article title here" id="title" required><br>

            <label for="url">Article URL</label><br>
            <small><i>This value cannot be changed later!</i></small>
            <input type="text" name="url" value="" placeholder="Enter the URL that will uniquely identify this article" required><br>

            <label for="date">Date</label><br>
            <input type="date" name="date" value="" id="date" required><br>

            <label for="tags">Tags</label><br>
            <select name="tags[]" multiple required>
                $tag_options
            </select><br>

            <label for="content">Content (HTML)</label><br>
            <textarea type="text" name="content" id="content" required>Start editing your new article here. You may format the article in HTML.</textarea><br>

            <input type="checkbox" id="hidden" name="hidden" checked />
            <label for="hidden">Hide article?</label><br>

            <input type="submit" value="Create Article"><br><br>
        </form>
        ';

        $tag_names = get_all_tag_names();
        $tag_options = "";
        foreach ($tag_names as $tag_name) {
            $tag_options_template = '<option>$tag_name</option>';
            $translation_array = array(
                '$tag_name' => $tag_name["tag_name"]
            );
            $tag_options .= strtr($tag_options_template, $translation_array);
        }

        $translation_array = array(
            '$tag_options' => $tag_options
        );
        return strtr($html_article_template, $translation_array);
    }

    // tag_create_html()
    function tag_create_html() {
        // Construct and return the HTML
        $html_tag_template = '
        <h1>Create New Tag</h1>
        <form method="post" action="/admin/newtag.php">
            <label for="tag_name">Tag Name</label><br>
            <input type="text" name="tag_name" value="" placeholder="Enter your tag name here" id="tag_name" required><br>

            <input type="submit" value="Create Tag"><br><br>
        </form>
        ';
        return $html_tag_template;
    }

    // article_delete_confirm_html()
    function article_delete_confirm_html($url) {
        $article = get_article_data($url);

        // Construct and return the HTML
        $html_article_template = '
        <h1>Delete Article</h1>
        <p>Are you sure you wish to delete the following article: <b>$title?</b></p>
        <p class="danger">This action cannot be undone.</p>
        <form method="post" action="/admin/delete.php">
            <input type="hidden" name="url" value="$url" />
            <input type="submit" value="Yes"><br>
        </form>
        ';

        $translation_array = array(
            '$title' => $article["title"],
            '$url' => $article["url"]
        );
        return strtr($html_article_template, $translation_array);
    }

    // tag_delete_confirm_html()
    function tag_delete_confirm_html($tag_name) {
        // Construct and return the HTML
        $html_tag_template = '
        <h1>Delete Tag</h1>
        <p>Are you sure you wish to delete the following tag: <b>$tag_name?</b></p>
        <p class="danger">This action cannot be undone.</p>
        <form method="post" action="/admin/deletetag.php">
            <input type="hidden" name="tag_name" value="$tag_name" />
            <input type="submit" value="Yes"><br>
        </form>
        ';

        $translation_array = array(
            '$tag_name' => $tag_name
        );
        return strtr($html_tag_template, $translation_array);
    }

    function error_html($error_message) {
        $page_contents_template = '<h1>Application Error</h1><p>An error was encountered while processing this request.<p>The error returned was:</p><div class="error-block">$error</div>';
        $translation_array = array(
            '$error' => $error_message
        );
        return strtr($page_contents_template, $translation_array);
    }

    // admin_html()
    // 
    // Returns HTML for the admin control panel
    function admin_html() {
        // Construct and return the HTML for the admin panel
        $html_article_template = '
        <h1>Admin Control Panel</h1>
        <h2>Quick Actions</h2>

        <svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5.5l.354-.354L10.707 0H10.5v.5zm3 3h.5v-.207l-.146-.147-.354.354zm-1 10.5h-10v1h10v-1zM2 13.5v-12H1v12h1zM2.5 1h8V0h-8v1zM13 3.5v10h1v-10h-1zM10.146.854l3 3 .708-.708-3-3-.708.708zM2.5 14a.5.5 0 01-.5-.5H1A1.5 1.5 0 002.5 15v-1zm10 1a1.5 1.5 0 001.5-1.5h-1a.5.5 0 01-.5.5v1zM2 1.5a.5.5 0 01.5-.5V0A1.5 1.5 0 001 1.5h1zM7 5v5h1V5H7zM5 8h5V7H5v1z" fill="currentColor"></path></svg>&emsp;<a href ="/admin/newarticle.php">Create a new article</a><br>
        <svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M.5 7.5l-.354-.354a.5.5 0 000 .708L.5 7.5zm7 7l-.354.354a.5.5 0 00.708 0L7.5 14.5zm7-7l.354.354A.5.5 0 0015 7.5h-.5zm-7-7V0a.5.5 0 00-.354.146L7.5.5zM.146 7.854l7 7 .708-.708-7-7-.708.708zm7.708 7l7-7-.708-.708-7 7 .708.708zM15 7.5v-6h-1v6h1zM13.5 0h-6v1h6V0zM7.146.146l-7 7 .708.708 7-7-.708-.708zM15 1.5A1.5 1.5 0 0013.5 0v1a.5.5 0 01.5.5h1zM10.5 5a.5.5 0 01-.5-.5H9A1.5 1.5 0 0010.5 6V5zm.5-.5a.5.5 0 01-.5.5v1A1.5 1.5 0 0012 4.5h-1zm-.5-.5a.5.5 0 01.5.5h1A1.5 1.5 0 0010.5 3v1zm0-1A1.5 1.5 0 009 4.5h1a.5.5 0 01.5-.5V3z" fill="currentColor"></path></svg>&emsp;<a href ="/admin/newtag.php">Create a new tag</a><br>
        <svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M5.944.5l-.086.437-.329 1.598a5.52 5.52 0 00-1.434.823L2.487 2.82l-.432-.133-.224.385L.724 4.923.5 5.31l.328.287 1.244 1.058c-.045.277-.103.55-.103.841 0 .291.058.565.103.842L.828 9.395.5 9.682l.224.386 1.107 1.85.224.387.432-.135 1.608-.537c.431.338.908.622 1.434.823l.329 1.598.086.437h3.111l.087-.437.328-1.598a5.524 5.524 0 001.434-.823l1.608.537.432.135.225-.386 1.106-1.851.225-.386-.329-.287-1.244-1.058c.046-.277.103-.55.103-.842 0-.29-.057-.564-.103-.841l1.244-1.058.329-.287-.225-.386-1.106-1.85-.225-.386-.432.134-1.608.537a5.52 5.52 0 00-1.434-.823L9.142.937 9.055.5H5.944z" stroke="currentColor" stroke-linecap="square" stroke-linejoin="round"></path><path clip-rule="evenodd" d="M9.5 7.495a2 2 0 01-4 0 2 2 0 014 0z" stroke="currentColor" stroke-linecap="square" stroke-linejoin="round"></path></svg>&emsp;<a href ="/admin/settings.php">Adjust blog settings</a><br>
        <svg class="svg-button-label-accent" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 14.5v-3a1 1 0 011-1h4a1 1 0 011 1v3m3 0h-12a1 1 0 01-1-1v-12a1 1 0 011-1h8.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V13.5a1 1 0 01-1 1z" stroke="currentColor"></path></svg>&emsp;<a href ="/admin/export.php">Export Database</a><br>

        <h2>Articles ( $article_count )</h2>
        $article_admin_list
        <small>ℹ️&emsp;<i>Articles highlighted in blue are hidden from public index. They may still be accessed via their URL.</i></small>
        <h2>Tags ( $tag_count )</h2>
        $tags_admin_list
        <h2>Software Information</h2>
        <table class="infotable">
            <tr>
                <td>Software Version</td>
                <td>$SOFTWARE_VERSION</td>
            </tr>
            <tr>
                <td>Software License</td>
                <td>$SOFTWARE_LICENSE</td>
            </tr>
            <tr>
                <td>Blog Title</td>
                <td>$BLOG_TITLE</td>
            </tr>
            <tr>
                <td>Database Server</td>
                <td>$DB_SERVER</td>
            </tr>
            <tr>
                <td>Database Size</td>
                <td>$DB_SIZE_B</td>
            </tr>
            <tr>
                <td>Total Articles</td>
                <td>$article_count</td>
            </tr>
            <tr>
                <td>Total Tags</td>
                <td>$tag_count</td>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td>$PHP_VERSION</td>
            </tr>
        </table>
        ';

        try {
            $translation_array = array(
                '$article_admin_list' => article_admin_list(get_articles(NULL,NULL,true)),
                '$article_count' => count_articles(),
                '$tag_count' => count_tags(),
                '$tags_admin_list' => tags_admin_list(get_all_tag_names()),
                '$SOFTWARE_VERSION' => SOFTWARE_VERSION,
                '$SOFTWARE_LICENSE' => SOFTWARE_LICENSE,
                '$DB_SERVER' => DB_SERVER,
                '$BLOG_TITLE' => BLOG_TITLE,
                '$PHP_VERSION' => PHP_VERSION,
                '$DB_SIZE_B' => format_bytes(get_db_size_b()),
            );
            return strtr($html_article_template, $translation_array);
        } catch (Exception $e) {
            return error_html($e->getMessage());
        }
        
    }
?>