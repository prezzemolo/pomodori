pomodori
==========
[![][mit-badge]][mit] [![][issue-badge]][issue]  
an api server for my php learning.

Setup
----------
1. Clone to Web Directory
2. configuration .htaccess
```
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?__PATH_INFO__=$1 [L,QSA]
```

LICENSE
----------
The MIT License. See [LICENSE](LICENSE).

[mit]: http://opensource.org/licenses/MIT
[mit-badge]:https://img.shields.io/badge/license-MIT-444444.svg?style=flat-square
[issue]: https://github.com/prezzemolo/pomodori/issues
[issue-badge]: https://img.shields.io/github/issues/prezzemolo/pomodori.svg?style=flat-square