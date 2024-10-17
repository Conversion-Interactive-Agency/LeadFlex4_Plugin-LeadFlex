/* global google */

import initTableSorting from "./table";

(function () {
  // Wait for the Google Maps API to be loaded
  const advertiseColors = {
    true: {
      hex: "#15803d",
      tw: "text-green-700" // green-700
    }, // green-700
    false: {
      hex: "#be123c",
      tw: "text-red-700" // red-700
    }
  };

  // Function to flash an element
  function flashElement(element) {
    console.log("Flashing element:", element);
    element.classList.add("flash");
    setTimeout(() => {
      element.classList.remove("flash");
    }, 1000); // Flash duration
  }

  // Flash the circle
  function animatePulse(circle) {
    let growing = true;
    const maxScaleFactor = 1.05; // 5% increase from initial radius
    const minScaleFactor = 0.95; // 5% decrease from initial radius
    let pulseCount = 0;
    const maxPulses = 2; // Limit to 3 pulses
    const baseRadius = circle.getRadius();
    const pulseDuration = 1500; // 1.5s per pulse

    function pulse() {
      if (pulseCount >= maxPulses) {return;} // Stop after 3 pulses

      let currentRadius = circle.getRadius();
      let change = currentRadius * 0.0085; // ±1% of the current radius

      if (growing) {
        currentRadius += change;
        if (currentRadius >= baseRadius * maxScaleFactor) {
          growing = false;
        }
      } else {
        currentRadius -= change;
        if (currentRadius <= baseRadius * minScaleFactor) {
          growing = true;
          pulseCount++; // Increment pulse count after a full cycle
        }
      }

      // Update the circle's radius and opacity
      circle.setRadius(currentRadius);
      circle.setOptions({
        fillOpacity: 0.35 * (currentRadius / baseRadius),
      });

      // Smooth the animation by spacing out frames
      if (pulseCount < maxPulses) {
        setTimeout(pulse, pulseDuration / 60); // Slower, smoother animation
      }
    }

    pulse(); // Start the pulse
  }

  function initMap() {
    console.log("Initializing map...");

    // Create a map centered to show the lower 48 states of the USA
    const map = new google.maps.Map(document.getElementById("map"), {
      center: {lat: 37.0902, lng: -95.7129}, // Approximate center of the contiguous USA
      zoom: 4 // Zoom level to show the lower 48 states
    });

    // Array to store all circles
    const circles = [];
    const bounds = new google.maps.LatLngBounds();

    // Create a single info window instance
    const infoWindow = new google.maps.InfoWindow();

    // Fetch location data
    fetch("/leadflex/map/locations")
      .then(response => {
        console.log("Fetching locations...");
        return response.json();
      })
      .then(locations => {
        console.log("Locations fetched:", locations);

        locations.forEach(job => {
          // exit early if coords are not lat, lng
          if (!job.location.coords.lat || !job.location.coords.lng) {
            console.error("Invalid coordinates for job:", job.title);
            return;
          }

          console.log("Creating circle for location:", job.title);
          // Create a circle for each location
          const circle = new google.maps.Circle({
            strokeColor: job.circle.strokeColor ?? advertiseColors[job.advertiseJob].hex,
            strokeOpacity: job.circle.strokeOpacity ?? 0.8,
            strokeWeight: job.circle.strokeWeight ?? 2,
            fillColor: job.circle.fillColor ?? advertiseColors[job.advertiseJob].hex,
            fillOpacity: job.circle.fillOpacity ?? 0.35,
            map: map,
            center: job.location.coords,
            radius: job.hiringRadius
          });

          // Extend the bounds to include this circle's center
          bounds.extend(job.location.coords);

          // Store the circle and the job data
          circles.push({circle, id:job.id, title: job.title, data: job});

          // Add a click event listener to the circle
          circle.addListener("click", (event) => {
            console.log(`Circle for ${job.title} clicked at:`, event.latLng);

            const clickedLocation = event.latLng;
            const overlappingJobs = [];

            // Check for overlapping circles
            circles.forEach(({circle, data}) => {
              const circleCenter = circle.getCenter();
              const distance = google.maps.geometry.spherical.computeDistanceBetween(circleCenter, clickedLocation);

              console.log(`Checking circle for ${data.title}: Distance = ${distance}, Radius = ${circle.getRadius()}`);

              if (distance <= circle.getRadius()) {
                console.log(`Circle for ${data.title} overlaps with click location.`);
                overlappingJobs.push(data);
              }
            });

            if (overlappingJobs.length > 0) {
              console.log("Overlapping jobs found:", overlappingJobs);

              // Populate the table with overlapping jobs
              const tbody = document.querySelector("#overlaps tbody");
              tbody.innerHTML = ""; // Clear existing rows

              let infoWindowContent = "<div class='info-window-content'>";
              overlappingJobs.forEach((job, index) => {
                // Get the icons html related to advertiseJob or not
                let svgId = (job.advertiseJob ? "check" : "x") + "-square-fill";
                // built this html with javascript functions
                let advertiseSvg = document.createElement("svg");
                advertiseSvg.className = `h-4 w-4 mx-2 ${advertiseColors[job.advertiseJob].tw}`;
                let useElement = document.createElement("use");
                useElement.setAttribute("xlink:href", `#${svgId}`);
                advertiseSvg.appendChild(useElement);

                // Create a row for each job
                const row = document.createElement("tr");
                row.className = "border-t border-gray-300 job-info"; // Add Tailwind classes for row borders
                row.dataset.job = JSON.stringify(job);
                row.dataset.jobId = job.id;
                let tableDataClasses = "px-4 py-2 border-b border-gray-300";

                row.innerHTML = `
                  <td class="">${job.title}</td>
                  <td class="${tableDataClasses}">${job.types.driver}</td>
                  <td class="${tableDataClasses}">${job.types.trailer}</td>
                  <td class="${tableDataClasses}">${job.types.job}</td>
                  <td class="${tableDataClasses}">${job.location.city}</td>
                  <td class="${tableDataClasses}">${job.location.state}</td>
                  <td class="${tableDataClasses}">${Math.round(job.hiringRadius / 1609.34)}</td>
                  <td class="${tableDataClasses}">${job.assignedCampaigns}</td>
                   <td class="${tableDataClasses}">
                        <div class="flex items-center justify-center" data-modal-triggers>
                            <svg class="h-4 w-4 mx-2 ${job.advertiseJob ? advertiseColors[job.advertiseJob].tw : ""}" data-advertise=true>
                                <use xlink:href="#check-square-fill"></use>
                            </svg>
                            <svg class="h-4 w-4 mx-2 ${!job.advertiseJob ? advertiseColors[job.advertiseJob].tw : ""}" data-advertise=false>
                                <use xlink:href="#x-square-fill"></use>
                            </svg>
                        </div>
                    </td>
                    <td class="${tableDataClasses}">
                    <a href="${job.url}" target="_blank" class="text-blue-500 hover:underline">View Job</a> 
                  </td>
                `;
                tbody.appendChild(row);

                advertiseSvg.className = `h-2 w-2 mr-1 ${advertiseColors[job.advertiseJob].tw}`;
                // Populate the info window with the job info
                infoWindowContent += `<div class='job-info py-2' data-job-id='${job.id}'>`;
                infoWindowContent += `<a class="underline blue-700" href='${job.url}' target='_blank'>`;
                infoWindowContent += `<h3><span class="flex items-center">${advertiseSvg.outerHTML}${job.title}</span></h3>`;
                infoWindowContent += "</a>";
                infoWindowContent += "<ul class='list-disc pl-6'>";
                infoWindowContent += `<li><strong>Driver Type:</strong> ${job.types.driver}</li>`;
                infoWindowContent += `<li><strong>Trailer Type:</strong> ${job.types.trailer}</li>`;
                infoWindowContent += `<li><strong>Job Type:</strong> ${job.types.job}</li>`;
                infoWindowContent += `<li><strong>City:</strong> ${job.location.city}</li>`;
                infoWindowContent += `<li><strong>Assigned Campaign:</strong> ${job.assignedCampaigns ? job.assignedCampaigns : "<i>none</i>"}</li>`;
                infoWindowContent += "</ul>";
                infoWindowContent += "</div>";

                // Add a separator between jobs if there is more than one
                if (index < overlappingJobs.length - 1) {
                  infoWindowContent += "<hr>";
                }
              });
              infoWindowContent += "</div>";

              // Close the current info window before opening a new one
              infoWindow.close();

              // Set the content of the info window to the list of titles
              infoWindow.setContent(infoWindowContent);
              infoWindow.setPosition(clickedLocation);
              infoWindow.open(map);

              
              google.maps.event.addListenerOnce(infoWindow, "domready", () => {
                // Focus on the close button when the info window is opened
                const closeButton = document.querySelector(".gm-ui-hover-effect");
                if (closeButton) {
                  closeButton.focus();
                }

                document.querySelectorAll(".job-info").forEach(container => {
                  console.log(container);
                  container.addEventListener("click", (e) => {
                    e.preventDefault();
                    // parse to int
                    const jobId = parseInt(container.getAttribute("data-job-id"));
                    // exit early if jobId is not a number
                    if (isNaN(jobId)) {
                      console.error("Invalid jobId:", jobId);
                      return;
                    }

                    console.log("Job info clicked:", jobId);

                    // Flash the corresponding circle
                    const circleData = circles.find(c => c.data.id === jobId);
                    if (circleData) {
                      animatePulse(circleData.circle);
                    } else {
                      console.error("Circle not found for job:", jobId);
                    }
    
                    // Flash the corresponding table row
                    const row = document.querySelector(`tr[data-job-id="${jobId}"]`);
                    if (row) {
                      flashElement(row);
                    } else {
                      console.error("Row not found for job:", jobId);
                    }

                    //  Flash the row in the info window // info-window-content
                    const infoWindow = document.querySelector(`.info-window-content .job-info[data-job-id="${jobId}"]`);
                    if (infoWindow) {
                      flashElement(infoWindow);
                    } else {
                      console.error("Info window not found");
                    }
                  });
                });
              });

              // Initialize table sorting after populating the table
              initTableSorting();
            } else {
              console.log("No overlapping circles found.");
            }
          });
        });

        // Adjust the map to fit all the circles
        map.fitBounds(bounds);
      })
      .catch(error => console.error("Error fetching locations:", error));
  }

  // Initialize the map when the window loads
  window.onload = initMap;
})();
