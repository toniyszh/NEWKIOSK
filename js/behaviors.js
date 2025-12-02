//banner or ads? parallax
document.addEventListener("DOMContentLoaded", function () {
  const header = document.querySelector(".header");
  const images = ["../images/adsBanner/1.jpg"];

  let currentIndex = 0;

  setInterval(() => {
    currentIndex = (currentIndex + 1) % images.length;
    header.style.backgroundImage = `url('${images[currentIndex]}')`;
  }, 3000);
});

// UNIFIED DRAG SCROLL FUNCTION - Works for both vertical and horizontal scrolling
document.addEventListener("DOMContentLoaded", function () {
  function applyDragScroll(
    containerSelector,
    direction = "vertical",
    scrollSpeed = 2.5
  ) {
    const container = document.querySelector(containerSelector);

    if (!container) return;

    let isDragging = false;
    let startPos;
    let scrollPos;
    let lastPos;
    let velocity = 0;
    let animationFrame;

    // Mouse Events
    container.addEventListener("mousedown", function (e) {
      if (e.target.tagName !== "A" && e.target.tagName !== "BUTTON") {
        isDragging = true;
        startPos = direction === "horizontal" ? e.pageX : e.pageY;
        lastPos = startPos;
        scrollPos =
          direction === "horizontal"
            ? container.scrollLeft
            : container.scrollTop;
        velocity = 0;
        container.classList.add("dragging");
        container.style.cursor = "grabbing";

        // Cancel any ongoing momentum
        if (animationFrame) {
          cancelAnimationFrame(animationFrame);
        }

        e.preventDefault();
      }
    });

    document.addEventListener("mousemove", function (e) {
      if (!isDragging) return;
      e.preventDefault();

      const pos = direction === "horizontal" ? e.pageX : e.pageY;
      const delta = pos - lastPos;
      velocity = delta;
      lastPos = pos;

      const walk = (startPos - pos) * scrollSpeed;

      if (direction === "horizontal") {
        container.scrollLeft = scrollPos + walk;
      } else {
        container.scrollTop = scrollPos + walk;
      }
    });

    document.addEventListener("mouseup", function () {
      if (!isDragging) return;
      isDragging = false;
      container.classList.remove("dragging");
      container.style.cursor = "grab";

      // Apply momentum
      applyMomentum();
    });

    // Touch Events
    let touchStartTime = 0;
    let touchStartPos = 0;
    let touchLastPos = 0;
    let touchVelocity = 0;

    container.addEventListener(
      "touchstart",
      function (e) {
        if (e.target.tagName !== "A" && e.target.tagName !== "BUTTON") {
          isDragging = true;
          touchStartTime = Date.now();
          touchStartPos =
            direction === "horizontal"
              ? e.touches[0].clientX
              : e.touches[0].clientY;
          touchLastPos = touchStartPos;
          startPos =
            direction === "horizontal"
              ? e.touches[0].pageX
              : e.touches[0].pageY;
          scrollPos =
            direction === "horizontal"
              ? container.scrollLeft
              : container.scrollTop;
          velocity = 0;
          touchVelocity = 0;
          container.classList.add("dragging");

          // Cancel any ongoing momentum
          if (animationFrame) {
            cancelAnimationFrame(animationFrame);
          }
        }
      },
      { passive: true }
    );

    container.addEventListener(
      "touchmove",
      function (e) {
        if (!isDragging) return;

        const currentPos =
          direction === "horizontal"
            ? e.touches[0].clientX
            : e.touches[0].clientY;
        const delta = currentPos - touchLastPos;
        touchVelocity = delta;
        touchLastPos = currentPos;

        const pos =
          direction === "horizontal" ? e.touches[0].pageX : e.touches[0].pageY;
        const walk = (startPos - pos) * scrollSpeed;

        if (direction === "horizontal") {
          container.scrollLeft = scrollPos + walk;
        } else {
          container.scrollTop = scrollPos + walk;
        }
        e.preventDefault();
      },
      { passive: false }
    );

    container.addEventListener(
      "touchend",
      function (e) {
        if (!isDragging) return;
        isDragging = false;
        container.classList.remove("dragging");

        // Use the last tracked velocity for momentum
        velocity = touchVelocity * 3; // Amplify for better momentum
        applyMomentum();
      },
      { passive: true }
    );

    // Momentum function
    function applyMomentum() {
      if (Math.abs(velocity) > 0.5) {
        animationFrame = requestAnimationFrame(() => {
          if (direction === "horizontal") {
            container.scrollLeft -= velocity;
          } else {
            container.scrollTop -= velocity;
          }

          // Deceleration
          velocity *= 0.92;

          // Continue momentum
          if (Math.abs(velocity) > 0.5) {
            applyMomentum();
          }
        });
      }
    }

    document.addEventListener("mouseleave", function () {
      if (isDragging) {
        isDragging = false;
        container.classList.remove("dragging");
        container.style.cursor = "grab";
      }
    });

    container.addEventListener("click", function (e) {
      if (isDragging) {
        e.preventDefault();
        e.stopPropagation();
      }
    });

    // Set initial cursor
    container.style.cursor = "grab";
  }

  // Apply horizontal scrolling to .sidebar and vertical scrolling to .menu-content
  applyDragScroll(".sidebar", "horizontal", 2);
  applyDragScroll(".menu-content", "vertical", 2);
});

