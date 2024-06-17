import { config } from "./config.js";

function escapeAmpersand(str) {
  return str.replace(/&/g, "%26");
}

function escapeLongdash(str) {
  return str.replace(/–/g, "-");
}

function escapeRightQuote(str) {
  return str.replace(/’/g, "'");
}

function escapeLeftQuote(str) {
  return str.replace(/‘/g, "'");
}

function escapeProblemChars(str) {
  let output = escapeAmpersand(str);
  output = escapeLongdash(output);
  output = escapeRightQuote(output);
  output = escapeLeftQuote(output);
  return output;
}

function api(module, route, id, pin, data) {
  console.log(escapeProblemChars(JSON.stringify(data)));
  return new Promise(function (resolve, reject) {
    setTimeout(function () {
      reject(
        new Error(
          "Your request timed out. Please check your internet connection."
        )
      );
    }, config.api.timeoutDuration);
    let req = new XMLHttpRequest();
    req.open("POST", config.api.url);
    req.onload = function () {
      if (req.status == 200) {
        try {
          if (config.api.showApiConsole)
            console.log("-->", JSON.parse(req.response));
          resolve(JSON.parse(req.response));
        } catch (e) {
          console.log("-->", req.response);
          console.error("Error outputting API response as parsed object", e);
          reject(req.response);
        }
      } else {
        try {
          console.error("API error", JSON.parse(req.response));
          reject(JSON.parse(req.response));
        } catch (e) {
          console.error("API error", e, req);
          reject(
            "API error: status " +
              req.status +
              " (" +
              req.statusText +
              ") " +
              req.response
          );
        }
      }
    };
    req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    req.send(
      "module=" +
        module +
        "&route=" +
        route +
        "&id=" +
        id +
        "&pin=" +
        pin +
        "&data=" +
        escapeProblemChars(JSON.stringify(data))
    );
    if (config.api.showApiConsole) console.log(route, data ? data : "");
  });
}

export { api };
