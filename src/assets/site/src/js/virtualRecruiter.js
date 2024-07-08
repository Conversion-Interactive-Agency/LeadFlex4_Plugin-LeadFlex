function virtualRecruiter() {
  document.addEventListener("DOMContentLoaded", function () {
    const virtualRecruiterField = document.querySelector("[data-field-handle='virtualRecruiter']");
    const isVirtualRecruiterEnabled = virtualRecruiterField !== null;

    if (isVirtualRecruiterEnabled) {
      const form = document.querySelector(".fui-form");
      const verifiedInput = virtualRecruiterField.querySelector("input[type='checkbox']");
      const redirectParamInput = form.querySelector("[data-field-handle='vrRedirect'] input");

      registerEventListeners();

      function registerEventListeners() {
        // Whenever the "Verified" field is conditionally hidden or shown, update the redirect input
        virtualRecruiterField.addEventListener("onAfterFormieEvaluateConditions", function (event) {
          // Is the target field conditionally hidden? Then it's always not-checked. The "Verified" field
          // could actually be checked, but hidden - we want it to evaluate as if they haven't opted-in.
          if (event.target.conditionallyHidden) {
            updateRedirectParam(false);
          } else {
            // Otherwise, best to check the state of the "Verified" field. Manually trigger the change event
            verifiedInput.dispatchEvent(new Event("change", { bubbles: true }));
          }
        });

        // Whenever the "Verified" field is toggled on or off, update the redirect input
        verifiedInput.addEventListener("change", (event) => {
          updateRedirectParam(event.target.checked);
        });
      }

      function updateRedirectParam(checked) {
        // Get whether the "Verified" input is checked, or conditionally hidden - or not
        const value = checked ? "thankyou" : "sorry";
        // Update the "Redirect Param" field
        redirectParamInput.value = value;
      }
    }
  }, false);
}

export default virtualRecruiter();
