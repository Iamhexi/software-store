#!/bin/bash
filename="tests/all.sqltests"
buffer_file=buffer.tmp
test_count=0
failed_tests=0

while IFS= read -r line1; do
    # Read the next two lines
    if [ -n "$line1" ]; then
        IFS= read -r line2
        IFS= read -r line3
        IFS= read -r line4

        # Check if all three lines are non-empty
        if [ -n "$line2" ] && [ -n "$line3" ] && [ -n "$line4" ]; then
            test_count=$(($test_count+1))
            echo "Running test: $line1 as $line4"

            mariadb \
            --defaults-file="users/$line4.cnf" \
            -D software_store \
            --raw --batch -e "$line2" --tee="$buffer_file" > $buffer_file 2>&1
            
            db_answer=$(cat $buffer_file)
            red_fg=`tput setaf 1`
            yellow_fg=`tput setaf 3`
            green_fg=`tput setaf 2`
            reset=`tput sgr0`


            lowercase_variable="${line2,,}"

            if [[ $lowercase_variable == *select* ]]; then
                echo "${yellow_fg}Verify results manually: $db_answer${reset}"
            elif [ "$db_answer" = "Logging to file 'buffer.tmp'" ] && [ "$line3" = "Success" ]; then
                echo "${green_fg}Test passed${reset}"
            elif [ "$db_answer" != "Logging to file 'buffer.tmp'" ] && [ "$line3" = "Failure" ]; then
                echo "${green_fg}Test passed${reset}"
            else
                echo "${red_fg}Test failed"
                echo "$db_answer${reset}"
                echo "Expected result: $line3"
                failed_tests=$(($failed_tests+1))
            fi

            rm $buffer_file
            
            printf "\n"

        fi
    fi
done < "$filename"

echo "RESULT: $failed_tests out of $test_count tests have failed."