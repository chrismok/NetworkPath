# NetworkPath
Imagine that there is a small network with a number of interconnected devices. Each connection has a latency parameter which is expressed in milliseconds. Your task is to write a program that will determine whether a signal can travel between two devices in a given amount of time or less.

## Implementation Guidelines:
1. The program should be executable from command line and accept one parameter - csv file path.
2. CSV file structure:

    **Format:**
    
        Device From, Device To, Latency (milliseconds)
    **Contents:**
    
        A,B,10
        A,C,20
        B,D,100
        C,D,30
        D,E,10
        E,F,1000

    **Interpretation:**
    
        A connects to B and it takes 10 milliseconds for signal to travel between two devices. A to C and it takes 20 milliseconds, B to D and it takes 100 milliseconds etc.

3. The program should then continually wait for user input. Format should be [Device From] [Device To] [Time] (e.g A F 1000 followed by ENTER key). If the signal can travel from A to F in 1000ms or less then output the signal path and total travel time in milliseconds otherwise print "Path not found". If user enters QUIT then terminate the program.

4. You are only required to output first path that meets the time constraint. It does not have to be the shortest path.

## Hints:

Think of the best data structure to accommodate devices and connections and write your code accordingly.

---
## Setup

```bash
$ composer install
```

## Usage

```bash
$ php main.php [CSV Data Filename]
```

## Example

```bash
$ php main.php ./Data/input_data.csv
```