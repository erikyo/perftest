#!/bin/bash

FORMATS=(mozjpg jxl avif webp jpg pjpg)
QUALITY=(05 25 40 50 60 70 75 80 82 85 87 90 92 95)

# man time to know more about builtins
# this is because i'm using zsh (you can comment the next line if not needed)
export TIMEFMT="%E real,%U user,%S sys,%P cpu"
TIMEFORMAT="%E real,%U user,%S sys,%P cpu"

echo "**imageBatchConvert 1.0**"
echo "to know: before to start you need to create a new folder with the original image"
read -p "Please, enter the path of this folder (with the trailing slash): " DESTPATH

read -p "The filename of the source image: " SOURCEIMAGE
read -p "...and the extension of that image: " SOURCEEXT

SOURCEFILE=$SOURCEIMAGE\.$SOURCEEXT

read -p "The filename of the image used for compare (leave empty to use the source image ): " COMPAREIMAGE
if [ -z $COMPAREIMAGE ];then
  COMPAREFILE=$SOURCEFILE
else
  read -p "...and the extension of that image: " COMPAREEXT
  COMPAREFILE=$COMPAREIMAGE\.$COMPAREEXT
fi

echo 'STARTAT: '`date +%s` &> ${DESTPATH}output.txt
echo 'SOURCEPATH: '${DESTPATH} &>> ${DESTPATH}output.txt
echo 'SOURCEFILE: '${SOURCEFILE} &>> ${DESTPATH}output.txt
echo 'REFERENCEFILE: '${COMPAREIMAGE}\.${COMPAREEXT} &>> ${DESTPATH}output.txt

identify -format '%[directory] %[basename] %[extension] %[compression] %[channels] %B %[width] %[height]' ${DESTPATH}${SOURCEFILE} &>> ${DESTPATH}output.txt

for f in "${FORMATS[@]}"
do
  for q in "${QUALITY[@]}"
  do
    if [ $f == "mozjpg" ];then
      EXT="jpg"
    elif [ $f == "jpg" ]; then
      EXT="jpg"
      OPTIONS="-strip -flatten"
    elif [ $f == "pjpg" ]; then
      EXT="jpg"
      OPTIONS="-strip -flatten -interlace plane"
    else
      EXT=$f
      OPTIONS="-strip"
    fi

    DESTFILE=$SOURCEIMAGE\-${f}\-${q}\.${EXT}

    printf "\nPROCESSING: "${DESTFILE}"\n" &>> ${DESTPATH}output.txt

    #the time check start here
    start=`date +%s.%N`

    echo -n "STATS:" &>> ${DESTPATH}output.txt

    if [ $f == "mozjpg" ];then
        # There's no way to add a background / flatten transparency in cjpeg so will be black
        # https://github.com/mozilla/mozjpeg/issues/264
        (time mozjpeg -quality $q -nojfif ${DESTPATH}${SOURCEFILE} > ${DESTPATH}${DESTFILE}) &>> ${DESTPATH}output.txt
      else
        (time convert -quality $q $OPTIONS ${DESTPATH}${SOURCEFILE} ${DESTPATH}${DESTFILE} &>> ${DESTPATH}output.txt 2>&1 ) &>> ${DESTPATH}output.txt
    fi

    #the time check ends here
    end=`date +%s.%N`
    ELAPSEDTIME=$( echo "$end - $start" | bc -l )
    echo ${DESTFILE}" created in "$ELAPSEDTIME

    echo -n "RESULT:" &>> ${DESTPATH}output.txt
    identify -format '%[directory] %[basename] %[extension] %[compression] %[channels] %B %[width] %[height]' ${DESTPATH}${DESTFILE} &>> ${DESTPATH}output.txt

    printf "\nTIMETOCOMPLETE:"$ELAPSEDTIME"\n" &>> ${DESTPATH}output.txt

    # here starts the image analysis
    convert ${DESTPATH}${DESTFILE} ${DESTPATH}test.tiff

    echo -n "SSIM:" &>> ${DESTPATH}output.txt
    compare -metric SSIM -verbose ${DESTPATH}$COMPAREFILE ${DESTPATH}test.tiff NULL &>> ${DESTPATH}output.txt 2>&1
    echo -n "DSSIM:" &>> ${DESTPATH}output.txt
    compare -metric DSSIM -fuzz 1% -verbose ${DESTPATH}$COMPAREFILE ${DESTPATH}test.tiff ${DESTPATH}$SOURCEIMAGE\-${f}\-${q}\-SSIM.png &>> ${DESTPATH}output.txt 2>&1
    echo -n "PSNR:" &>> ${DESTPATH}output.txt
    compare -metric PSNR -verbose ${DESTPATH}$COMPAREFILE ${DESTPATH}test.tiff NULL &>> ${DESTPATH}output.txt 2>&1
    echo -n "MAE:" &>> ${DESTPATH}output.txt
    compare -metric MAE -verbose ${DESTPATH}$COMPAREFILE ${DESTPATH}test.tiff NULL &>> ${DESTPATH}output.txt 2>&1

    printf ${DESTFILE}" Analysis completed\n"

  done
done

#housekeep things
rm ${DESTPATH}test.tiff

compare -metric SSIM -verbose ${DESTPATH}$SOURCEFILE ${DESTPATH}$COMPAREFILE ${DESTPATH}$SOURCEIMAGE\-SSIM.png &>> ${DESTPATH}output.txt 2>&1

#not needed anymore but could be useful for the future
#echo -n "\n\n\nProcessing:FILESIZES:" >> ${DESTPATH}output.txt
#echo stat -c "%n,%s" ${DESTPATH}/* >> ${DESTPATH}output.txt

echo 'ENDAT: '`date +%s` &>> ${DESTPATH}output.txt

echo 'Done!'

exit 0