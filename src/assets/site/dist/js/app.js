function r(){const a=document.querySelectorAll("[data-event-trigger]");function o(t){const e=t.dataset.googleEventCategory,n=t.dataset.facebookEventTrack;e&&window.hasOwnProperty("dataLayer")&&c(e),n&&window.hasOwnProperty("fbq")&&E(n)}function c(t){let e={event:"click",method:"",transport_type:"beacon",event_category:t};window.dataLayer.push(e)}function E(t){window.fbq("track",t)}a.forEach(t=>{const e=t.dataset.eventTriggerType||"click";t.addEventListener(e,()=>o(t))})}r();
//# sourceMappingURL=app.js.map