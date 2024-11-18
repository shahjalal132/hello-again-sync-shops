i=0

while [ $i -le 100 ]
do
    i=$(($i+1))  # Increment the value of i by 1.

    # Print a message indicating the current shop number.
    echo "Adding shop no: $i ..."

    # Make an HTTP request using curl to the specified URL with a timestamp parameter.
    curl -X GET -H 'Cache-Control: no-store' "http://helloagain.test/wp-json/hello-again/v1/sync-shops?$(date +%s)" > /dev/null 2>&1

    # Print a message indicating that the ith shop has been added.
    echo "$i th shop Added"
done