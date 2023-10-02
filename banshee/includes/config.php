<?php
    // Basic configuration
    define('BLOG_TITLE', 'Banshee');
    define('BLOG_DESCRIPTION', 'Lightweight blogging software!');
    define('BLOG_DOMAIN', 'example.com');
    define('BLOG_THEME', 'rhino');
    define('ENABLE_RSS', true);

    // Security configuration
    define('ALLOWED_ARTICLEHTML_TAGS', '<center><div><b><i><strong><small><p><h1><h2><h3><img><a><ul><ol><li><table><tr><th><td><pre><code><br>');

    // Database configuration
    define('DB_SERVER', getenv('DB_SERVER'));
    define('DB_USERNAME', getenv('DB_USERNAME'));
    define('DB_PASSWORD', getenv('DB_PASSWORD'));
    define('DB_NAME', 'blog');

    // Sidebar contents
    define('SIDEBAR_CONTENTS', '
        <a href="/">home</a><br>
        <a href="/article">articles</a><br>
        <a href="/tags">tags</a><br><br>
    ');

    // Intro panel contents
    define('SITE_INTRO', '
        <h1>Welcome to Banshee!</h1>

        <p>Banshee is lightweight blog software. If you are seeing this page, it means that Banshee has been installed and can successfully connect to your database!</p>

        <p>To get started in making it your own, edit the <code>config.php</code> file located in the <code>/includes</code> directory. From there, you can modify the look of your blog by selecting different themes, change this text, and add custom sidebar elements.</p>
    ');
?>