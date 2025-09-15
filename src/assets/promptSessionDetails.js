/**
 * @module promptSessionDetails
 * @summary Utility for prompting session ID, PIN, email input using SweetAlert
 * @description This composable shows a SweetAlert modal prompting the user to enter a session ID and optionally a PIN.
 * @requires sweetalert2
 * @exports promptSessionDetails
 */

import Swal from "sweetalert2";

/**
 * Prompts the user for a session ID and optionally a PIN using SweetAlert2.
 *
 * @param {string} [defaultId=""] - Default value to prefill the session ID input
 * @param {string} [title="Enter session details"] - Title of the SweetAlert modal
 * @param {string} [subtitle="You can find these details in your session confirmation email."] - Subtitle of the SweetAlert modal
 * @param {boolean} [requireId=true] - Whether to ask for the session ID
 * @param {boolean} [requirePin=true] - Whether to ask for session PIN
 * @param {boolean} [requireEmail=true] - Whether to ask for user email
 * @param {Object} [boolean] - Optional boolean prompt configuration
 * @param {string} [boolean.promptText] - Text for the boolean prompt
 * @param {string} [boolean.trueText] - Text for the "true" option
 * @param {string} [boolean.falseText] - Text for the "false" option
 * @returns {Promise<{ id: string, pin?: string }|false>} An object containing the entered values, or `false` if the user cancelled
 * @memberof module:promptSessionDetails
 */
export async function promptSessionDetails(
  defaultId = "",
  title = "Enter session ID and PIN",
  subtitle = "You can find these details in your session confirmation email.",
  requireId = true,
  requirePin = true,
  requireEmail = false,
  boolean = {
    promptText: "",
    trueText: "",
    falseText: "",
  }
) {
  const requireBoolean = Boolean(boolean.promptText);
  const result = await Swal.fire({
    title: title,
    html: `
      <div class="overflow-hidden">
        <div>${subtitle}</div>
        ${
          requireId
            ? `<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="${defaultId}">`
            : ""
        }
        ${
          requirePin
            ? '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">'
            : ""
        }
        ${
          requireEmail
            ? '<input id="swalFormEmail" placeholder="Email" type="text" autocomplete="off" class="swal2-input">'
            : ""
        }
        ${
          requireBoolean
            ? `<div class="d-flex justify-content-center mt-3">
                <div class="form-floating w-100">
                  <select id="swalFormBoolean" class="form-select form-select-lg">
                    <option value=true>${boolean.trueText}</option>
                    <option value=false>${boolean.falseText}</option>
                  </select>
                  <label for="swalFormBoolean">${boolean.promptText}</label>
                </div>
              </div>`
            : ""
        }
      </div>
    `,
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      const id = requireId
        ? document.getElementById("swalFormId").value.trim()
        : undefined;
      if (requireId && !id) {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }

      const pin = requirePin
        ? document.getElementById("swalFormPin").value.trim()
        : undefined;
      if (requirePin && !pin) {
        Swal.showValidationMessage("Please enter your PIN");
        return false;
      }

      const email = requireEmail
        ? document.getElementById("swalFormEmail").value.trim()
        : undefined;
      if (requireEmail && !email) {
        Swal.showValidationMessage("Please enter your email");
        return false;
      }

      const boolean = requireBoolean
        ? document.getElementById("swalFormBoolean").value == "true"
        : undefined;

      return { id, pin, email, boolean };
    },
  });

  return {
    isConfirmed: result.isConfirmed,
    ...(result.value || {}),
  };
}
