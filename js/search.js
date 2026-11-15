// Search a table as you type
function searchTable(searchBoxId, tableId) {
    var searchBox = document.getElementById(searchBoxId);
    var searchText = searchBox.value.toUpperCase();
    var table = document.getElementById(tableId);
    var rows = table.getElementsByTagName("tr");

    // Start at 1 to skip header row
    for (var i = 1; i < rows.length; i++) {
        var rowText = rows[i].textContent.toUpperCase();

        if (rowText.includes(searchText)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}

// Show popup
function openPopup(popupId) {
    var popup = document.getElementById(popupId);
    popup.style.display = "flex";
}

// Hide popup
function closePopup(popupId) {
    var popup = document.getElementById(popupId);
    popup.style.display = "none";
}