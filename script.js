const leftArrow = document.getElementById("left-arrow");
const rightArrow = document.getElementById("right-arrow");

const imagesContainer = document.getElementById("image-container");
let images = document.querySelectorAll(".model-images");

// This was done incase the user wanted to start first by pressing the left button
let cloneLastImage = images[images.length - 1].cloneNode(true); 
imagesContainer.prepend(cloneLastImage);


// For the progressBar
const imageCount = images.length;
let currentImage = 0;

let firstImageIndexValue = 0;
let currentFirstImage = images[0];
let currentLastImage = images[images.length - 1];


// GSAP Animations
function buttonPressedAnimation(buttonId) {
  let rule = CSSRulePlugin.getRule(buttonId);
  let tl = gsap.timeline();

  gsap.set(rule, {
    cssRule: {
      scale: 1,
      border: "solid 0.1rem #fff",
      opacity: 0,
    },
  });

  tl.to(rule, {
    duration: .2,
    cssRule: {
      scale: 1.5,
      opacity: 1,
    },
  });

  tl.to(rule, {
    duration: 0.2,
    cssRule: {
      scale: 3,
      opacity: 0,
    },
    ease: "power2.out",
  });
    
    tl.to(rule, {
      duration: 0.2,
      cssRule: {
        scale: 1,
      },
      ease: "power2.in",
    });
}

function staggerImageAnimation(fromValue, toValue, direction) {
  gsap.fromTo(
    ".model-images",
    {
      translate: fromValue,
    },
    {
      translate: toValue,
      stagger: {
        from: direction,
        amount: 0.3,
      },
      ease: "power2.inOut",
    }
  );
}

function progressBarAnimation() {
  gsap.to("#progress-bar", {
    scaleX: `${1 / imageCount + (currentImage % imageCount) / imageCount}`,
    duration: 0.3 * ((imageCount - 1) / 2),
    ease: "power2.inOut",
  });
}

gsap.set("#progress-bar", {
  scaleX: `${1 / imageCount + currentImage / imageCount}`,
});

// Gsap animation ends

// Image Placements
function moveImagesTotheLeft() {
  images = document.querySelectorAll(".model-images");
  let cloneFirstImage = images[1].cloneNode(true);
  imagesContainer.append(cloneFirstImage);

  let fromValue = `0`;
  let toValue = `calc(-100% - 0.5rem) `;

  staggerImageAnimation(fromValue, toValue, "start");
  images[0].remove();
}

function moveImagesTotheRight() {
  images = document.querySelectorAll(".model-images");
  let cloneLastImage = images[images.length - 2].cloneNode(true);

  imagesContainer.prepend(cloneLastImage);
  let fromValue = `calc(-200% - 1rem)`;
  let toValue = `calc(-100% - 0.5rem) `;
  staggerImageAnimation(fromValue, toValue, "end");
  images[images.length - 1].remove();
}
// Image Placements Ends

// Event Listeners
leftArrow.addEventListener("click", () => {
  moveImagesTotheRight();
  buttonPressedAnimation("#left-arrow:before");
  gsap.set("#progress-bar", {
    scaleX: `${1 / imageCount + (currentImage % imageCount) / imageCount}`,
  });
  currentImage = (currentImage - 1) % imageCount;

  if (currentImage < 0) {
    currentImage = 3;
  }

  progressBarAnimation();
});

rightArrow.addEventListener("click", () => {
  moveImagesTotheLeft();
  buttonPressedAnimation("#right-arrow:before");
  gsap.set("#progress-bar", {
    scaleX: `${1 / imageCount + (currentImage % imageCount) / imageCount}`,
  });

  currentImage = (currentImage + 1) % imageCount;

  progressBarAnimation();
});





// Event Listeners Ends