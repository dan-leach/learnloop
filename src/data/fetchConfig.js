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
    console.error(error);
    throw error;
  }
}

export { config, fetchConfig };
