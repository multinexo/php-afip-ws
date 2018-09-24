#!/bin/sh

## Copyright (C) 1997-2017 Reyesoft <info@reyesoft.com>.
## This file is part of Multinexo. Multinexo can not be copied and/or
## distributed without the express permission of Reyesoft

#use find-double-spaces.sh folder/

HASERROR=false
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

for line in $(find $1 -type f -iname '*.php'); do
    ## remove comments like   //
    ## remove comments like   /* */
    ## remove comments like   /* \n \n */
    ## search double spaces

    sed -E 's/ *\/\/.*//g' $line \
        | sed -E 's/ *\/\*.*\*\///g' \
        | sed '/\/\*/,/\*\// {s/.*\/.*//p; d}' \
        | grep -P '[^ ]+(  )+.*'
    RC=$?
    if [ $RC -eq 0 ]; then
        echo "${YELLOW}^^^ File with double space:${NC} $line\n"
        HASERROR=true
    fi
done
if [ "$HASERROR" = true ]; then
  exit 1
fi
