/* global Chart, rawdata */
window.addEventListener('load', function () {

  const sizeChart = new Chart( document.getElementById('sizeChart'), {
    type: 'pie',
    data: {
      labels: [
        '',
        ''
      ],
      datasets: [{
        label: 'size Dataset',
        data: [50, 50],
        backgroundColor: [
          'rgb(255,183,0)',
          'rgb(54, 162, 235)'
        ],
        hoverOffset: 10
      }]
    },
    options: {
      layout: {
        padding: 20
      }
    },
    plugins: [{
      beforeInit: function(chart, options) {
        chart.legend.afterFit = function() {
          this.height = this.height + 50;
        };
      }
    }]
  });

  const timeChart = new Chart( document.getElementById('timeChart'), {
    type: 'pie',
    data: {
      labels: [
        '',
        ''
      ],
      datasets: [{
        label: 'size Dataset',
        data: [50, 50],
        backgroundColor: [
          'rgb(255,183,0)',
          'rgb(54, 162, 235)'
        ],
        hoverOffset: 4
      }]
    },
    options: {
      layout: {
        padding: 20
      }
    },
    plugins: [{
      beforeInit: function(chart, options) {
        chart.legend.afterFit = function() {
          this.height = this.height + 50;
        };
      }
    }]
  });

  const ssimChart = new Chart( document.getElementById('ssimChart'), {
    type: 'bar',
    data: {
      labels: [
        '',
        ''
      ],
      datasets: [{
        label: 'size Dataset',
        data: [50, 50],
        backgroundColor: [
          'rgb(54, 162, 235)',
          'rgb(255,183,0)'
        ]
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          display: false,
          labels: {
            generateLabels: (chart) => {
              const datasets = chart.data.datasets;
              return datasets[0].data.map((data, i) => ({
                text: `${chart.data.labels[i]} ${data}`,
                fillStyle: datasets[0].backgroundColor[i],
              }))
            }
          }
        }
      }
    },
  });

  function humanFileSize(size) {
    var i = Math.floor( Math.log(size) / Math.log(1024) );
    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
  }

  function parseIdentify(data) {
    var rawData = data.split(" ");
    return {
      directory: rawData[0],
      basename: rawData[1],
      extension: rawData[2],
      compression: rawData[3],
      channels: rawData[4],
      sizebyte: rawData[5],
      filesize: humanFileSize(rawData[5]),
      width: rawData[6],
      height: rawData[7],
    }
  }

  function extractImageAnalysis(raw, line) {
    result = {};
    if (raw[line+4].trim().startsWith("alpha:")){
      result.alpha = raw[line+4].trim().replace("alpha: ", "")
      result.all = raw[line+5].trim().replace("all: ", "")
    } else {
      result.all = raw[line+4].trim().replace("all: ", "")
    }
    result.red = raw[line+1].trim().replace("red: ", "")
    result.green = raw[line+2].trim().replace("green: ", "")
    result.blue = raw[line+3].trim().replace("blue: ", "")
    return result;
  }

  // return the formatted text below the image comparator
  function formatData(imgData) {

    if (!imgData) return "no data"

    var html = '';
    html += "<p>format: " + imgData.imageData.extension + (imgData.quality ? " (quality " + imgData.quality + "%)" : imgData.imageData.compression) + "</p>";
    html += "<p>size: " + imgData.imageData.width + "x" + imgData.imageData.height + " " + imgData.imageData.channels + "</p>";
    html += "<p>filesize: " + imgData.imageData.filesize + " (" + imgData.imageData.sizebyte + "b)</p>";
    if (imgData.encodetime) html += "<p>encodetime: " + imgData.encodetime + "</p>";
    if (imgData.ssim) html += `<p>ssim: ${imgData.ssim.all} (red: ${imgData.ssim.red}, green: ${imgData.ssim.green}, blue: ${imgData.ssim.blue})</p>`;
    if (imgData.dssim) html += `<p>dssim: ${imgData.dssim.all} (red: ${imgData.dssim.red}, green: ${imgData.dssim.green}, blue: ${imgData.dssim.blue})</p>`;
    if (imgData.psnr) html += `<p>psnr: ${imgData.psnr.all} (red: ${imgData.psnr.red}, green: ${imgData.psnr.green}, blue: ${imgData.psnr.blue})</p>`;
    if (imgData.mae) html += `<p>mae: ${imgData.mae.all} (red: ${imgData.mae.red}, green: ${imgData.mae.green}, blue: ${imgData.mae.blue})</p>`;
    if (imgData.stats) html += "<p>stats: " + imgData.stats + "</p>";
    return html;
  }

  // Used to Store the parsed images data
  var imagesData = [];

  var sourceImage = {
    path: rawdata['header'][0][1].replace("SOURCEPATH: ", ''),
    file: rawdata['header'][0][2].replace("SOURCEFILE: ", ''),
    reference: rawdata['header'][0][3].replace("REFERENCEFILE: ", ''),
    source: parseIdentify(rawdata['header'][0][4]),
  };

  imagesData[sourceImage['file']] = {
    imageData : sourceImage.source,
    filesize : sourceImage.source.filesize,
    encodetime: 0,
    name: sourceImage.file,
    type: "png",
    quality: "100",
    ssim: {all: 1, red: 1, green: 1, blue: 1},
    dssim: {all: 1, red: 1, green: 1, blue: 1},
    psnr: {all: 1, red: 1, green: 1, blue: 1},
    mae: {all: 1, red: 1, green: 1, blue: 1},
    stats: ['0.0 real', '0.0 user', '0.000 sys', '000.00 cpu']
  }


  // show the original image raw data
  document.getElementById('sourceData').innerHTML = `<h2>${sourceImage.source.basename}.${sourceImage.source.extension}</h2><p class='original-image-data'>${sourceImage.source.filesize} (compression: ${sourceImage.source.compression}) - width:${sourceImage.source.width}px height:${sourceImage.source.height}px - ${sourceImage.source.channels}</p>`;
  
  delete rawdata['header'];

  // for each image found parse and store the data we need for comparisons
  Object.values(rawdata).forEach(image => {

    imagesData[image[0]] = [];

    imagesData[image[0]].name = image[0].split(".")[0];

    imagesData[image[0]].type = imagesData[image[0]].name.split("-")[1];

    imagesData[image[0]].quality = imagesData[image[0]].name.split("-")[2];

    image.forEach((imgData, index) => {
      if (imgData.indexOf("STATS:") !== -1) {
        imagesData[image[0]].stats = imgData.replace("STATS:", '', imgData).split(",")
      } else if (imgData.indexOf("TIMETOCOMPLETE:") !== -1) {
        imagesData[image[0]].encodetime = imgData.replace("TIMETOCOMPLETE:", '', imgData)
      } else if (imgData.indexOf("RESULT:") !== -1) {
        imagesData[image[0]].imageData = parseIdentify(imgData.replace("RESULT:", ''))
      } else if (imgData.indexOf("  Channel distortion: SSIM") !== -1) {
        imagesData[image[0]].ssim = extractImageAnalysis(image, index);
      } else if (imgData.indexOf("  Channel distortion: DSSIM") !== -1) {
        imagesData[image[0]].dssim = extractImageAnalysis(image, index);
      } else if (imgData.indexOf("  Channel distortion: PSNR") !== -1) {
        imagesData[image[0]].psnr = extractImageAnalysis(image, index);
      } else if (imgData.indexOf("  Channel distortion: MAE") !== -1) {
        imagesData[image[0]].mae = extractImageAnalysis(image, index);
      }
    })
  })

  console.log(imagesData);

  // the UI script
  var imgCompContainer = document.querySelector('.img-comp-container');
  var imageSelectLeft = document.getElementById('selectImage1');
  var imageFilter = document.getElementById('selectImage1Filter');
  var imageFilterValue = document.getElementById('selectImage1FilterValue');
  var imageSelectRight = document.getElementById('selectImage2');
  var image1ContainerOverlay = document.getElementById('image1ContainerOverlay');

  var optionsGroup = '';
  // Fill the select with the available images
  Object.keys(imagesData).forEach(image => {
    optionsGroup += `<option value="${image}">${image}</option>`;
  })
  imageSelectRight.innerHTML = optionsGroup;
  imageSelectLeft.innerHTML = optionsGroup;

  // change the image of the given target and update the comparator data
  function switchImage(target, imageName) {

    // first change the target image
    target.src = imagepath + imageName;

    if (target === imageContainers[1]) {
      // the image on the left side
      imageSelectLeft.value = imageName;
      document.getElementById('image0Data').innerHTML = formatData(imagesData[imageName]);
      if (imagesData[imageName]) {
        sizeChart.data.datasets[0].data[1] = imagesData[imageName].imageData.sizebyte;
        sizeChart.data.labels[1] = imagesData[imageName].name;
        timeChart.data.datasets[0].data[1] = imagesData[imageName].encodetime;
        timeChart.data.labels[1] = imagesData[imageName].name;
        ssimChart.data.datasets[0].data[0] = imagesData[imageName].ssim.all;
        ssimChart.data.labels[0] = imagesData[imageName].name;
      }
    } else {
      // the image on the right side
      imageSelectRight.value = imageName;
      document.getElementById('image1Data').innerHTML = formatData(imagesData[imageName]);
      if (imagesData[imageName]) {
        sizeChart.data.datasets[0].data[0] = imagesData[imageName].imageData.sizebyte;
        sizeChart.data.labels[0] = imagesData[imageName].name;
        timeChart.data.datasets[0].data[0] = imagesData[imageName].encodetime;
        timeChart.data.labels[0] = imagesData[imageName].name;
        ssimChart.data.datasets[0].data[1] = imagesData[imageName].ssim.all;
        ssimChart.data.labels[1] = imagesData[imageName].name;
      }
    }
    sizeChart.update();
    timeChart.update();
    ssimChart.update();

    currentImages[target.id] = imagesData[imageName];

    // update the stats below the image
    if (Object.keys(currentImages).length > 1) updateLabels(currentImages);
  }

  // the image currentImages
  var imageContainers = document.querySelectorAll('.img-comp-img > img')
  var currentImages = {};

  // initial comparator setup
  if (imageContainers[0]) {

    // set the image src
    switchImage(imageContainers[0], sourceImage['file'] );
    switchImage(imageContainers[1], Object.keys(imagesData)[1] );

    // set the width and height of the comparator
    imageContainers[0].onload = function() {
      document.querySelector('.img-comparator').style.width = this.naturalWidth + 'px';
      imgCompContainer.style.height = this.naturalHeight + 'px';
      imgCompContainer.style.width = this.naturalWidth + 'px';
      imageContainers[0].width = this.naturalWidth;
      imageContainers[0].height = this.naturalHeight;
      imageContainers[1].width = this.naturalWidth;
      imageContainers[1].height = this.naturalHeight;
    }

    var pageloaded = false;
    // start the image compare after the second image load
    imageContainers[1].onload = function () {
      if (!pageloaded) imageCompare();
      pageloaded = true;
    }
  }

  function updateLabels(newImages) {
    var score = {}
    var image0size = parseInt(newImages.image0Container.imageData.sizebyte);
    var image1size = parseInt(newImages.image1Container.imageData.sizebyte);

    if (image1size > image0size) {
      score.size = {
        win: 1,
        winName: newImages.image0Container.name,
        difference: image1size - image0size,
        increase: (image1size / image0size * 100) - 100
      }
    } else {
      score.size = {
        win: 0,
        winName: newImages.image1Container.name,
        difference: image0size - image1size,
        increase: (image0size / image1size * 100) - 100
      }
    }

    var image0time = parseFloat(newImages.image0Container.encodetime);
    var image1time = parseFloat(newImages.image1Container.encodetime);

    if (image1time < image0time) {
      score.time = {
        win: 1,
        winName: newImages.image1Container.name,
        difference: image0time - image1time,
        increase: (image1time / image0time * 100) - 100
      }
    } else {
      score.time = {
        win: 0,
        winName: newImages.image0Container.name,
        difference: image1time - image0time,
        increase: (image0time / image1time * 100) - 100
      }
    }

    var image0ssim = parseFloat(newImages.image0Container.ssim.all);
    var image1ssim = parseFloat(newImages.image1Container.ssim.all);

    if (image1ssim > image0ssim) {
      score.ssim = {
        win: 1,
        winName: newImages.image1Container.name,
        difference: (image0ssim - image1ssim) * -1,
        increase: ((image0ssim / image1ssim * 100) - 100) * -1
      }
    } else {
      score.ssim = {
        win: 0,
        winName: newImages.image0Container.name,
        difference: (image1ssim - image0ssim) * -1,
        increase: ((image1ssim / image0ssim * 100) - 100) * -1
      }
    }

    document.querySelector('.chart-size-wrap .chart-values').classList.add(score.size.win === 0 ? "win0" : "win1");
    document.querySelector('.chart-size-wrap .chart-values').innerHTML = "<h2>-"+ Math.round(score.size.increase) +"%</h2>" +
      "<h4>"+score.size.winName+"</h4>" +
      "<p class='diff'> weight " + humanFileSize(score.size.difference) + " less</p>" +
      "<p><span class='label data'>" + humanFileSize(newImages.image1Container.imageData.sizebyte) + "</span>" +
      "<span class='sign'>" + (score.size.win === 0 ? "<" : ">") + "</span>" +
      "<span class='label data data-two'>" + humanFileSize(newImages.image0Container.imageData.sizebyte) + "</span></p>";

    document.querySelector('.chart-time-wrap .chart-values').classList.add(score.time.win === 0 ? "win0" : "win1");
    document.querySelector('.chart-time-wrap .chart-values').innerHTML = "<h2>" + Math.round(score.time.increase) + " %</h2>" +
      "<h4>"+score.time.winName+"</h4>" +
      "<p class='diff'>encoding is faster by "+parseFloat(score.time.difference).toFixed(3)+"</p>" +
      "<p><span class='label data'>" + parseFloat(newImages.image1Container.encodetime).toFixed(5) + " sec.</span>" +
      "<span class='sign'>" + (score.time.win === 0 ? ">" : "<") + "</span>" +
      "<span class='label data data-two'>" + parseFloat(newImages.image0Container.encodetime).toFixed(5) + " sec.</span></p>";

    document.querySelector('.chart-ssim-wrap .chart-values').classList.add(score.ssim.win === 0 ? "win0" : "win1");
    document.querySelector('.chart-ssim-wrap .chart-values').innerHTML = "<h2>+" + parseFloat(score.ssim.increase).toFixed(1) + "%</h2>" +
      "<h4>"+score.ssim.winName+"</h4>" +
      "<p class='diff'> Quality analysis (SSIM) score better by " + parseFloat(score.ssim.difference).toFixed(5) + "</p>" +
      "<p><span class='label data'>" + parseFloat(newImages.image1Container.ssim.all).toFixed(5) + "</span>" +
      "<span class='sign'>" + (score.ssim.win === 0 ? "<" : ">") + "</span>" +
      "<span class='label data data-two'>" + parseFloat(newImages.image0Container.ssim.all).toFixed(5) + "</span></p>";

  }

  imageSelectLeft.addEventListener('change', function () {
    switchImage( imageContainers[1], this.value);
    image1ContainerOverlay.src = '';
    imageFilter.classList.remove('active');
  })

  imageFilter.addEventListener('click', function () {
    var overlayImage = imageSelectLeft.value.split('.')[0] + "-SSIM.png"
    imageFilter.classList.toggle('active');
    image1ContainerOverlay.src = image1ContainerOverlay.src === window.location.href ? imagepath + overlayImage : '';
  })

  imageFilterValue.addEventListener('change', function () {
    image1ContainerOverlay.style.opacity = this.value * .01;
  })

  imageSelectRight.addEventListener('change', function () {
    switchImage( imageContainers[0], this.value);
  })

  var topScores = {
    "targets" : [".85",".90",".91",".92",".93",".94",".95",".955",".96",".965",".97",".975",".98",".985",".99"],
  }

  var lineChartData = {};
  Object.values(imagesData).forEach((image) => {

      if (image.type !== 'png') {
        if (!lineChartData[image.type]) {
          lineChartData[image.type] = {sizebyte : {},encodetime : {},ssim : {},dssim : {},psnr : {},mae: {}}
        }
        lineChartData[image.type].sizebyte[parseInt(image.quality)] = image.imageData.sizebyte
        lineChartData[image.type].encodetime[parseInt(image.quality)] = image.encodetime
        lineChartData[image.type].ssim[parseInt(image.quality)] = image.ssim ? image.ssim.all : 0
        lineChartData[image.type].dssim[parseInt(image.quality)] = image.dssim ? image.dssim.all : 0
        lineChartData[image.type].psnr[parseInt(image.quality)] = image.psnr ? image.psnr.all : 0
        lineChartData[image.type].mae[parseInt(image.quality)] = image.mae ? image.mae.all.split(" ")[0] : 0

        topScores.targets.forEach(score => {
          if ( parseFloat(image.ssim.all) > parseFloat(score) && (typeof topScores[score] == "undefined" || topScores[score].sizebyte > parseInt(image.imageData.sizebyte)) ) {
            topScores[score] = {
              name: image.name,
              sizebyte: parseInt(image.imageData.sizebyte),
              ssim: image.ssim.all,
              time: image.encodetime
            }
          }
        })
      }
  })


  // BEST RESULTS table
  var resultsTable = '<h3>Best results table</h3><p>the best format/quality combination for a given SSIM score</p>' +
    '<table class="table-best-results"><tr><th>Target SSIM</th><th>Best SSIM</th><th>Winner</th><th>Size</th><th>Time to encode</th></tr>'

  topScores.targets.forEach( score => {
    if (topScores[score]) resultsTable += '<tr><td>'+score+'</td><td>'+topScores[score].ssim+'</td><td>'+topScores[score].name+'</td><td>'+humanFileSize(topScores[score].sizebyte)+'</td><td>'+topScores[score].time+'</td></tr>'
  });

  resultsTable += '</table>'

  const chartsWrap = document.getElementById('img-comparator-table')
  chartsWrap.innerHTML = resultsTable;


  // Results Charts
  var colors = [ '#ff5722','#3f51b5', '#03a9f4', '#4caf50','#ffeb3b', '#ff9800' ]
  var sizeFormat = [];
  var timeFormat = [];
  var ssimFormat = [];
  var dssimFormat = [];
  var psnrFormat = [];
  var maeFormat = [];

  Object.entries(lineChartData).forEach((format, index) => {
    sizeFormat.push({
      label: format[0],
      data: Object.values(format[1].sizebyte),
      lineTension: 0.4,
      fill: false,
      borderColor: colors[index]
    })
    timeFormat.push({
      label: format[0],
      data: Object.values(format[1].encodetime),
      lineTension: 0.2,
      fill: false,
      borderColor: colors[index]
    })
    ssimFormat.push({
      label: format[0],
      data: Object.values(format[1].ssim),
      lineTension: 0.2,
      fill: false,
      borderColor: colors[index]
    })
    dssimFormat.push({
      label: format[0],
      data: Object.values(format[1].dssim),
      lineTension: 0.2,
      fill: false,
      borderColor: colors[index]
    })
    psnrFormat.push({
      label: format[0],
      data: Object.values(format[1].psnr),
      lineTension: 0.2,
      fill: false,
      borderColor: colors[index]
    })
    maeFormat.push({
      label: format[0],
      data: Object.values(format[1].mae),
      lineTension: 0.2,
      fill: false,
      borderColor: colors[index]
    })
  })


  var sizeLineChart = new Chart(document.getElementById('sizeLineChart'), {
    type: 'line',
    data: {
      labels: [5,25,40,50,60,70,75,80,82,85,87,90,92,95],
      datasets: sizeFormat
    },
    options: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80
        }
      },
      scales: {
        x: {
          max: 95,
          min: 5,
          type: 'linear'
        }
      }
    }
  });

  new Chart(document.getElementById('timeLineChart'), {
    type: 'line',
    data: {
      labels: [5,25,40,50,60,70,75,80,82,85,87,90,92,95],
      datasets: timeFormat
    },
    options: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80
        }
      },
      scales: {
        x: {
          max: 95,
          min: 5,
          type: 'linear'
        }
      }
    }
  });

  new Chart(document.getElementById('ssimLineChart'), {
    type: 'line',
    data: {
      labels: [5,25,40,50,60,70,75,80,82,85,87,90,92,95],
      datasets: ssimFormat
    },
    options: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80
        }
      },
      scales: {
        x: {
          max: 95,
          min: 5,
          type: 'linear'
        }
      }
    }
  });

  new Chart(document.getElementById('dssimLineChart'), {
    type: 'line',
    data: {
      labels: [5,25,40,50,60,70,75,80,82,85,87,90,92,95],
      datasets: dssimFormat
    },
    options: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80
        }
      },
      scales: {
        x: {
          max: 95,
          min: 5,
          type: 'linear'
        }
      }
    }
  });


  new Chart(document.getElementById('psnrLineChart'), {
    type: 'line',
    data: {
      labels: [5,25,40,50,60,70,75,80,82,85,87,90,92,95],
      datasets: psnrFormat
    },
    options: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80
        }
      },
      scales: {
        x: {
          max: 95,
          min: 5,
          type: 'linear'
        }
      }
    }
  });


  new Chart(document.getElementById('maeLineChart'), {
    type: 'line',
    data: {
      labels: [5,25,40,50,60,70,75,80,82,85,87,90,92,95],
      datasets: maeFormat
    },
    options: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80
        }
      },
      scales: {
        x: {
          max: 95,
          min: 5,
          type: 'linear'
        }
      }
    }
  });
  // an edited version of image_comparison @ w3schools
  // https://www.w3schools.com/howto/howto_js_image_comparison.asp
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
      img.style.width = "50%";
      /* Create slider: */
      slider = document.createElement("DIV");
      slider.setAttribute("class", "img-comp-slider");
      /* Insert slider */
      img.parentElement.insertBefore(slider, img);
      /* Position the slider in the middle: */
      // slider.style.top = (h / 2) - (slider.offsetHeight / 2) + "px";
      slider.style.left = "50%";
      slider.style.margin.left = "-1px";
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
        x = x - window.scrollX;
        return x;
      }

      function pixelToPercent(pixel, totalPixels) {
        return (pixel * 100) / totalPixels;
      }

      function slide(x) {
        /* Resize the image: */
        img.style.width = pixelToPercent(x, w) + "%";
        /* Position the slider: */
        slider.style.left = img.offsetWidth - (slider.offsetWidth / 2) + "px";
      }
    }
  }
});
