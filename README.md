# LearnLoop

This is the repository for LearnLoop - a web application for improving communication between educators and learners in a postgraduate medical education setting.
Client-side is VueJS v3 with composition and Bootstrap 5. Server-side is vanilla PHP with SQL.

## Project Setup

```sh
npm install
```

### During development

- src/data/config.js > config > client > url: `https://dev.learnloop.co.uk` if deploying development builds for live testing
- deploy latest version of API codebase to `https://dev.learnloop.co.uk/api/`
- change target database tables to avoid affecting live data:
  - api/interaction/database.php > $tblSessions / $tblSubmissions / $tblFeedbackSessions to development tables
  - api/feedback/database.php > $tblSessions / $tblSubmissions / $tblAttendance to development tables
- api/index.php > devMode: `true`
- api/shared/QRcode/index.php: `QRcode::png('https://dev.learnloop.co.uk/'.htmlspecialchars($_REQUEST['id']));`
- src/data/config.js > config > api > url: `https://dev.learnloop.co.uk/api/` to ensure requests go to the development API
- src/data/config.js > config > api > imagesUrl: `https://dev.learnloop.co.uk/api/interaction/uploads/img/` to ensure images sourced from the development image store
- src/data/config.js > config > api > showApiConsole: `true` to show API calls in the developer console
- .htaccess in dev subdomain to enable CORS as requests will come from development environment localhost `Header set Access-Control-Allow-Origin \*`

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

If building for final deployment (rather than live testing) reverse changes in 'During development' list above.

```sh
npm run build
```
