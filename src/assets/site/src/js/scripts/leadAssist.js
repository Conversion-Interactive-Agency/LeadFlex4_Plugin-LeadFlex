function leadAssist() {
  const leadAssistBlock = document.querySelector("[data-component=\"lead-assist\"]");
  if (!leadAssistBlock) {return;}

  const coll = leadAssistBlock.getElementsByClassName("collapsible");
  for (let i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function() {
      addAccordionEventListener(this);
    });
  }

  function addAccordionEventListener(element) {
    element.classList.toggle("active");
    const content = element.nextElementSibling;
    if (content) {
      content.classList.toggle("block");
    }
  }

  (function(d, s, id) {
    let js;
    const fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, "script", "facebook-jssdk"));

  function openForm() {
    document.getElementById("myForm").style.display = "block";
  }

  function closeForm() {
    document.getElementById("myForm").style.display = "none";
  }
}

export default leadAssist();
