#!/bin/bash

# Function to convert mm:ss to total seconds
mmss_to_seconds() {
    local mm=$(echo $1 | cut -d':' -f1)
    local ss=$(echo $1 | cut -d':' -f2)
    # Remove leading zeros if any
    mm=$((10#$mm))
    ss=$((10#$ss))
    echo $(( mm * 60 + ss ))
}

# Function to convert total seconds to mm:ss
seconds_to_mmss() {
    local total_seconds=$1
    local mm=$(( total_seconds / 60 ))
    local ss=$(( total_seconds % 60 ))
    printf "%d:%02d\n" $mm $ss
}

# Start the loop
while true; do
    echo "Enter time in mm:ss or seconds (type 'exit' to quit):"
    read input

    # Check if the user wants to exit
    if [[ "$input" == "exit" ]]; then
        echo "Exiting..."
        break
    fi

    # Check the input format and decide the conversion
    if [[ $input =~ ^[0-9]+:[0-9]{2}$ ]]; then
        mmss_to_seconds $input
    elif [[ $input =~ ^[0-9]+$ ]]; then
        seconds_to_mmss $input
    else
        echo "Invalid input format. Use mm:ss or just seconds."
    fi
done
