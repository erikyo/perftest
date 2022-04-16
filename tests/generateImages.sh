#!/bin/bash
echo "imageBatchConvert"
echo "First: You need to prepare a folder with an image inside named original image"
read -p "Please, enter the path of this folder (with the trailing slash): " DESTPATH

read -p "The filename of the source image: " SOURCEIMAGE
read -p "...and the extension of that image: " SOURCEEXT

FORMATS=(mozjpg avif webp jpg pjpg)
QUALITY=(05 25 50 60 70 75 80 85 90 95)
SOURCEFILE=$SOURCEIMAGE\.$SOURCEEXT

which time;

# man time to know more about builtins
echo 'Start at: '`date +%s` >> ${DESTPATH}output.txt
echo 'Source: '${DESTPATH}${SOURCEFILE} &>> ${DESTPATH}output.txt

#this is because i'm using zsh
export TIMEFMT="%E real,%U user,%S sys,%P cpu"

identify ${DESTPATH}${SOURCEFILE} &>> ${DESTPATH}output.txt

for f in "${FORMATS[@]}"
do
  for q in "${QUALITY[@]}"
  do
    if [ $f == "mozjpg" ];then
      EXT="jpg"
    elif [ $f == "pjpg" ]; then
      EXT="jpg"
      OPTIONS="-strip -interlace plane"
    else
      EXT=$f
      OPTIONS="-strip"
    fi

    DESTFILE=$SOURCEIMAGE\-${f}\-${q}\.${EXT}

    printf "\nProcessing: "${DESTFILE}"\n" &>> ${DESTPATH}output.txt

    start=`date +%s.%N`

    if [ $f == "mozjpg" ];then
        (time mozjpeg -quality $q -nojfif ${DESTPATH}${SOURCEFILE} > ${DESTPATH}${DESTFILE}) &>> ${DESTPATH}output.txt
      else
        (time convert -quality $q $OPTIONS ${DESTPATH}${SOURCEFILE} ${DESTPATH}${DESTFILE} &>> ${DESTPATH}output.txt 2>&1 ) &>> ${DESTPATH}output.txt
    fi

    end=`date +%s.%N`
    ELAPSEDTIME=$( echo "$end - $start" | bc -l )
    printf ${DESTFILE}" created in "$ELAPSEDTIME"\n"

    identify ${DESTPATH}${DESTFILE} &>> ${DESTPATH}output.txt

    printf "TIMETOCOMPLETE:"$ELAPSEDTIME"\n" &>> ${DESTPATH}output.txt

    compare -metric SSIM -verbose ${DESTPATH}$SOURCEFILE ${DESTPATH}$DESTFILE ${DESTPATH}${DESTFILE}\-SSIM.png &>> ${DESTPATH}output.txt 2>&1

    printf ${DESTFILE}" SSIM analysis completed\n"

  done
done

compare -metric SSIM -verbose ${DESTPATH}$SOURCEFILE ${DESTPATH}$SOURCEFILE ${DESTPATH}${SOURCEFILE}\-SSIM.png &>> ${DESTPATH}output.txt 2>&1

echo "\n\nFILESIZES: " && stat -c "%n,%s" ${DESTPATH}/* >> ${DESTPATH}output.txt

echo 'Done!'

exit 0