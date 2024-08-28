(function cookieConsentBanner() {

  let consent = {
    // CSS selector for modal
    modal: "[data-component='consent-modal']",

    // Fade transition (in milliseconds)
    fadeTransition: 300,

    // Name of cookie
    cookie: "consent-cookie",

    // Default status of whether user has accepted
    accepted: false,

    // Default consent states
    types: {
      ad_storage: "granted",
      ad_user_data: "granted",
      ad_personalization: "granted",
      analytics_storage: "granted"
    },

    // Array of callbacks to be executed when consent changes
    consentListeners: [],

    // Supported actions
    actions: {
      show: "show",
      close: "close",
      grant: "grant",
      grantAll: "grantAll"
    },

    // Data attributes assigned to HTML elements to control/manage things
    data: {
      action: "data-consent",
      currentView: "data-current-view",
      view: "data-consent-view",
      types: "data-consent-types"
    },

    /** Start consent functions **/

    // Show modal popup
    show: function(view = 1) {
      let modal = document.querySelector(this.modal);

      // Swap view
      document.querySelector(`[${this.data.currentView}]`).setAttribute(this.data.currentView, view);

      // Show it
      if (modal.style.display === "none" || !modal.style.display) {
        modal.style.display = "block";
        setTimeout(() => {
          modal.style.opacity = "1";
        }, 10); // To allow transition effect
        modal.style.transition = `opacity ${this.fadeTransition}ms`;
      }
    },

    // Close modal popup
    close: function() {
      let modal = document.querySelector(this.modal);
      modal.style.opacity = "0";
      setTimeout(() => {
        modal.style.display = "none";
      }, this.fadeTransition);
    },

    // Update types based on ticked input checkboxes
    updateTypes: function() {
      let types = {};
      let dataType = this.data.types;
      document.querySelectorAll(`[${dataType}]`).forEach(input => {
        let inputTypes = input.getAttribute(dataType).split(",");
        inputTypes.forEach(type => {
          types[type] = input.checked ? "granted" : "denied";
        });
      });
      this.types = types;
    },

    // Tick input checkboxes based on given types
    tickTypes: function(checkAll = false, types = this.types) {
      let dataType = this.data.types;
      document.querySelectorAll(`[${this.data.types}]`).forEach(input => {
        let inputTypes = input.getAttribute(dataType).split(",");
        inputTypes.forEach(type => {
          input.checked = checkAll || types[type] === "granted";
        });
      });
    },

    // Sets info to cookie from current values
    updateCookie: function(accepted = this.accepted, types = this.types) {
      let cookie = {
        accepted: accepted,
        types: types
      };

      let consentListeners = this.consentListeners;
      let args = {
        secure: true,
        expires: 30 // Expires after 30 days
      };

      // if (typeof website !== "undefined") {
      //   args.domain = website.domain;
      // }

      document.cookie = `${this.cookie}=${JSON.stringify(cookie)};path=/;max-age=${args.expires * 24 * 60 * 60};${args.secure ? "secure;" : ""}${args.domain ? `domain=${args.domain};` : ""}`;

      // Trigger consent listeners
      consentListeners.forEach(callback => {
        callback(cookie.types);
      });
    },

    init: function() {
      // Update data with cookie
      let cookie = document.cookie.split("; ").find(row => row.startsWith(this.cookie + "="));
      let consentListeners = [];

      if (cookie) {
        cookie = JSON.parse(cookie.split("=")[1]);
        this.accepted = cookie.accepted;
        this.types = cookie.types;

        // Tick checkboxes based on types
        this.tickTypes();
      } else {
        // If there is no cookie data, check all options
        this.tickTypes(true);
        this.updateTypes();
      }

      // Show modal if user has not accepted
      if (!this.accepted) {
        this.show();
      }

      // Allow adding consent listeners
      window.addConsentListener = function(callback) {
        consentListeners.push(callback);
      };
      this.consentListeners = consentListeners;
    }
  };

  // Initialize consent handling on page load
  consent.init();

  // ✓ -- Save
  document.querySelectorAll(`[${consent.data.action}="${consent.actions.grant}"]`).forEach(function(element) {
    element.addEventListener("click", function() {
      // Update types from ticked checkboxes
      consent.updateTypes();

      // Update cookie
      consent.updateCookie(true);

      // Close
      consent.close();
    });
  });

  // ✓ -- Accept all
  document.querySelectorAll(`[${consent.data.action}="${consent.actions.grantAll}"]`).forEach(function(element) {
    element.addEventListener("click", function() {
      // Tick all checkboxes
      consent.tickTypes(true);

      // Update types from ticked checkboxes
      consent.updateTypes();

      // Update cookie
      consent.updateCookie(true);

      // Close
      consent.close();
    });
  });

  // ✓ -- Any of the elements to close pop-up
  document.querySelectorAll(`[${consent.data.action}="${consent.actions.close}"]`).forEach(function(element) {
    element.addEventListener("click", function() {
      consent.close();
    });
  });

  // ✓ -- Any of the elements to show
  document.querySelectorAll(`[${consent.data.action}="${consent.actions.show}"]`).forEach(function(element) {
    element.addEventListener("click", function() {
      let view = element.getAttribute(consent.data.view) || 1;
      consent.show(view);
    });
  });
})();
