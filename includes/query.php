<?php
/**
 * article_exists()
 *
 * Checks if an article exists by URL
 *
 * @param string $url
 * @throws Exception if there's an error in the database query
 * @return bool
 */
function article_exists($url) {
    try {
        $sql = 'SELECT id FROM articles WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount() > 0;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * count_articles()
 *
 * Count the number of articles with a given URL.
 *
 * @throws Exception if there's an error in counting articles
 * @return int
 */
function count_articles() {
    try {
        $sql = 'SELECT COUNT(*) AS article_count FROM articles';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['article_count'])) {
            return (int)$result['article_count'];
        } else {
            throw new Exception('Article count query returned unexpected result.');
        }
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * tag_id_exists()
 *
 * Returns TRUE if a tag exists (by tag_id)
 * Returns FALSE otherwise
 *
 * @param int $tag_id
 * @throws Exception if there's an error in checking tag existence
 * @return bool
 */
function tag_id_exists($tag_id) {
    try {
        $sql = 'SELECT tag_id FROM tags WHERE tag_id=:tag_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_id', $tag_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * tag_name_exists()
 *
 * Returns TRUE if a tag exists (by tag_name)
 * Returns FALSE otherwise
 *
 * @param string $tag_name
 * @throws Exception if there's an error in checking tag existence
 * @return bool
 */
function tag_name_exists($tag_name) {
    try {
        $sql = 'SELECT tag_id FROM tags WHERE tag_name=:tag_name';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();

        return $statement->rowCount() > 0;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * get_article_tags()
 *
 * Returns an array of all the tag_ids associated
 * with a certain article URL
 *
 * @param string $url
 * @throws Exception if there's an error in the database query
 * @return array
 */
function get_article_tags($url) {
    try {
        $sql = 'SELECT tag_id FROM article_tags WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * count_tag()
 *
 * Returns a count of how many articles have a tag
 *
 * @param string $tag_name
 * @throws Exception if there's an error in the database query
 * @return int
 */
function count_tag($tag_name) {
    try {
        $tag_id = get_tag_id($tag_name);
        $sql = 'SELECT COUNT(tag_id) FROM article_tags WHERE tag_id=:tag_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_id', $tag_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_COLUMN, 0);
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * is_article_tagged()
 *
 * Returns true if an article is tagged with tag_name
 * false otherwise
 *
 * @param string $url
 * @param string $tag_name
 * @return bool
 */
function is_article_tagged($url, $tag_name) {
    try {
        $sql = 'SELECT a.*
                FROM articles a, article_tags t, tags tt
                WHERE t.tag_id=tt.tag_id
                AND tt.tag_name=:tag_name
                AND a.url=t.url
                AND a.url=:url';
        $statement = db()->prepare($sql);
        $statement->bindValue('url', $url, PDO::PARAM_STR);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();
        
        return $statement->rowCount() > 0;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * get_tag_name()
 *
 * Converts a tag_id to the tag's string representation
 *
 * @param int $tag_id
 * @throws Exception
 * @return string|false
 */
function get_tag_name($tag_id) {
    try {
        $sql = 'SELECT * FROM tags WHERE tag_id=:tag_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_id', $tag_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();

        if ($result) {
            return $result['tag_name'];
        } else {
            return false;
        }
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * get_tag_id()
 *
 * Converts a tag_name to the tag's tag_id
 *
 * @param string $tag_name
 * @throws Exception
 * @return int|false
 */
function get_tag_id($tag_name) {
    try {
        $sql = 'SELECT tag_id FROM tags WHERE tag_name=:tag_name';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_COLUMN, 0);

        if ($result !== false) {
            return (int)$result;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * get_all_tag_names()
 *
 * Returns an array of all tags
 * 
 * @throws Exception
 * @return array
 */
function get_all_tag_names() {
    try {
        $sql = 'SELECT tags.tag_name, QTY.quantity FROM tags LEFT JOIN (SELECT COUNT(article_tags.tag_id) AS quantity, article_tags.tag_id FROM article_tags GROUP BY article_tags.tag_id) AS QTY ON tags.tag_id = QTY.tag_id ORDER BY QTY.quantity DESC';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}

/**
 * get_article()
 *
 * Returns an array for an article if the article exists by URL
 *
 * @param string $url
 * @throws Exception
 * @return array|false
 */
function get_article($url) {
    try {
        $sql = 'SELECT * FROM articles WHERE url=:article_url';
        $statement = db()->prepare($sql);
        $statement->bindValue('article_url', $url, PDO::PARAM_STR);
        $statement->execute();
        $article = $statement->fetch();

        // Perform data output sanitization if needed
        if ($article) {
            $article['content'] = strip_tags($article['content'], ALLOWED_ARTICLEHTML_TAGS);
            $article['title'] = strip_tags($article['title'], ALLOWED_ARTICLEHTML_TAGS);
        }

        return $article;

    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}

/**
 * get_articles()
 *
 * Returns an array of article info
 *
 * @param int|null $amount
 * @param int|null $tag_id
 * @param bool $show_hidden
 * @throws Exception
 * @return array
 */
function get_articles($amount, $tag_id, $show_hidden = false) {
    try {
        $sql = '';
        $params = [];

        if ($tag_id) {
            $sql = 'SELECT a.* FROM articles a, article_tags t, tags tt
                    WHERE t.tag_id = tt.tag_id
                    AND tt.tag_id=:tag_id
                    AND a.url = t.url
                    ORDER BY a.timestamp DESC';
            $params['tag_id'] = $tag_id;
        } else {
            // Query for all articles
            $sql = 'SELECT title,url,timestamp,content,hidden FROM articles
                    ORDER BY timestamp DESC';
        }

        $statement = db()->prepare($sql);
        $statement->execute($params);
        $articles = $statement->fetchAll();

        // Cut down on the amount if applicable
        if ($amount) {
            $articles = array_slice($articles, 0, $amount);
        }

        // By default, do not return hidden articles
        if (!$show_hidden) {
            $articles = array_filter($articles, function($article) {
                return $article['hidden'] != 1;
            });
        }

        // Perform data output sanitization if needed
        foreach ($articles as &$article) {
            $article['content'] = strip_tags($article['content'], ALLOWED_ARTICLEHTML_TAGS);
            $article['title'] = strip_tags($article['title'], ALLOWED_ARTICLEHTML_TAGS);
        }

        return $articles;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}

/**
 * update_article()
 *
 * Update an article with specific URL
 *
 * @param string $url
 * @param string $title
 * @param string $date
 * @param string $content
 * @param array $tags
 * @param int $hidden
 * @throws Exception
 * @return null
 */
function update_article($url, $title, $date, $content, $tags, $hidden) {
    try {
        // First, create/update the article entry
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

        return;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}

/**
 * create_article()
 *
 * Creates a new article
 *
 * @param string $url
 * @param string $title
 * @param string $timestamp
 * @param string $content
 * @param array $tags
 * @param int $hidden
 * @throws Exception if there's an error in creating the article
 * @return null
 */
function create_article($url, $title, $timestamp, $content, $tags, $hidden) {
    try {
        // Check if the URL is already taken
        $article_exists = article_exists($url);
        if ($article_exists == TRUE) {
            throw new Exception("ERROR: An article with the specified URL already exists!");
        }

        // Check if all requested tags exist
        foreach ($tags as $tag_name) {
            if (!tag_name_exists($tag_name)) {
                throw new Exception("ERROR: One or more of the specified tags does not exist!");
            }
        }

        // Validate against allowed HTML tags
        if ($content != strip_tags($content, ALLOWED_ARTICLEHTML_TAGS)) {
            throw new Exception("ERROR: Supplied article contents did not pass validation against specified ALLOWED_ARTICLEHTML_TAGS parameter!");
        }

        if ($title != strip_tags($title, "")) {
            throw new Exception("ERROR: Supplied article title cannot include HTML tags!");
        }

        if ($url != strip_tags($url, "")) {
            throw new Exception("ERROR: Supplied article URL cannot include HTML tags!");
        }

        // Insert the new article
        $sql = 'INSERT INTO articles (title, url, timestamp, content, hidden) VALUES (:title, :url, :timestamp, :content, :hidden)';
        $statement = db()->prepare($sql);
        $statement->bindValue('title', $title, PDO::PARAM_STR);
        $statement->bindValue('url', $url, PDO::PARAM_STR);
        $statement->bindValue('timestamp', $timestamp, PDO::PARAM_STR);
        $statement->bindValue('content', $content, PDO::PARAM_STR);
        $statement->bindValue('hidden', $hidden, PDO::PARAM_INT);
        $statement->execute();

        // Delete tag relationships for the URL if they exist
        $sql = 'DELETE FROM article_tags WHERE url=:url';
        $statement = db()->prepare($sql);
        $statement->bindValue('url', $url, PDO::PARAM_STR);
        $statement->execute();

        // Create tag relationships
        foreach ($tags as $tag_name) {
            $sql = 'INSERT IGNORE INTO article_tags (url, tag_id) VALUES (:url, :tag_id);';
            $statement = db()->prepare($sql);
            $statement->bindValue('url', $url, PDO::PARAM_STR);
            $statement->bindValue('tag_id', get_tag_id($tag_name), PDO::PARAM_INT);
            $statement->execute();
        }

        return;
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * delete_article()
 *
 * Delete an article with a specific URL
 *
 * @param string $url
 * @throws Exception if there's an error in deleting the article
 * @return int
 */
function delete_article($url) {
    try {
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
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}


/**
 * create_tag()
 *
 * Creates a new tag
 *
 * @param string $tag_name
 * @throws Exception if there's an error in creating the tag
 * @return void
 */
function create_tag($tag_name) {
    try {
        // Does the tag already exist?
        $tag_exists = tag_name_exists($tag_name);
        if ($tag_exists) {
            throw new Exception("ERROR: A tag with the specified name already exists!");
        }

        $sql = 'INSERT INTO tags (tag_name) VALUES (:tag_name)';
        $statement = db()->prepare($sql);
        $statement->bindValue('tag_name', $tag_name, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}

/**
 * delete_tag()
 *
 * Delete a tag identified by tag_name
 *
 * @param string $tag_name
 * @throws Exception if there's an error in deleting the tag
 * @return int
 */
function delete_tag($tag_name) {
    try {
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
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}

/**
 * get_db_size_b()
 *
 * Return the database size in bytes
 *
 * @throws Exception if there's an error in retrieving the database size
 * @return int
 */
function get_db_size_b() {
    try {
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
    } catch (PDOException $e) {
        throw new Exception("ERROR [PDOException]: " . $e->getMessage());
    }
}
?>
