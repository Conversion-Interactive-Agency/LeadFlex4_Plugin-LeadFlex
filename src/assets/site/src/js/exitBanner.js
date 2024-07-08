import mobile from "is-mobile";
import Cookies from "js-cookie";

function exitBanner() {
  const modalId = "exitModal";
  const modalWindow = document.querySelector(`#${modalId}`);
  const modalExists = !!modalWindow;
  const isMobile = mobile();
  const modalHasBeenDismissed = !!Cookies.get(`modal.${modalId}`);

  function initModal() {
    // eslint-disable-next-line no-undef
    const modal = new bootstrap.Modal(`#${modalId}`);
    modal.show();
    // Set cookie - don't show again for 1 day
    Cookies.set(`modal.${modalId}`, "1", { expires: 1 });
  }

  function assignEventListeners() {
    if (isMobile) {
      setTimeout(initModal, 6000);
    } else {
      document.addEventListener("mouseleave", initModal, { once: true });
    }
  }

  function init() {
    assignEventListeners();
    setTrackingParams();
  }

  function checkIfBootstrapIsLoaded(i = 0) {
    setTimeout(() => {
      if (typeof window.bootstrap !== "undefined") {
        init();
      } else if (i < 10) {
        checkIfBootstrapIsLoaded(i + 1);
      }
    }, 500);
  }

  if (modalExists && !modalHasBeenDismissed) {
    checkIfBootstrapIsLoaded();
  }

  function setTrackingParams() {
    const referrer = modalWindow.dataset.referrer;
    const subst = "_eb";
    const result = referrer + subst;

    buildYesBtnLink(result);
  }

  function buildYesBtnLink(referralVal) {
    const utmSource = modalWindow.dataset.utmSource;
    const yesBtnUrl = new URL("#quickApp", location);
    yesBtnUrl.searchParams.set("utm_source", utmSource);
    yesBtnUrl.searchParams.set("utm_campaign", referralVal);
    yesBtnUrl.searchParams.set("utm_medium", "eb");
    yesBtnUrl.searchParams.set("r", referralVal);

    document.getElementById("exitbannerLink").href = yesBtnUrl.href;
  }
}

export default exitBanner();
