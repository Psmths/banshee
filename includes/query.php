<?php
    // article_exists()
    // 
    // Returns TRUE if an article exists
    // Returns FALSE otherwise
    // 
    // Parameters
    // url
    function article_exists($url) {
        $sql = 'SELECT id FROM articles WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount() == 0 ? false : true;
    }

    // tag_id_exists()
    // 
    // Returns TRUE if a tag exists (by tag_id)
    // Returns FALSE otherwise
    // 
    // Parameters
    // tag_id
    function tag_id_exists($tag_id) {
        $sql = 'SELECT tag_id FROM tags WHERE tag_id=:tag_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_id', $tag_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() == 0 ? false : true;
    }

    // tag_name_exists()
    // 
    // Returns TRUE if a tag exists (by tag_name)
    // Returns FALSE otherwise
    // 
    // Parameters
    //  tag_id
    function tag_name_exists($tag_name) {
        $sql = 'SELECT tag_id FROM tags WHERE tag_name=:tag_name';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount() == 0 ? false : true;
    }

    // get_article_tags()
    // 
    // Returns an array of all the tag_ids associated
    // with a certain article url
    //
    // Parameters
    // url
    function get_article_tags($url) {
        $sql = 'SELECT tag_id FROM article_tags WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

        return $result;
    }

    // count_tag()
    // 
    // Returns a count of how many articles have a tag
    //
    // Parameters
    // tag_name
    function count_tag($tag_name) {
        $tag_id = get_tag_id($tag_name);
        $sql = 'SELECT COUNT(tag_id) FROM article_tags WHERE tag_id=:tag_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_id', $tag_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_COLUMN, 0);

        return $result;
    }

    // is_article_tagged()
    // 
    // Returns true if an article is tagged with tag_name
    // false otherwise
    // 
    // Parameters
    // url
    // tag_name
    function is_article_tagged($url, $tag_name) {
        $sql = 'SELECT a.* FROM articles a, article_tags t, tags tt WHERE t.tag_id=tt.tag_id AND tt.tag_name=:tag_name AND a.url=t.url AND a.url=:url';
        $statement = db()->prepare($sql);
        $statement->bindValue('url', $url, PDO::PARAM_STR);
        $statement->bindValue('tag_name', $tag_name["tag_name"], PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount() == 0 ? false : true;
    }

    // get_tag_name()
    // 
    // Converts a tag_id to the tag's string representation
    // 
    // Parameters
    // tag_id
    function get_tag_name($tag_id) {
        $sql = 'SELECT * FROM tags WHERE tag_id=:tag_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_id', $tag_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        return $result;
    }

    // get_tag_id()
    // 
    // Converts a tag_name to the tag's tag_id
    // 
    // Parameters
    // tag_name
    function get_tag_id($tag_name) {
        $sql = 'SELECT tag_id FROM tags WHERE tag_name=:tag_name';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_COLUMN, 0);
        return $result;
    }

    // get_all_tag_names()
    // 
    // Returns an array of all tags
    function get_all_tag_names() {
        $sql = 'SELECT tags.tag_name, QTY.quantity FROM tags LEFT JOIN (SELECT COUNT(article_tags.tag_id) AS quantity, article_tags.tag_id FROM article_tags GROUP BY article_tags.tag_id) AS QTY ON tags.tag_id = QTY.tag_id ORDER BY QTY.quantity DESC';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    // get_article()
    // 
    // Returns array for an article if the article exists by URL
    // 
    // Parameters
    // url      CLIENT GET for article url column
    function get_article($url) {
        $sql = 'SELECT * FROM articles WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();
        $article = $statement->fetch();
        return $article;
    }

    // get_articles()
    // 
    // Returns array of article info
    // 
    // Parameters
    // amount       How many articles to return, NULL for unlimited
    // tag          Only return articles with this tag, NULL For any tag
    function get_articles($amount, $tag_id, $show_hidden = false) {

        if ($tag_id) {
            $sql = 'SELECT a.* FROM articles a, article_tags t, tags tt WHERE t.tag_id = tt.tag_id AND tt.tag_id=:tag_id AND a.url = t.url';
            $statement = db()->prepare($sql);
            $statement->bindValue('tag_id', $tag_id, PDO::PARAM_INT);
            $statement->execute();
        } else {
            // Query for all articles
            $sql = 'SELECT title,url,timestamp,content,hidden FROM articles ORDER BY timestamp DESC';
            $statement = db()->prepare($sql);
            $statement->execute();
        }
        
        
        $articles = $statement->fetchAll();

        // Cut down on the amount if applicable
        if ($amount) { 
            $articles = array_slice($articles, 0, $amount);
        }

        // By default, do not return hidden articles
        if (!$show_hidden) { 
            foreach($articles as $key => $article ) {
                if($article['hidden'] == 1) {
                    unset($articles[$key]);  
                }
            }
        }

        return $articles;
    }

    // update_article()
    // 
    // Update an article with specific url
    // 
    // Parameters
    // url
    // title
    // date
    // content
    // tags
    function update_article($url, $title, $date, $content, $tags, $hidden) {
        // First, create the article entry
        $sql = 'UPDATE articles SET title=:title, timestamp=:date, content=:content, hidden=:hidden WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->bindValue('title', $title, PDO::PARAM_STR);
        $statement->bindValue('date', $date, PDO::PARAM_STR);
        $statement->bindValue('content', $content, PDO::PARAM_STR);
        $statement->bindValue('hidden', $hidden, PDO::PARAM_INT);
        $statement->execute();

        // Refresh tags
        $sql = 'DELETE FROM article_tags WHERE url=:url';
        $statement = db()->prepare($sql);
        $statement->bindValue('url', $url, PDO::PARAM_STR);
        $statement->execute();

        foreach ($tags as $tag_name) {
            $sql = 'INSERT IGNORE INTO article_tags (url, tag_id) VALUES (:url, :tag_id);';
            $statement = db()->prepare($sql);
            $statement->bindValue('url', $url, PDO::PARAM_STR);
            $statement->bindValue('tag_id', get_tag_id($tag_name), PDO::PARAM_INT);
            $statement->execute();
        }

        // TODO : ERROR CHECKING
        return true;
    }

    // create_article()
    // 
    // Creates an article
    // 
    // Parameters
    // title
    // url
    // timestamp
    // content
    // tags
    //
    // Returns NULL if success, returns an error if failed
    function create_article($url, $title, $timestamp, $content, $tags, $hidden) {

        // Check if the URL is already taken
        if (article_exists($url)) {
            return("ERROR: An article with the specified URL already exists!");
        }

        // Check if all requested tags exist
        foreach ($tags as $tag_name) {
            if (!tag_name_exists($tag_name)) {
                return("ERROR: One or more of the specified tags does not exist!");
            }
        }

        // Insert the new article
        try {
            $sql = 'INSERT INTO articles (title, url, timestamp, content, hidden) VALUES (:title, :url, :timestamp, :content, :hidden)';
            $statement = db()->prepare($sql);
            $statement->bindValue('title', $title, PDO::PARAM_STR);
            $statement->bindValue('url', $url, PDO::PARAM_STR);
            $statement->bindValue('timestamp', $timestamp, PDO::PARAM_STR);
            $statement->bindValue('content', $content, PDO::PARAM_STR);
            $statement->bindValue('hidden', $hidden, PDO::PARAM_INT);
        $statement->execute();
        } catch (PDOException $e) {
            return "ERROR [PDOException]: " . $e->getMessage();
        }
        
        // Delete tag relationships for the URL if they exist
        try {
            $sql = 'DELETE FROM article_tags WHERE url=:url';
            $statement = db()->prepare($sql);
            $statement->bindValue('url', $url, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            return "ERROR [PDOException]: " . $e->getMessage();
        }

        // Create tag relationships
        try {
            foreach ($tags as $tag_name) {
                $sql = 'INSERT IGNORE INTO article_tags (url, tag_id) VALUES (:url, :tag_id);';
                $statement = db()->prepare($sql);
                $statement->bindValue('url', $url, PDO::PARAM_STR);
                $statement->bindValue('tag_id', get_tag_id($tag_name), PDO::PARAM_INT);
                $statement->execute();
            }
        } catch (PDOException $e) {
            return "ERROR [PDOException]: " . $e->getMessage();
        }

        return NULL;
    }

    // delete_article()
    // 
    // Delete an article with specific url
    // 
    // Parameters
    // url
    function delete_article($url) {
        // Delete article entry
        $sql = 'DELETE FROM articles WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();

        // Delete any tag references in article_tags
        $sql = 'DELETE FROM article_tags WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount();
    }

    // create_tag()
    // 
    // Creates a new tag
    // 
    // Parameters
    // tag_name
    //
    // Returns NULL if success, returns an error if failed
    function create_tag($tag_name) {
        // Does the tag already exist?
        if (tag_name_exists($tag_name)) {
            return("ERROR: A tag with the specified name already exists!");
        }

        $sql = 'INSERT INTO tags (tag_name) VALUES (:tag_name)';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();

        return NULL;
    }

    // delete_tag()
    // 
    // Delete a tag identified by tag_name
    // 
    // Parameters
    // tag_name
    function delete_tag($tag_name) {
        // First delete any references to the tag
        $sql = 'DELETE FROM article_tags WHERE tag_id=:tag_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_id', get_tag_id($tag_name), PDO::PARAM_INT);
        $statement->execute();

        // Then delete the tag entry
        $sql = 'DELETE FROM tags WHERE tag_name=:tag_name';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount();
    }

    // get_db_size_b()
    // 
    // Return the database size in bytes
    function get_db_size_b() {
        $sql = 'SELECT 
        TABLE_SCHEMA AS DB_NAME, 
        ROUND(sum(data_length + index_length)) AS "size"
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA=:DB_NAME
        GROUP BY TABLE_SCHEMA ;';
        $statement = db()->prepare($sql);
        $statement->bindValue('DB_NAME', DB_NAME, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch();
        return $result["size"];
    }
?>