// SIDEBAR ARROW NAVIGATION
document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector(".sidebar");
  const leftArrow = document.querySelector(".sidebar-arrow-left");
  const rightArrow = document.querySelector(".sidebar-arrow-right");

  if (sidebar && leftArrow && rightArrow) {
    const scrollStep = 150;

    leftArrow.addEventListener("click", () => {
      sidebar.scrollLeft -= scrollStep;
      updateArrowVisibility();
    });

    rightArrow.addEventListener("click", () => {
      sidebar.scrollLeft += scrollStep;
      updateArrowVisibility();
    });

    function updateArrowVisibility() {
      const hasScrollLeft = sidebar.scrollLeft > 0;
      const hasScrollRight =
        sidebar.scrollLeft < sidebar.scrollWidth - sidebar.clientWidth - 5;

      leftArrow.style.opacity = hasScrollLeft ? "1" : "0.3";
      rightArrow.style.opacity = hasScrollRight ? "1" : "0.3";

      leftArrow.disabled = !hasScrollLeft;
      rightArrow.disabled = !hasScrollRight;
    }

    sidebar.addEventListener("scroll", updateArrowVisibility);
    updateArrowVisibility();

    function checkOverflow() {
      if (sidebar.scrollWidth > sidebar.clientWidth) {
        leftArrow.style.display = "flex";
        rightArrow.style.display = "flex";
      } else {
        leftArrow.style.display = "none";
        rightArrow.style.display = "none";
      }
    }

    checkOverflow();
    window.addEventListener("resize", checkOverflow);
  }

  // Add styles
  const style = document.createElement("style");
  style.textContent = `
    .sidebar-container {
      position: relative !important;
    }
    
    .sidebar, .menu-content {
      scroll-behavior: smooth !important;
    }
    
    .dragging {
      cursor: grabbing !important;
      user-select: none !important;
      scroll-behavior: auto !important;
    }
    
    .sidebar-arrow:active {
      transform: translateY(-50%) scale(0.95) !important;
    }
    
    .sidebar-arrow[disabled] {
      opacity: 0.3 !important;
      cursor: default !important;
      pointer-events: none !important;
    }
  `;
  document.head.appendChild(style);
});

//fullscreen automatics
function enableFullScreen() {
  const elem = document.documentElement;

  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.webkitRequestFullscreen) {
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) {
    elem.msRequestFullscreen();
  } else if (elem.mozRequestFullScreen) {
    elem.mozRequestFullScreen();
  }
}

document.addEventListener("DOMContentLoaded", function () {
  enableFullScreen();

  document.addEventListener(
    "click",
    function onFirstClick() {
      enableFullScreen();
      document.removeEventListener("click", onFirstClick);
    },
    { once: true }
  );
});

//disable inspect screen element
document.addEventListener("contextmenu", function (e) {
  e.preventDefault();
});

// iOS Safari Mobile Detection and Modal Content Fix
function detectiOSSafariMobile() {
  const userAgent = navigator.userAgent;
  const platform = navigator.platform;
  const maxTouchPoints = navigator.maxTouchPoints;

  const isIOS =
    /iPad|iPhone|iPod/.test(userAgent) ||
    (platform === "MacIntel" && maxTouchPoints > 1);

  const isSafari =
    /Safari/.test(userAgent) && !/Chrome|CriOS|FxiOS|EdgiOS/.test(userAgent);

  const isMobile = window.innerWidth <= 768;

  const hasWebkit = /WebKit/.test(userAgent);
  const hasVersion = /Version\//.test(userAgent);

  return isIOS && isSafari && isMobile && hasWebkit && hasVersion;
}

function applyiOSSafariModalFix() {
  if (detectiOSSafariMobile()) {
    const modalElements = document.querySelectorAll(".modal-content1");
    modalElements.forEach((element) => {
      element.style.paddingBottom = "80px";
    });

    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === "childList") {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1) {
              if (node.classList && node.classList.contains("modal-content1")) {
                node.style.paddingBottom = "80px";
              }
              const modalContents = node.querySelectorAll(".modal-content1");
              modalContents.forEach((element) => {
                element.style.paddingBottom = "80px";
              });
            }
          });
        }
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });

    return true;
  }
  return false;
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", applyiOSSafariModalFix);
} else {
  applyiOSSafariModalFix();
}

window.addEventListener("resize", function () {
  setTimeout(applyiOSSafariModalFix, 100);
});

if (
  /iPad|iPhone|iPod/.test(navigator.userAgent) &&
  /Safari/.test(navigator.userAgent) &&
  !/Chrome|CriOS/.test(navigator.userAgent) &&
  window.innerWidth <= 768
) {
  const applyPadding = () =>
    document
      .querySelectorAll(".modalcontent")
      .forEach((el) => (el.style.paddingBottom = "80px"));
  applyPadding();
  new MutationObserver(() => applyPadding()).observe(document.body, {
    childList: true,
    subtree: true,
  });
}
