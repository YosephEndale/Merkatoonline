function updateQuantity(index, quantity) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Update cart display after quantity is updated
            window.location.reload();
        }
    };
    xhr.send("index=" + index + "&quantity=" + quantity);
}
