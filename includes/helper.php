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
        return date('M j, Y', strtotime($sql_timestamp));
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
        http_response_code(404);
        return file_get_contents('../resource/404.html');
    }

    // tags_csv_string()
    // 
    // Returns a CSV string of article tags by article URL column
    function tags_csv_string($url) {
        // Get tags and convert to CSV string
        $article_tag_array = get_article_tags($url);
        $article_tag_str_array = Array();
        foreach ($article_tag_array as $tag_id) {
            array_push($article_tag_str_array, get_tag_name($tag_id)["tag_name"]);
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
        $article = get_article($url);

        // Construct and return the HTML for the article body
        $html_article_template = '
        <h1 class="nomarginbottom">
            $title
        </h1>
        <i class="tags">$date</i>
        $content
        <i class="tags">Tags: $tags</i><br><br>
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
                $date - <a class="headerlink" href="/article/?id=$url">$title</a>
            </h3>
            <i class="tags">
                Tags: $tags_csv
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
        $html = "";
        $html_article_template = '
        <div class="aeitem">
            <a href="/article?id=$url">👁️ View</a>
            <a href="/admin/edit.php?id=$url">✍️ Edit</a>
            <a href="/admin/delete.php?id=$url" class="danger">🗑️ Delete</a>
            <p class="$class">$title</p>
        </div>
        ';
        foreach ($articles_array as $article) {
            $translation_array = array(
                '$title' => $article["title"],
                '$url' => $article["url"],
                '$class' => ($article["hidden"] != 1) ? 'thinp' : 'thinp hidden-item'
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
            <a href="/admin/deletetag.php?tag_name=$tag_name" class="danger">🗑️ Delete</a>
            <p class="thinp">$tag_name</p>
        </div>
        ';
        foreach ($tags_array as $tag_name) {
            $translation_array = array(
                '$tag_name' => $tag_name["tag_name"]
            );
            $html .= strtr($html_taglist_template, $translation_array);
        }
        return $html;
    }

    // article_edit_html()
    function article_edit_html($url) {
        $article = get_article($url);

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

        $tag_names = get_all_tag_names();
        $tag_options = "";
        foreach ($tag_names as $tag_name) {
            $tag_options_template = '<option $selected>$tag_name</option>';
            $translation_array = array(
                '$tag_name' => $tag_name["tag_name"],
                '$selected' => is_article_tagged($url, $tag_name) ? "selected" : ""
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
        $article = get_article($url);

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

    // admin_html()
    // 
    // Returns HTML for the admin control panel
    function admin_html() {
        // Construct and return the HTML for the admin panel
        $html_article_template = '
        <h1>Admin Control Panel</h1>
        <h2>Quick Actions</h2>
        <ul>
            <li><a href ="/admin/newarticle.php">Create a new article</a></li>
            <li><a href ="/admin/newtag.php">Create a new tag</a></li>
        </ul>
        <h2>Articles</h2>
        $article_admin_list
        <small>ℹ️&emsp;<i>Articles highlighted in blue are hidden from public index. They may still be accessed via their URL.</i></small>
        <h2>Tags</h2>
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
                <td>PHP Version</td>
                <td>$PHP_VERSION</td>
            </tr>
        </table>
        ';

        $translation_array = array(
            '$article_admin_list' => article_admin_list(get_articles(NULL,NULL,true)),
            '$tags_admin_list' => tags_admin_list(get_all_tag_names()),
            '$SOFTWARE_VERSION' => SOFTWARE_VERSION,
            '$SOFTWARE_LICENSE' => SOFTWARE_LICENSE,
            '$DB_SERVER' => DB_SERVER,
            '$BLOG_TITLE' => BLOG_TITLE,
            '$PHP_VERSION' => PHP_VERSION,
            '$DB_SIZE_B' => format_bytes(get_db_size_b()),
        );
        return strtr($html_article_template, $translation_array);
    }
?>