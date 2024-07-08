/* eslint no-console:0 */
import Cookies from "js-cookie";

function socialShareForm() {
  let socialShareForm = null;
  let roleOption = null;

  document.addEventListener("afterBlitzInjectAll", function () {
    socialShareForm = document.getElementById("socialShare");
    roleOption = document.getElementById("referrerSelectInput");

    // If the form has not been injected into the DOM, exit early.
    if (!socialShareForm) {
      return;
    }

    init();
  });

  function setCookie(name, value) {
    const oneYearFromNow = new Date(new Date().getTime() + 365 * 24 * 60 * 60 * 1000);
    Cookies.set(name, value, { expires: oneYearFromNow });
  }

  function init() {
    // Copies job search input values, if they exist, and appends them to the current URL with the respective R value based on the social role selected
    socialShareForm.addEventListener("submit", function (event) {
      event.preventDefault();

      const form = document.querySelector("#jobSearch input");
      const inputs = form ? Array.from(form) : []; // spread into an array only if form is not null

      // Strip current URL of all query parameters
      const currentURLNoParams = `${window.location.origin}${window.location.pathname}`;
      const shareURL = new URL(currentURLNoParams);

      inputs
        .filter(input => input.value !== "")
        .forEach(input => shareURL.searchParams.append(input.name, input.value));

      setCookie("social-role", roleOption.value);
      shareURL.searchParams.append("r", roleOption.value);

      navigator.clipboard.writeText(shareURL.href)
        .then(() => {
          // Create a new element to display the "copied to clipboard" message
          const messageElement = document.createElement("div");
          messageElement.classList.add("clipboard");
          messageElement.textContent = "Copied to clipboard";
          // Add the new element to the page
          document.body.appendChild(messageElement);
          // Remove the "copied to clipboard" message after a short delay
          setTimeout(() => {
            document.body.removeChild(messageElement);
          }, 2000);
        })
        .catch(err => {
          console.error("Failed to copy URL: ", err);
        });
    });
  }
}

export default socialShareForm();
