window.addEventListener("load", function () {
    setTimeout(function () {
      const preloader = document.getElementById("preloader");
      preloader.classList.add("fade", "show"); // Initially visible
      
      setTimeout(() => {
        preloader.classList.remove("show"); // This triggers fade out
        setTimeout(() => {
          preloader.classList.add("d-none");
        }, 300); // Wait for fade transition
      }, 500);
    }, 500);
});