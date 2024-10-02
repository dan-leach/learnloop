import { config } from "./fetchConfig.js";

/**
 * Escapes ampersand characters in the input string.
 * @param {string} str - The input string to escape.
 * @returns {string} - The escaped string.
 */
function escapeAmpersand(str) {
  return str.replace(/&/g, "%26");
}

/**
 * Escapes long dash (–) characters in the input string.
 * @param {string} str - The input string to escape.
 * @returns {string} - The escaped string.
 */
function escapeLongdash(str) {
  return str.replace(/–/g, "-");
}

/**
 * Escapes right quote (’) characters in the input string.
 * @param {string} str - The input string to escape.
 * @returns {string} - The escaped string.
 */
function escapeRightQuote(str) {
  return str.replace(/’/g, "'");
}

/**
 * Escapes left quote (‘) characters in the input string.
 * @param {string} str - The input string to escape.
 * @returns {string} - The escaped string.
 */
function escapeLeftQuote(str) {
  return str.replace(/‘/g, "'");
}

/**
 * Escapes problematic characters in the input string.
 * @param {string} str - The input string to escape.
 * @returns {string} - The escaped string.
 */
function escapeProblemChars(str) {
  return escapeLeftQuote(
    escapeRightQuote(escapeLongdash(escapeAmpersand(str)))
  );
}

/**
 * Makes an API call using the fetch API with a POST request.
 * @param {string} module - The module name for the API.
 * @param {string} route - The API route.
 * @param {string} id - The ID for the request.
 * @param {string} pin - The pin for the request.
 * @param {Object} data - The data to send in the request.
 * @returns {Promise<Object>} - A promise that resolves with the API response or rejects with an error.
 */
async function api(module, route, id, pin, data) {
  const timeout = new Promise((_, reject) =>
    setTimeout(
      () => reject(new Error("Request timed out. Check your connection.")),
      config.api.timeoutDuration
    )
  );

  const apiCall = fetch(config.api.url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      module,
      route,
      id,
      pin,
      data: escapeProblemChars(JSON.stringify(data)),
    }),
  })
    .then(async (response) => {
      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(
          `API error: status ${response.status} (${response.statusText}) ${errorText}`
        );
      }
      const responseData = await response.json();
      if (config.api.showApiConsole) {
        console.log("-->", responseData);
      }
      return responseData;
    })
    .catch((error) => {
      console.error("API error", error);
      throw error;
    });

  if (config.api.showApiConsole) {
    console.log(route, data ? data : "");
  }

  return Promise.race([apiCall, timeout]);
}

export { api };
