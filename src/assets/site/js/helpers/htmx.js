export default function checkIfHtmxIsLoaded(count = 0) {
  if (count > 5) {
    return false;
  }
  setTimeout(() => {
    if (window.htmx) {
      return true;
    } else {
      checkIfHtmxIsLoaded(count + 1);
    }
  }, 500);
}
