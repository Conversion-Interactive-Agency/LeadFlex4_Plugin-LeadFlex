function nav() {
  const mainNav = document.getElementById("nav");
  const mobileIcon = document.getElementById("mobile-icon");
  const useElement = document.querySelector("#mobile-icon > svg > use");

  function openCloseMenu() {
    mainNav.classList.toggle("block");
    mainNav.classList.toggle("active");
    useElement.setAttribute("xlink:href", "#close");
    mobileIcon.title = "Close Menu";

    // Check if the main nav is clicked closed
    if (!mainNav.classList.contains("block") && !mainNav.classList.contains("active")) {
      useElement.setAttribute("xlink:href", "#solid-bars");
      mobileIcon.title = "Open Menu";
    }
  }

  mobileIcon.addEventListener("click", openCloseMenu);
}

export default nav();
