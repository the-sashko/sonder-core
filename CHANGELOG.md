# Changelog

## v0.1.1-alpha (04/26/2019)

### New Features
-  Helpers For Using MVC Patern
-  Helpers For Using ValueObject Patern In Models
-  Helpers For Routing HTTP Requests
-  Helpers For Creating API

-  Wrapper For Working With PostgreSQL (By PDO)
-  Wrapper For Working With Redis (By Predis)

-  Simple Plain PHP Templater
-  Uploading Files In Secure Way
-  Validaton Input Values By Type
-  Handling Errors And Exceptions
-  Logging
-  Sharing Links Via Twitter (By CodeBird) And Telegram (By Curl)
-  Translation By Dictionary
-  Transliteration Cyrillic To Latin
-  Converting Numbers Fron Base 10 To Others Bases (From 2 To 64)

-  Getting Input Data From HTTP Requests And Server Environment In Secure Way
-  Getting User IP And Hash Of User IP

-  Generating HTML Breadcrumbs
-  Generating HTML From Markdown-Like Markup
-  Generating HTML From External Web Page Link
-  Generating HTML From Youtube Video Link
-  Generating HTML Pagination

-  Sending E-mails (Unstable Feature)
-  Resizing And Converting Images (Unstable Feature)
-  Creating Short Links Via Shortener API (Unstable Feature)
-  Helpers For Generation Sitemaps (Unstable Feature)
-  Caching Data Base Requests By Redis, Files Or Memcache (Unstable Feature)
-  Caching Templater Data By Redis, Files Or Memcache (Unstable Feature)

## v0.2-beta (05/21/2019)

### New Features
-  Helper For User Authentication / Authorization
-  Plugin For Push And SMS Notifications (Unstable)
-  Add Multiple Get Methods In ValuesObject Class

### Bug Fixes
-  Fix Cron Model (In Examples)
-  Fix Breadcrumbs Plugin

### Upgrade Steps (From Alpha Version)
-  Rename All Calls initConfig() To getConfig()
-  Rename All Calls initPlugin() To getPlugin()
-  Rename All Calls initModel() To getModel()

### Improvements
-  Stability
-  Code Style

## v1.0 (12/04/2020)

### Basic Features
-  MVC Patern Layout (Controller-Model&Hooks-Templates)
-  Plugins With Common Business Logic
-  Helpers For Using ValueObject Patern In Models
-  Helpers For Handling Forms Data
-  Helpers For Routing HTTP Requests
-  Helpers For Creating API
-  Helpers For Calling Methods From CLI
-  Helpers For Cron Jobs

### Plugins
-  **Breadcrumbs** – Generating HTML Breadcrumbs
-  **Captcha** – Generating And Checking Captcha
-  **Crypt** – Generating Hashes And Trip Codes
-  **Database** – PDO Wrapper (PostgreSQL)
-  **Error** – Generating HTML And JSON Errors
-  **Geoip** – Getting User IP Or Hash Of User IP
-  **Image** – Resize Images
-  **Markup** – Generating HTML From Markdown-Like Markup And BB-Codes
-  **Language** – Translating By Dictionaries
-  **Link** – Generating HTML From External Web Page Link
-  **Logger** – Logging Errors Or Other Messages
-  **Mail** – Sending E-Mails
-  **Math** – Some Useful Math Functions (Convert Decimal To 64 Based Numbers)
-  **Mock** – Empty Plugin For Example
-  **Page** – Generating HTML Static Pages
-  **Paginator** – Generating HTML Pagination
-  **Qr** – Generating QR From Text
-  **Redis** – Wrapper For Working With Redis
-  **Security** – Escape Input Data
-  **Session** – Wrapper For Getting/Setting Data From/To PHP Session
-  **Share** – Sharing URLs In Twitter And Telegram
-  **Shortener** – Generating Short Links By Shortener API
-  **Sitemap** – Generating Sitemap
-  **Sms** – Sending SMS Messages (By SMSClub Service)
-  **Templater** – Generating HTML From Data And PHTML Files
-  **Translit** – Transliteration Cyrillic To Latin
-  **Upload** – Wrapper For Uploading Single Or Multiple File(s)
-  **Youtube** – Generating HTML From Youtube Video Link

## v1.1 (03/13/2021)

### New Features
-  Add `onAfterControllerInit` hook

### Bug Fixes
-  Fix all bugs in core classes
-  Fix CLI script
-  Fix install script and install resources
-  Fix Database plugin
-  Fix Language plugin
-  Fix Logger plugin
-  Fix Markup plugin
-  Fix Page plugin
-  Fix QR plugin
-  Fix Session plugin
-  Fix Templater plugin
-  Fix Translit plugin

## v1.1.1 (03/21/2021)

### Bug Fixes
-  Fix response core class
-  Fix Markup plugin
-  Fix Page plugin
-  Fix Sitemap plugin
-  Fix Telegram plugin
