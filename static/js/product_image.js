document.addEventListener("DOMContentLoaded", function() {
        var mainImage = document.getElementById("main-image");
        var thumbnails = document.querySelectorAll(".thumbnail");

        // Set initial click event for each thumbnail
        thumbnails.forEach(function(thumbnail) {
            thumbnail.addEventListener("click", function() {
                var index = this.getAttribute("data-index");
                mainImage.src = this.src; // Change main image src to clicked thumbnail
                mainImage.alt = this.alt; // Change alt text too, if needed
                mainImage.setAttribute("data-index", index); // Set the index for future reference
            });
        });
    });

