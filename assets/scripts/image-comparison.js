//https://www.w3schools.com/howto/howto_js_image_comparison.asp
window.addEventListener('load', function () {

  console.log(rawdata);

  var imgCompContainer = document.querySelector('.img-comp-container');
  var imageSelectLeft = document.getElementById('selectImage2');
  var imageSelectRight = document.getElementById('selectImage1');
  var optionsGroup = '';
  var images = Object.values(compareImages);

  // prepare the select content
  images.forEach(image => {
    optionsGroup += `<option value="${image}">${image}</option>`;
  })
  imageSelectRight.innerHTML = optionsGroup;
  imageSelectLeft.innerHTML = optionsGroup;

  // get the image containers
  var imageContainers = document.querySelectorAll('.img-comp-img > img')

  imageContainers.forEach( img => {
    // set the image src
    img.src = imagepath + images[0];

    img.onload = function() {
      // if is the image 1 set also the container height
      if (img.src === imageContainers[0].src) {
        imgCompContainer.style.height = this.naturalHeight + 'px';
        imgCompContainer.style.width = this.naturalWidth + 'px';
      }
      // set the image size
      img.width = this.naturalWidth;
      img.height = this.naturalHeight;
    }
  });

  var pageloaded = false;
  imageContainers[1].onload = function () {
    if (!pageloaded) imageCompare();
    pageloaded = true;
  }

  imageSelectLeft.addEventListener('change', function () {
    imageContainers[0].src = imagepath + this.value;
    imageContainers[0].style.height = this.naturalHeight + 'px';
    imageContainers[0].style.width = this.naturalWidth + 'px';
  })

  imageSelectRight.addEventListener('change', function () {
    imageContainers[1].src = imagepath + this.value;
    imageContainers[1].style.height = this.naturalHeight + 'px';
    imageContainers[1].style.width = this.naturalWidth + 'px';
  })

  function imageCompare() {

    var x, i;
    /* Find all elements with an "overlay" class: */
    x = document.getElementsByClassName("img-comp-overlay");
    for (i = 0; i < x.length; i++) {
      /* Once for each "overlay" element:
      pass the "overlay" element as a parameter when executing the compareImages function: */
      compareImages(x[i]);
    }

    function compareImages(img) {
      var slider, img, clicked = 0, w, h;
      /* Get the width and height of the img element */
      w = img.offsetWidth;
      h = img.offsetHeight;
      /* Set the width of the img element to 50%: */
      img.style.width = (w / 2) + "px";
      /* Create slider: */
      slider = document.createElement("DIV");
      slider.setAttribute("class", "img-comp-slider");
      /* Insert slider */
      img.parentElement.insertBefore(slider, img);
      /* Position the slider in the middle: */
      // slider.style.top = (h / 2) - (slider.offsetHeight / 2) + "px";
      slider.style.left = (w / 2) - (slider.offsetWidth / 2) + "px";
      /* Execute a function when the mouse button is pressed: */
      slider.addEventListener("mousedown", slideReady);
      /* And another function when the mouse button is released: */
      window.addEventListener("mouseup", slideFinish);
      /* Or touched (for touch screens: */
      slider.addEventListener("touchstart", slideReady);
      /* And released (for touch screens: */
      window.addEventListener("touchend", slideFinish);

      function slideReady(e) {
        /* Prevent any other actions that may occur when moving over the image: */
        e.preventDefault();
        /* The slider is now clicked and ready to move: */
        clicked = 1;
        /* Execute a function when the slider is moved: */
        window.addEventListener("mousemove", slideMove);
        window.addEventListener("touchmove", slideMove);
      }

      function slideFinish() {
        /* The slider is no longer clicked: */
        clicked = 0;
      }

      function slideMove(e) {
        var pos;
        /* If the slider is no longer clicked, exit this function: */
        if (clicked == 0) return false;
        /* Get the cursor's x position: */
        pos = getCursorPos(e)
        /* Prevent the slider from being positioned outside the image: */
        if (pos < 0) pos = 0;
        if (pos > w) pos = w;
        /* Execute a function that will resize the overlay image according to the cursor: */
        slide(pos);
      }

      function getCursorPos(e) {
        var a, x = 0;
        e = (e.changedTouches) ? e.changedTouches[0] : e;
        /* Get the x positions of the image: */
        a = img.getBoundingClientRect();
        /* Calculate the cursor's x coordinate, relative to the image: */
        x = e.pageX - a.left;
        /* Consider any page scrolling: */
        x = x - window.pageXOffset;
        return x;
      }

      function slide(x) {
        /* Resize the image: */
        img.style.width = x + "px";
        /* Position the slider: */
        slider.style.left = img.offsetWidth - (slider.offsetWidth / 2) + "px";
      }
    }
  }
});