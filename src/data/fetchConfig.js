import { ref } from "vue";

let config = ref({});

async function fetchConfig() {
  const url = "https://dev.api.learnloop.co.uk/config";
  const timeoutDuration = 15000;

  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeoutDuration);

  try {
    const response = await fetch(url, {
      method: "GET",
      signal: controller.signal,
    });

    clearTimeout(timeoutId);

    if (!response.ok) {
      const errorResponse = await response.json();
      throw errorResponse;
    }

    const jsonResponse = await response.json();
    config.value = jsonResponse;
    return jsonResponse;
  } catch (error) {
    // Handle errors (including timeout and network issues)
    if (error.name === "AbortError") {
      const errorStr = "API error: The request timed out.";
      console.error(errorStr);
      throw [{ msg: errorStr }];
    } else if (error.errors) {
      //is a jsonResponse with errors array
      console.error("API errors: ", error.errors);
      throw error.errors;
    } else {
      //another unexpected error
      console.error("API error: ", error);
      throw [{ msg: "API error: " + error.toString() }];
    }
  }
}

export { config, fetchConfig };
