# LearnLoop

This is the repository for LearnLoop client - a web application for improving communication between educators and learners in a postgraduate medical education setting.
Client-side is VueJS v3 with composition and Bootstrap 5.
See API here: https://github.com/dan-leach/dka-calculator-api

## Project Setup

```sh
npm install
```

### During development

- src/data/config.js > config > client > url: `https://dev.learnloop.co.uk` if deploying development builds for live testing
- deploy latest version of API codebase to `https://api.learnloop.co.uk/dev/`
- change target database tables in config.json to avoid affecting live data:
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
