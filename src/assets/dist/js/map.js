(function(){function T(){const c=document.querySelector("#overlaps");if(!c)return;const v=c.querySelectorAll("thead th");v.forEach(f=>{f.addEventListener("click",()=>{const x=c.querySelector("tbody"),n=Array.from(x.querySelectorAll("tr")),d=Array.from(v).indexOf(f),p=f.classList.contains("asc");v.forEach(i=>{i.classList.remove("asc","desc");const l=i.querySelector(".sort-icon");l&&(l.style.display="none")}),n.sort((i,l)=>{const t=i.children[d].textContent.trim(),y=l.children[d].textContent.trim();return p?t.localeCompare(y):y.localeCompare(t)}),n.forEach(i=>x.appendChild(i)),f.classList.toggle("asc",!p),f.classList.toggle("desc",p);let u=f.querySelector(".sort-icon");u.style.display="inline"})})}(function(){const c={true:{hex:"#15803d",tw:"text-green-700"},false:{hex:"#be123c",tw:"text-red-700"}};function v(n){console.log("Flashing element:",n),n.classList.add("flash"),setTimeout(()=>{n.classList.remove("flash")},1e3)}function f(n){let d=!0;const p=1.05,u=.95;let i=0;const l=2,t=n.getRadius(),y=1500;function w(){if(i>=l)return;let g=n.getRadius(),m=g*.0085;d?(g+=m,g>=t*p&&(d=!1)):(g-=m,g<=t*u&&(d=!0,i++)),n.setRadius(g),n.setOptions({fillOpacity:.35*(g/t)}),i<l&&setTimeout(w,y/60)}w()}function x(){console.log("Initializing map...");const n=new google.maps.Map(document.getElementById("map"),{center:{lat:37.0902,lng:-95.7129},zoom:4}),d=[],p=new google.maps.LatLngBounds,u=new google.maps.InfoWindow,i=document.getElementById("loading-indicator");fetch("/leadflex/map/locations").then(l=>(console.log("Fetching locations..."),l.json())).then(l=>{console.log("Locations fetched:",l),i&&(i.style.display="none"),l.forEach(t=>{if(!t.location.coords.lat||!t.location.coords.lng){console.error("Invalid coordinates for job:",t.title);return}console.log("Creating circle for location:",t.title);const y=new google.maps.Circle({strokeColor:t.circle.strokeColor??c[t.advertiseJob].hex,strokeOpacity:t.circle.strokeOpacity??.8,strokeWeight:t.circle.strokeWeight??2,fillColor:t.circle.fillColor??c[t.advertiseJob].hex,fillOpacity:t.circle.fillOpacity??.35,map:n,center:t.location.coords,radius:t.hiringRadius});p.extend(t.location.coords),d.push({circle:y,id:t.id,title:t.title,data:t}),y.addListener("click",w=>{console.log(`Circle for ${t.title} clicked at:`,w.latLng);const g=w.latLng,m=[];if(d.forEach(({circle:$,data:o})=>{const e=$.getCenter(),h=google.maps.geometry.spherical.computeDistanceBetween(e,g);console.log(`Checking circle for ${o.title}: Distance = ${h}, Radius = ${$.getRadius()}`),h<=$.getRadius()&&(console.log(`Circle for ${o.title} overlaps with click location.`),m.push(o))}),m.length>0){console.log("Overlapping jobs found:",m);const $=document.querySelector("#overlaps tbody");$.innerHTML="";let o="<div class='info-window-content'>";m.forEach((e,h)=>{let k=(e.advertiseJob?"check":"x")+"-square-fill",r=document.createElement("svg");r.className=`h-4 w-4 mx-2 ${c[e.advertiseJob].tw}`;let C=document.createElement("use");C.setAttribute("xlink:href",`#${k}`),r.appendChild(C);const s=document.createElement("tr");s.className="border-t border-gray-300 job-info",s.dataset.job=JSON.stringify(e),s.dataset.jobId=e.id;let a="px-4 py-2 border-b border-gray-300";s.innerHTML+=`<td class="">${e.title}</td>`,s.innerHTML+=`<td class="${a}">${e.types.driver}</td>`,s.innerHTML+=`<td class="${a}">${e.types.trailer}</td>`,s.innerHTML+=`<td class="${a}">${e.types.job}</td>`,s.innerHTML+=`<td class="${a}">${e.location.city}</td>`,s.innerHTML+=`<td class="${a}">${e.location.state}</td>`,s.innerHTML+=`<td class="${a}">${Math.round(e.hiringRadius/1609.34)}</td>`,s.innerHTML+=`<td class="${a}">${e.assignedCampaigns}</td>`,s.innerHTML+=`<td class="${a}">
                                    <div class="flex items-center justify-center" data-modal-triggers>
                                        <svg class="h-4 w-4 mx-2 ${e.advertiseJob?c[e.advertiseJob].tw:""}" data-advertise=true>
                                            <use xlink:href="#check-square-fill"></use>
                                        </svg>
                                        <svg class="h-4 w-4 mx-2 ${e.advertiseJob?"":c[e.advertiseJob].tw}" data-advertise=false>
                                            <use xlink:href="#x-square-fill"></use>
                                        </svg>
                                    </div>
                                  </td>`,s.innerHTML+=`<td class="${a}">
                                    <a href="${e.url}" target="_blank" class="text-blue-500 hover:underline">View Job</a> 
                                  </td>`,$.appendChild(s),r.className=`h-2 w-2 mr-1 ${c[e.advertiseJob].tw}`,o+=`<div class='job-info py-2' data-job-id='${e.id}'>`,o+=`<a class="underline blue-700" href='${e.url}' target='_blank'>`,o+=`<h3><span class="flex items-center">${r.outerHTML}${e.title}</span></h3>`,o+="</a>",o+="<ul class='list-disc pl-6'>",e.additionalInfo.forEach(L=>{o+=`<li><strong>${L.label}:</strong> ${L.value}</li>`}),o+=`<li><strong>Driver Type:</strong> ${e.types.driver}</li>`,o+=`<li><strong>Trailer Type:</strong> ${e.types.trailer}</li>`,o+=`<li><strong>Job Type:</strong> ${e.types.job}</li>`,o+=`<li><strong>City:</strong> ${e.location.city}</li>`,o+=`<li><strong>Assigned Campaign:</strong> ${e.assignedCampaigns?e.assignedCampaigns:"<i>none</i>"}</li>`,o+="</ul>",o+="</div>",h<m.length-1&&(o+="<hr>")}),o+="</div>",u.close(),u.setContent(o),u.setPosition(g),u.open(n),google.maps.event.addListenerOnce(u,"domready",()=>{const e=document.querySelector(".gm-ui-hover-effect");e&&e.focus(),document.querySelectorAll(".job-info").forEach(h=>{console.log(h),h.addEventListener("click",k=>{k.preventDefault();const r=parseInt(h.getAttribute("data-job-id"));if(isNaN(r)){console.error("Invalid jobId:",r);return}console.log("Job info clicked:",r);const C=d.find(L=>L.data.id===r);C?f(C.circle):console.error("Circle not found for job:",r);const s=document.querySelector(`tr[data-job-id="${r}"]`);s?v(s):console.error("Row not found for job:",r);const a=document.querySelector(`.info-window-content .job-info[data-job-id="${r}"]`);a?v(a):console.error("Info window not found")})})}),T()}else console.log("No overlapping circles found.")})}),n.fitBounds(p)}).catch(l=>{console.error("Error fetching locations:",l),i&&(i.style.display="none")})}window.onload=x})();
//# sourceMappingURL=map.js.map
})()