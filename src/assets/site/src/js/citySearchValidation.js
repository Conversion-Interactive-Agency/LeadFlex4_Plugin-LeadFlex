function citySearchValidation() {
  const jobSearchForm = document.querySelector("#jobSearch");
  if (!jobSearchForm) {return;}

  const cityContainer = jobSearchForm.querySelector(".city-container");
  const cityInput = jobSearchForm.querySelector("#city");
  const stateInput = jobSearchForm.querySelector("#state");
  const zipInput = jobSearchForm.querySelector("#zip");
  const jobSearchBtn = jobSearchForm.querySelector(".jobs-search-btn");

  let isTooltipDisplayed = false;
  let hasBeenDismissed = false;

  // Create the tooltip element
  const toolTipEl = document.createElement("div");
  toolTipEl.classList.add("tool-tip");
  toolTipEl.textContent = "When searching a job by location, please enter a state or a valid zip code";

  // Create the dismiss button
  const toolTipDismissBtn = document.createElement("button");
  toolTipDismissBtn.textContent = "\u2715";
  toolTipDismissBtn.setAttribute("type", "button");
  toolTipEl.appendChild(toolTipDismissBtn);

  function handleInputChange() {
    const city = cityInput.value;
    const state = stateInput.value;
    const zip = zipInput.value;

    // Check if the city is entered and if state and zip were left blank
    if (city && !state && !zip) {
      disableSearchButton();
    } else {
      enableSearchButton();
    }
  }

  function disableSearchButton() {
    // Add the disabled attribute to the job search button
    jobSearchBtn.setAttribute("disabled", "");

    if (!isTooltipDisplayed && !hasBeenDismissed) {
      toggleTooltip();
    }
  }

  function enableSearchButton() {
    // Remove the disabled attribute from the job search button
    jobSearchBtn.removeAttribute("disabled");

    if (isTooltipDisplayed) {
      toggleTooltip();
    }
  }

  function toggleTooltip() {
    isTooltipDisplayed = !isTooltipDisplayed;

    if (isTooltipDisplayed) {
      cityContainer.appendChild(toolTipEl);
    } else {
      cityContainer.removeChild(toolTipEl);
    }
  }

  stateInput.addEventListener("input", handleInputChange);
  zipInput.addEventListener("input", handleInputChange);
  cityInput.addEventListener("input", handleInputChange);
  toolTipDismissBtn.addEventListener("click", () => {
    toggleTooltip();
    hasBeenDismissed = true;
  });
}

export default citySearchValidation();
