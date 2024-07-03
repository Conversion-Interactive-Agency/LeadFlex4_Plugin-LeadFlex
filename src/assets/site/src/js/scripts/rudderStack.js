/* eslint no-console:0 */
import { RudderAnalytics } from "@rudderstack/analytics-js";
import checkIfHtmxIsLoaded from "./helpers/htmx";

function updateAnonymousIdField() {
  const anonymousIdField = document.querySelector("[name=\"fields[anonymous_id]\"]");
  if (anonymousIdField !== null) {
    anonymousIdField.value = rudderanalytics.getAnonymousId();
    console.log("RudderStack Field Update complete - ", rudderanalytics.getAnonymousId());
  } else {
    console.log("anonymousIdField is null");
  }
}

function registerEvents() {
  const anchorLinks = document.getElementsByTagName("a");

  // Internal Sites
  [...anchorLinks].forEach((anchor) => {
    anchor.addEventListener(
      "click",
      (event) => {
        const element = event.currentTarget;
        const params = {
          eventElement: "a",
          target: element.href,
          title: element.innerText,
          id_html: element.id,
          className: element.className.split(" "),
          ariaLabel: element.getAttribute("aria-label"),
        };
        rudderanalytics.track("Clicked A Link", params);
      },
      { once: true }
    );

    // If anchor doesn't have a href attribute - exit
    if (!anchor.href || !anchor.hasAttribute("href")) {
      return false;
    }

    const currentUrl = new URL(anchor.href);
    const updateExternalUrl =
            currentUrl.origin !== location.origin && !currentUrl.searchParams.get("anonymous_id");
    if (updateExternalUrl) {
      currentUrl.searchParams.append("anonymous_id", rudderanalytics.getAnonymousId());
      anchor.href = currentUrl;
    }
  });

  const buttonLinks = document.getElementsByTagName("button");
  [...buttonLinks].forEach((button) => {
    button.addEventListener(
      "click",
      (event) => {
        const element = event.currentTarget;
        const params = {
          eventElement: "button",
          title: element.innerText,
          id_html: element.id,
          className: element.className.split(" "),
          ariaLabel: element.getAttribute("aria-label"),
        };
        rudderanalytics.track("Pressed A Button", params);
      },
      { once: true }
    );
  });
}

const rudderanalytics = new RudderAnalytics();

rudderanalytics.load("2Fivwkm2WzdbgiuTksdgWz729mz", "https://conversionwbv.dataplane.rudderstack.com", {
  storage: {
    encryption: {
      version: "legacy"
    }
  }
});
rudderanalytics.ready(() => {
  // Register Custom Events
  checkIfHtmxIsLoaded();
  rudderanalytics.getAnonymousId();

  // Decrypting AnnoymousId into form field.
  window.addEventListener("onBeforeFormieSubmit", () => {
    updateAnonymousIdField();
  });
});
rudderanalytics.page();
