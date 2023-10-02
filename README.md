<p align="center">
  <h2 align="center">Banshee</h2>
</p>

Banshee is a lightweight, database-backed blogging software hand-written for my personal website. It is intended to be easy to set up and use while also maintaining flexibility and speed. 

Supports:
  - Automatic creation of an RSS feed
  - Point-and-click administration and publishing
  - Hidden articles
  - Tagging functionality

It even comes with some themes!

![Rhino](/banshee/htdocs/resource/img/repo_rhino_theme.png)
![Soba](/banshee/htdocs/resource/img/repo_soba_theme.png)

Additionally, administration is primarily done through an included administrative interface:

![Admin Panel](/banshee/htdocs/resource/img/repo_admin_panel.png)

# Installation
Banshee is run as a dockerized application, and is hosted behind a caching proxy, Varnish. To install, you must first create a .htpasswd file that will be used to protect the admin directory for the blog, as follows:
```
git clone https://github.com/Psmths/banshee
cd banshee
htpasswd -c .htpasswd admin_user
```

Then, modify the supplied .env file as required. To run Banshee:
```
docker-compose up --build -d
```