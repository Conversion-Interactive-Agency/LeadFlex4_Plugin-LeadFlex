function initTableSorting() {
    const table = document.querySelector("#overlaps");
    if (!table) {return;}

    const headers = table.querySelectorAll("thead th");
    headers.forEach(header => {
        header.addEventListener("click", () => {
            const tableBody = table.querySelector("tbody");
            const rows = Array.from(tableBody.querySelectorAll("tr"));
            const index = Array.from(headers).indexOf(header);
            const isAscending = header.classList.contains("asc");

            // Remove sorting classes and hide icons for all headers
            headers.forEach(h => {
                h.classList.remove("asc", "desc");
                const icon = h.querySelector(".sort-icon");
                if (icon) {
                    icon.style.display = "none"; // Hide all sort icons
                }
            });

            // Sort rows based on the clicked column
            rows.sort((a, b) => {
                const aText = a.children[index].textContent.trim();
                const bText = b.children[index].textContent.trim();

                return isAscending
                    ? aText.localeCompare(bText)
                    : bText.localeCompare(aText);
            });

            // Append sorted rows back to the table body
            rows.forEach(row => tableBody.appendChild(row));

            // Toggle sorting class and show the active sort icon
            header.classList.toggle("asc", !isAscending);
            header.classList.toggle("desc", isAscending);
            // Get the sort icon for the active header
            let sortIcon = header.querySelector(".sort-icon");
            sortIcon.style.display = "inline"; // Show the active sort icon
        });
    });
}

export default initTableSorting;
