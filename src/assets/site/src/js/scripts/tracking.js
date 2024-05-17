function tracking() {
  const TRACKING_ELEMENTS = document.querySelectorAll("[data-event-trigger]");

  // Query data attributes if the tracked button is clicked and invoke the respective function
  function handleEvent(node) {
    const GOOGLE_EVENT = node.dataset.googleEventCategory;
    const FACEBOOK_EVENT = node.dataset.facebookEventTrack;

    if (GOOGLE_EVENT && Object.prototype.hasOwnProperty.call("window", "dataLayer")) {
      pushGoogleEvent(GOOGLE_EVENT);
    }

    if (FACEBOOK_EVENT && Object.prototype.hasOwnProperty.call("window", "fbq")) {
      pushFacebookEvent(FACEBOOK_EVENT);
    }
  }

  // Sends event to Google
  function pushGoogleEvent(event) {
    window.dataLayer.push({
      event: "click",
      method: "",
      transport_type: "beacon",
      event_category: event,
    });
  }

  // Sends event to Facebook
  function pushFacebookEvent(event) {
    window.fbq("track", event);
  }

  // Adds an event listener to all tracked buttons
  TRACKING_ELEMENTS.forEach(node => {
    const EVENT_LISTENER_TYPE = node.dataset.eventTriggerType || "click";
    node.addEventListener(EVENT_LISTENER_TYPE, () => handleEvent(node));
  });
}

export default tracking();
// Path: src/assets/site/src/js/scripts/tracking.js